<?php


namespace Impactaweb\Crud\Listing;


class Listing2
{
    public $primaryKey = 'id_cliente';

    // Lista de colunas e relacionamento que podem ter texto buscado
    // Por padrão é buscado as colunas searchable dentro da model
    public $searchable = [
        'email',
        'nome',
        'cpf',
        'cidade.cidade',
        'cidade.estado'
    ];

    // associação do elemento com o campo que é ordenavel
    // utilizar para quando um campo é customizado, indicar qual campo no banco
    // deverá ser utilizado para ordenação
    public $orderable = [
        'id',
        'status' => 'status.id'
    ];

    // Filters
    // Associa um campo da busca avançada a um dropdown de filtros
    // Isso substitui o input do usuário
    public $filters = [
        'id' => 'id_cliente',
        'Status' => [
            'column' => 'status.id', // coluna que fará a busca (busca por querystring)
            'operator' => 'exact', // o operador padrao é 'exact' / não mostra o dropdown de operadores
            'options' => [ // se nao for um array valido ou nao setado, cria o input para digitacao
                '1' => 'Foo',
                '2' => 'Bar',
                '3' => 'Xtpo'
            ]
        ],
        'Deferido' => [
            'column' => 'solicitacao.id',
            'operator' => 'in', // neste caso será possível setar o in como operador / não mostra o dropdown de operadores
            'options' => [
                '1,2,3' => 'Sim',
                '4,5' => 'Não'
            ]
        ],
        'Data de Deferimento' => [
            'column' => 'data_deferimento',
            'options' => [
                '2000-00-00' => 'Hoje',
                '2000-00-00' => 'Semana passada',
                '2000-00-00' => '15 dias atrás',
                '2000-00-00' => 'Um mês atrás',
            ]
        ]
    ];

    // Aqui é fornecido um array já montado
    public $data = [
        1 => [
            'id_cliente' => 1,
            'cliente' => 'Bar',
            'status' => [
                'id_status' => 1,
                'status' => '<span class="badge badge-success">Ativo</span>'
            ]
        ]
    ];

    // Operadores padrões
    public $operators = [
        'contains' => 'Contém',
        'not_contains' => 'Não contém',
        'lte' => 'Menor ou igual',
        'lt' => 'Menor',
        'gte' => 'Maior ou igual',
        'gt' => 'Maior que',
        'exact' => 'Igual'
    ];

    // Botões de ação do formulário
    public $actions = [
        'editar' => [
            'name' => "editar",
            'label' => "Editar",
            'method' => "GET",
            'url' => "/admin/clientes/{id}/editar?ids={ids}",
            'icon' => "far fa-edit",
            'resourceCustomVerbs' => [
                "create" => "criar",
                "edit" => "editar",
            ],
            'message' => null
        ]
    ];

    // Ordenação atual
    public $currentOrderby = [
        'id_cliente',
        'asc'
    ];

    // Mostra checkbox
    public $showCheckbox = true;

}

/////////////////////
/////////////////////
/////////////////////
// Usage examples //
/////////////////////
/////////////////////
/////////////////////

$data = [
    1 => [
        'id' => 1,
        'name' => 'Jon Doe',
        'code' => 123,
        'date' => '2000-00-00',
        'status_badge' => '<span class="badge badge-info">Xtpo</span>',
        'status' => [
            'id' => 4,
            'name' => 'Xtpo'
        ]
    ],
    2 => [
        'id' => 2,
        'name' => 'Mark Zuckenberg',
        'date' => '2000-00-00',
        'code' => '123',
        'status_badge' => '<span class="badge badge-sucess">Foo</span>',
        'status' => [
            'id' => 3,
            'name' => 'Foo'
        ]
    ]
];

$orderable = [
    'code',
    'name',
    'status_badge' => 'status.id',
];

$searchable = [
    'name', 'code', 'status.name'
];

$filters = [
    'id' => 'id',
    'code' => 'code',
    'Status' => [
        'column' => 'status.id', // coluna que fará a busca (busca por querystring)
        'operator' => 'exact', // o operador padrao é 'exact' / não mostra o dropdown de operadores
        'options' => [ // se nao for um array valido ou nao setado, cria o input para digitacao
            '1' => 'Foo',
            '2' => 'Bar',
            '3' => 'Xtpo'
        ]
    ],
    'Ativo' => [
        'column' => 'status.id', // coluna que fará a busca (busca por querystring)
        'operator' => 'in', // o operador padrao é 'exact' / não mostra o dropdown de operadores
        'options' => [ // se nao for um array valido ou nao setado, cria o input para digitacao
            '1,2,3' => 'Yes',
            '4,5' => 'No'
        ]
    ],
    'Data de Cadastro' => [
        'column' => 'data', // coluna que fará a busca (busca por querystring)
        'operator' => ['lt', 'lte', 'gte', 'gt', 'exact'],
        'options' => [ // se nao for um array valido ou nao setado, cria o input para digitacao
            '2000-00-00' => 'Hoje',
            '2000-00-00' => 'Esta semana',
            '2000-00-00' => 'Este mês',
            '2000-00-00' => 'Este Ano'
        ]
    ],
];

$l = new Listing2('id');
$l->setData($data);
$l->setOrderable($orderable);
$l->setSearchable($searchable);
$l->setFilters($filters);
$l->addAction([...]);
$l->render();

