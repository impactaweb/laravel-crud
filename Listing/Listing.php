<?php

namespace Impactaweb\Crud\Listing;

class Listing {
    /**
     * Colunas a serem listadas na tabela.
     * @var Array
     */
    public $columns;

    /**
     * Source: seria a fonte de data(Model, DB::table('tabela')) que será usada nas queries
     * caso a listagem faça este gerenciamento
     */
    private $source;

    /**
     * Dados(registros) que serão listados:
     * @var Object (collection)
     */
    public $data;

    /**
     * Campo da tabela que será mostrado na column ID
     * @var String
     */
    private $index;

    /**
     * @var Boolean ativa/desativa a paginação na query. Default: true <Boolean>
     */
    public $pagination;

    /**
     * @var Integer - quantos itens teremos por página. Default 10 <int>
     */
    public $perPage;

    /**
     * @var Integer - a quantity máxima permitida de itens por página
     */
    private $perPageMax;

    /**
     * Ações da listagem (editar, excluir, [customizados]...)
     * @var Array
     */
    // private $acoes;

    /**
     * View que será usada na listagem.
     * Pode ser alterada sob demanda.
     * @var String
     */
    public $view;

    /**
     * Nome do arquivo de configuracoes
     */
    public $configFile = 'listing';

    public function __construct(string $index = null) {

        if (!is_null($index)) {
            $this->setIndex($index);
        }

        # verifica se há qtd de itens por página alterados pelo usuário da sessão:
        $this->checkQuantityPerPage();

        # set configs
        $this->setDefaultValues();

        if (empty($this->view)) {
            throw new \Exception('Config file not found.');
        }
    }

    /**
     * Setamos os valores padrão de configuração da listagem,
     * são itens buscados do config que o usuário pode customizar:
     */
    public function setDefaultValues()
    {
        $this->view = config($this->configFile . '.view');
        $this->pagination = config($this->configFile . '.pagination');
        $this->perPage = config($this->configFile . '.defaultPerPage');
        $this->perPageMax = config($this->configFile . '.defaultPerPageMaximum');
    }

    /**
     * Seta o índice da tabela:
     */
    public function setIndex(string $index)
    {
        $this->index = $index;
    }

    /**
     * Obter o field index
     */
    public function getIndex()
    {
        return in_array('ID', $this->columns) ? $this->columns[0] : null;
    }

    /**
     * Seta a paginação
     * @param Boolean
     */
    public function setPagination(bool $pagination)
    {
        return $this->pagination = $pagination;
    }

    /**
     * Seta a quantity de registros por página para a paginação
     * @param Integer
     */
    public function setPerPage(int $quantity)
    {
        return $this->perPage = $quantity;
    }

    /**
     * Colunas a serem listadas na tabela.
     * Aceita field => label
     * ou field => array('parâmetros')
     * ex: 
     * $columns => [
     *   'data_nasc' => 'Data de Nascimento',
     *   'ativo'     => ['label' => 'Ativo', 'callback' => (alguma function personalizada))]
     * ]
     */
    public function setColumns(array $columns = []) 
    {
        # se existe o field índice, será adicionado em $columns[]:
        if (!is_null($this->index)) {
            $columns = [$this->index => 'ID'] + $columns;
        }
        
        if (empty($columns)) {
            return null;
        }

        foreach ($columns as $field => $params) {
            if (is_array($params)) {
                # preenchemos a variável principal com os parâmetros enviados
                $this->columns[$field] = $params;
            } else {
                /**
                 * caso não tenha sido enviado nemhum parâmetro(nem label), aqui fazemos a 
                 * transformação. Basta verificar se está no formato 
                 * [0 => 'field_tabela'] ao invés de ['field_tabela' => 'Campo Tabela']
                 * */
                if (is_int($field)) {
                    $field = $params;
                    $params = $this->label($params);
                }
                # considera-se que enviou só o label mesmo:
                $this->columns[$field] = ['label' => $params];
            }

            # link para ordenação:
            $this->columns[$field]['column_link'] = $this->makeOrderLink($field);
        }
    }
    
    /**
     * Source: recebe a variável que contém a Model a ser utilizada na busca dos registros.
     * No caso a query deve vir sem o get(), pois ele indica a finalização do processo de 
     * busca (não pode haver ordenação ou where's após o get(), por exemplo...), 
     * então o get() é feito neste método ao final de tudo + paginação.
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Prepara a query para a listagem dos data:
     */
    public function prepararQuery()
    {
        if (is_null($this->source)) {
            return null;
        }

        # inicia o builder caso tenha passado a Model pura, para não dar exception ao tentar montar query numa string:
        $source = (is_string($this->source)) ? $this->source::query() : $this->source;

        # verificamos se tem ordenação:
        if (request()->get('ord') !== null) {
            $source = $source->orderBy(request()->get('ord'), (request()->get('dir') !== null) ? request()->get('dir') : 'ASC');
        }

        # Search?
        if (request()->get('q') !== null) {
            foreach ($this->columns as $field => $params) {
                $source = $source->orWhere($field, 'LIKE', '%'.request()->get('q').'%');
            }
        }

        # Pagination:
        if ($this->pagination) {
            # verificamos se a paginação foi alterada pelo usuário:
            if (request()->get('pp') !== null && ((int)request()->get('pp') > 0 && (int)request()->get('pp') < $this->perPageMax)) {
                $this->perPage = request()->get('pp');
                # salva na sessão para reaproveitar durante a navegação do usuário:
                $this->saveQuantityPerPage($this->perPage);
            }
            $source = $source->paginate($this->perPage);
        } else {
            $source = $source->get();
        }

        $this->setDados($source);

        return;
    }

    /**
     * Setamos os data que serão exibidos na listagem:
     */
    public function setDados($data)
    {
        $this->data = $data;
    }

    /**
     * Montagem dos data antes de serem exibidos.
     * Percorremos todos os $this->data e fazemos as ações necessárias, 
     * como customizar o field, callbacks...
     */
    public function prepararDados()
    {
        # prepara os data com callbacks e etc...
        if (!empty($this->data)) {
            # passamos por todos os registros:
            foreach ($this->data as $index => $registro) {
                # passamos pelas columns de cada registro para verificar customizações:
                foreach ($this->columns as $field => $params) {
                    foreach ($params as $item => $valor) {
                        switch ($item) {
                            case 'callback':
                                $this->data[$index]->$field = $params['callback']($registro->$field);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Verifica se o índice realmente existe em $data:
     */
    public function checkIndice()
    {
        if (!empty($this->index) && is_object($this->data) && !isset($this->data->first()->{$this->index})) {
            throw new \Exception('Listagem: o índice informado ('.$this->index.') não existe na coleção de data.');
        }
    }

    /**
     * Renderiza a view de listagem:
     * @param $view customizada(opcional)
     */
    public function render($view = '')
    {
        $this->checkIndice();
        $this->prepararQuery();
        $this->prepararDados();

        $resposta = [
            'columns'   => $this->columns,
            'data'     => $this->data,
            'pagination' => $this->pagination,
            'perPage' => $this->perPage,
        ];

        # é uma requisição ajax? Retornaremos só o json
        if (request()->ajax()) {
            return response()->json($resposta);
        }

        # template padrão do pacote, que pode ser customizado
        return view((empty($view)? $this->view : $view), $resposta);
    }

    /**
     * Montar o link de ordenação de cada column.
     * O objetivo é inserir os parâmetros ordem(ord) e direção(dir) ao link para que, ao ser clicado, volte
     * para a mesma página com a nova ordem/direção e mantendo parâmetros que possam existir anteriormente(como busca, etc...)
     * @param $field String <nome original do field da tabela>
     */
    public function makeOrderLink($field)
    {
        # se já existe ordem, verificamos a direção para mudá-la
        $dir = request()->get('dir') ?? 'ASC';
        $ord = request()->get('ord');
        if ($ord && $ord == $field) {
            $dir = ($dir == 'ASC') ? 'DESC' : 'ASC';
        }

        # removemos da query string a "ord" e "dir" antigas:
        $requestQuery = request()->query();
        unset($requestQuery['ord'], $requestQuery['dir']);

        # remonta a query string:
        $query_string = http_build_query($requestQuery);
        $separador = (empty($query_string)) ? '' : '&';
        $url = request()->url().'?'.$query_string . $separador . 'ord=' . $field . '&dir=' . $dir;
        return '<a href="'.$url.'" >'.$this->columns[$field]['label'].'</a>';
    }

    /**
     * Transformar o nome do field em um "label" mais amigável
     * @param String $field
     */
    public function label($field)
    {
        $field = str_replace('_', ' ', $field);
        return ucfirst($field);
    }

    /**
     * Ações da listagem (editar, excluir, etc...)
     * @param Array
     */
    // public function setAcoes($acoes)
    // {
    //     if (is_array($acoes)) {
    //         return $this->acoes = $acoes;
    //     }        
    //     throw new \Exception('Listagem: parâmetro inválido para o setAcoes()');
    // }

    /**
     * Manter na sessão a configuração "perPage" caso seja alterada pelo usuário:
     */
    public function saveQuantityPerPage($qtd)
    {
        return request()->session()->put('perPage', $qtd);
    }

    /**
     * Verifica se temos a quantity perPage customizada:
     */
    public function checkQuantityPerPage()
    {
        if (request()->session()->has('perPage')) {
            $this->perPage = request()->session()->get('perPage');
        }
    }

}