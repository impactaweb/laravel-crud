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
     * Para inserir na tabela o campo ID automaticamente:
     */
    private $autoId;

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
     * checkEmpty habilita uma verificação da própria listagem
     * antes de tentar aplicar um callback em um campo(pois se for null pode dar erros)
     */
    private $checkEmpty = true;

    /**
     * View que será usada na listagem.
     * Pode ser alterada sob demanda.
     * @var String
     */
    public $view;

    /**
     * Nome do arquivo de configuracoes
     * @var String
     */
    public $configFile = 'listing';

    /**
     * Ações
     */
    public $actions;

    /**
     * Requisições ajax recebem respostas em JSON
     * Esta opção diz para a classe ignorar esta verificação:
     */
    public $checkAjaxRequest = true;

    /**
     * Campo que será utilizado como ID do checkbox:
     */
    public $checkboxIdField;

    /**
     * Opções de operadores da busca avançada:
     */
    private $operators = [
        'like'     => 'is like',
        'not like' => 'not like',
        '='        => 'equal',
        '!='       => 'different',
        '<'        => 'less than',
        '<='       => 'less or equal than',
        '>'        => 'greater than',
        '>='       => 'greater or equal than',
        'in'       => 'in',
    ];

    /**
     * Campos que serão utilizados na busca avançada:
     * Se não for setado, pegará todos da source:
     * @var Array
     */
    private $advancedSearchFields = []; 

    /**
     * Campos que são para serem removidos da busca avançada
     */
    public $removeAdvancedSearchFields;

    /**
     * Flag para interromper listagem:
     * Caso tenhamos alguma ação da lista (ex: alterar um flag_status, etc...) devemos interromper 
     * a execução da listagem e retornar:
     */
    private $requestActionResponse;

    /**
     * Textos padrão para a flag:
     */
    private $flagTexts;

    public function __construct(string $index = null, bool $autoId = true, string $actions = null) {

        if (!is_null($index)) {
            $this->setIndex($index);
        }

        $this->autoId = $autoId;

        # verifica se há qtd de itens por página alterados pelo usuário da sessão:
        $this->checkQuantityPerPage();

        # set configs
        $this->setDefaultValues();

        if ($actions === false) {
            $this->setActions([]);
        }

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
        $this->view       = config($this->configFile . '.view');
        $this->pagination = config($this->configFile . '.pagination');
        $this->perPage    = config($this->configFile . '.defaultPerPage');
        $this->perPageMax = config($this->configFile . '.defaultPerPageMaximum');
        $this->removeAdvancedSearchFields = config($this->configFile . '.defaultFieldsRemovedFromAdvancedSearch');
        $this->flagTexts  = config($this->configFile . '.defaultFlagTexts') ? config($this->configFile . '.defaultFlagTexts') : ['Não', 'Sim'];

        # Ações padrão:
        $this->setActions([
            'editar' => config($this->configFile . '.defaultActionEdit'),
            'inserir' => config($this->configFile . '.defaultActionInsert'),
            'excluir' => config($this->configFile . '.defaultActionDelete'),
        ]);
    }

    /**
     * Seta o índice da tabela:
     */
    public function setIndex(string $index)
    {
        $this->index = $index;
    }

    /**
     * Seta o formAction da tabela:
     */
    public function setFormAction(string $formAction)
    {
        $this->formAction = $formAction;
    }

    /**
     * Seta o método do form
     */
    public function setFormMethod(string $formMethod)
    {
        $this->formMethod = $formMethod;
    }

    /**
     * Obter o field index
     */
    public function getIndex()
    {
        return in_array('ID', $this->columns) ? $this->columns[0] : null;
    }

    /**
     * Seta o formAction da tabela:
     * @param Boolean
     */
    public function setCheckEmpty(bool $check)
    {
        $this->checkEmpty = $check;
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
     * Seta a quantidade de registros por página para a paginação
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
     * @param $columns Array
     * @param $checkbox Bool - se é para inserir o input checkbox de envio de formulário
     */
    public function setColumns(array $columns = [], $showCheckbox = true)
    {
        if (empty($columns)) {
            return null;
        }

        # se existe a coluna "__checkbox" voltamos com erro, pois ela é reservada da lib:
        if (in_array('__checkbox', $columns)) {
            throw new \Exception('Listagem: a coluna __checkbox é reservada e não pode ser inserida em setColumns');
        }

        # se é para inserir o ID automaticamente
        if ($this->autoId && !empty($this->index)) {
            $columns = [$this->index => [ 'label' => 'ID']] + $columns;
        }

        # se houver ação de formulário inserimos checkboxes:
        if ( !empty($this->getActions()) && ($showCheckbox) ) {
            $checkbox['__checkbox'] = [
                'label' => ''
            ];
            $columns = $checkbox + $columns;
        }
        foreach ($columns as $field => $params) {

            # se é checkbox, inserimos o checkbox de controle:
            if ($field === '__checkbox') {
                $this->columns[$field]['column_link'] = '<input type="checkbox" name="checkbox-listing" onchange="handleAllChecked()" />';
                continue;
            }

            if (is_array($params)) {
                # preenchemos a variável principal com os parâmetros enviados
                $this->columns[$field] = $params;
            } else {
                /**
                 * caso não tenha sido enviado nemhum parâmetro(nem label), aqui fazemos a
                 * transformação. Basta verificar se está no formato
                 * [0 => 'field_tabela'] ao invés de ['field_tabela' => 'Campo Tabela']
                 * */
                $field = $params;
                $params = $this->label($params);
                # considera-se que enviou só o label mesmo:
                $this->columns[$field] = ['label' => $params];
            }

            # link para ordenação:
            $this->columns[$field]['column_link'] = $this->makeOrderLink($field);

            /**
             * Agora verificamos se a coluna é um relacionamento
             * ex: candidato.nome
             */ 
            $f = explode('.', $field);
            if (is_array($f)) {
                // remove o primeiro item pois já será o campo original:
                $this->columns[$field]['original'] = $f[0];
                unset($f[0]);
                foreach ($f as $related) {
                    if (!empty($related)) {
                        $this->columns[$field]['relations'][] = $related;
                    }
                }
            }
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

        # inicia o builder caso tenha passado a Model pura, para não dar exception ao tentar montar query numa string:
        $this->source = (is_string($this->source)) ? $this->source::query() : $this->source;

        # Se há um post para a listagem realizar alguma ação após o setSource():
        $response = $this->requestActions();
        if ($response) {
            $this->requestActionResponse = $response;
        }
    }

    /**
     * Prepara a query para a listagem dos data:
     */
    public function prepararQuery()
    {
        if (is_null($this->source)) {
            return null;
        }

        $source = $this->source;
        # verificamos se tem ordenação:
        if (request()->get('ord') !== null) {
            $source = $source->orderBy(request()->get('ord'), (request()->get('dir') !== null) ? request()->get('dir') : 'ASC');
        }

        # Search?
        if (request()->get('q') !== null) {
            foreach ($this->columns as $field => $params) {
                # ignoramos as colunas reservadas:
                if ($field == '__checkbox') {
                    continue;
                }
                $source = $source->orWhere($field, 'LIKE', '%'.request()->get('q').'%');
            }
        }

        # Advanced Search
        $source = $this->advancedSearch($source);

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
            foreach ($this->data as $key => $registro) {
                # passamos pelas columns de cada registro para verificar customizações:
                foreach ($this->columns as $field => $params) {

                    # se é checkbox, embutimos no item correspondente:
                    if ($field == '__checkbox') {
                        $checkboxIdField = $this->checkboxIdField == null ? $this->index : $this->checkboxIdField;
                        # se existe um id de checkbox personalizado:
                        // usaremos o índice informado como "id_"
                        if ( !empty($this->data[$key]->{$checkboxIdField})) {
                            $this->data[$key]->$field = '<input type="checkbox" name="item[]" class="listing-checkboxes" value="'.$this->data[$key]->{$checkboxIdField}.'" />';
                        }
                        continue;
                    }

                    # valor padrão em caso de relacionamento vazio:
                    $valor = isset($params['emptyRelationValue']) ? $params['emptyRelationValue'] : config('listing.defaultEmptyRelationValue');

                    # Joga o valor correto considerando relacionamento:
                    if (isset($params['relations'])) {
                        if ( !is_null($this->data[$key]->{$params['original']}) ) {
                            $valor = $this->data[$key]->{$params['original']};
                            foreach ($params['relations'] as $related) {
                                $valor = $valor->{$related};
                            }
                        }
                        $this->data[$key]->$field = $valor;
                    }

                    // Verifica callbacks e etc:
                    foreach ($params as $item => $valor) {

                        switch ($item) {
                            case 'callback':
                                if ($this->checkEmpty &&  !empty($registro->$field)) {
                                    $this->data[$key]->$field = $params['callback']($registro->$field);
                                }
                            break;

                            case 'type':
                                switch($params['type']) {
                                    case 'flag':
                                        # certificando-se que não irá dar erro
                                        $this->data[$key]->$field = is_null($this->data[$key]->$field) ? '0' : $this->data[$key]->$field;
                                        if (isset($params['flagTexts'])) {
                                            $this->flagTexts = $params['flagTexts'];
                                        }
                                        $class = ($this->data[$key]->$field > 0)  ? 'listing_on' : 'listing_off';
                                        $this->data[$key]->$field = '<a href="javascript:void(0)" 
                                                                    class="listing_flag '.$class.'" 
                                                                    data-id="'.$this->data[$key]->{$this->index}.'" 
                                                                    data-field="'.$field.'"
                                                                    data-flag-text-on="'.$this->flagTexts[1].'"
                                                                    data-flag-text-off="'.$this->flagTexts[0].'"
                                                                    data-current-flag="'.$this->data[$key]->$field.'"
                                                                    >'.$this->flagTexts[$this->data[$key]->$field].'</a>';
                                    break;
                                }
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
        if (!empty($this->requestActionResponse)) {
            abort(200, json_encode($this->requestActionResponse));
        }
        $this->checkIndice();
        $this->prepararQuery();
        $this->prepararDados();
        $this->checkAdvancedSearchFields();

        $resposta = [
            'actions'    => $this->actions,
            'columns'    => $this->columns,
            'data'       => $this->data,
            'pagination' => $this->pagination,
            'perPage'    => $this->perPage,
            'advancedSearchFields' => $this->advancedSearchFields,
            'advancedSearchOperators' => $this->operators,
        ];

        # é para tratar diferente uma requisição ajax? Retornaremos só o json
        if ($this->checkAjaxRequest && request()->ajax()) {
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
        return '<a href="'.$url.'" class="text-body" >'.$this->columns[$field]['label'].'</a>';
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

    /**
     * Ações da listagem (editar, excluir, etc...)
     * @param Array
     */
    public function setActions($actions = null)
    {
        return $this->actions = $actions;
    }

    /**
     * get Actions
     */
    public function getActions()
    {
        return $this->actions;
    }

    public function setCheckAjaxRequest(bool $bool)
    {
        return $this->checkAjaxRequest = $bool;
    }

    public function setCheckboxIdField(String $field) {
        return $this->checkboxIdField = $field;
    }

    /**
     * Adicionar campo específicos para a busca avançada:
     */
    public function setAdvancedSearchFields(Array $fields)
    {
        return $this->advancedSearchFields = $fields;
    }

    /**
     * Informa campos específicos para serem ignorados na busca avancada
     * @param Array campos a serem ignorados
     * @param Boolean se é para ignorar os campos já existentes.
     */
    public function removeAdvancedSearchFields(Array $fields, Bool $ignoreDefaults = false)
    {
        if ($ignoreDefaults) {
            return $this->removeAdvancedSearchFields = $fields;
        }
        return $this->removeAdvancedSearchFields += $fields;        
    }

    /**
     * Busca avançada:
     * @param Object <a source da Model/Table que terá a busca alterada>
     * Também utiliza o request para pegar os campos, peradores e termos da busca
     */
    public function advancedSearch($source)
    {
        $fields = request()->get('fields');
        $operators = request()->get('operators');
        $terms = request()->get('terms');    
        
        if (!is_array($fields) || empty($fields)) {
            return $source;
        }
        
        foreach ($fields as $index => $field) {
            
            if (empty($terms[$index])) 
                continue;

            switch($operators[$index]) {
                case '=' :
                case '!=':
                case '<' :
                case '>' :
                case '<=':
                case '>=':
                    $source = $source->where($field, $operators[$index], $terms[$index]);
                    break;
                case 'like':
                case 'not like':
                    $source = $source->where($field, $operators[$index], '%'.$terms[$index].'%');
                    break;
                case 'in':
                    # termos separados por vírula:
                    $values = explode(',', $terms[$index]);
                    $source = $source->whereIn($field, $values);
                    break;
            }
        }
        return $source;
    }

    /**
     * Check if the advanced search fields are set:
     */
    public function checkAdvancedSearchFields()
    {
        # capo reservado da lib a ser ignorado:
        $this->removeAdvancedSearchFields[] = '__checkbox';

        if (empty($this->advancedSearchFields)) {
            // if its empty, we put all the source fields as default:
            if ( is_object($this->data) ) {
                $arr = [];
                foreach ($this->data->first() as $index => $value) {
                    # remove the reserved fields:
                    if ( in_array($index, $this->removeAdvancedSearchFields) ) {
                        continue;
                    }
                    # 
                    $arr[] = $index;
                }
                return $this->advancedSearchFields = $arr;
            }
        }
    }

    /**
     * Verifica e há ações para a listagem realizar, como
     * alterar uma flag, etc...
     * @return array
     */
    public function requestActions()
    {
        if (request()->get('listingAction') !== null) {

            switch(request()->get('listingAction')) {
                case 'flag':
                    return $this->setSourceFlag(request()->get('id'), request()->get('field'), request()->get('currentFlag'));
                break;

                default: return ['erro' => 'nenhuma ação informada'];
            }
        }
        return false;
    }

    /**
     * Set Flag
     * Altera o status(flag) de uma determinada source:
     * @param $id do registro
     * @param $field nome do campo flag
     * @param $flagAtual Número ou boolean
     */
    public function setSourceFlag($id, $field, $currentFlag)
    {
        if ($this->source
            ->where($this->index, $id)
            ->update([$field => !$currentFlag])) {
                return ['success' => true];
        }
        return ['success' => false];
    }

}
