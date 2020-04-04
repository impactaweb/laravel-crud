<?php

/**
 * Configurações do ProSeleta - Listagem
 */
return [

    /**
     * View padrão a ser utilizada:
     * <String>
     */
    'view' => 'listing::listing',

    /**
     * Paginação: quantidade padrão de itens por página:
     * <Int>
     */
    'defaultPerPage' => 10,

    /**
     * Ações padrão da listagem:
     */
    'defaultActions' => [
        [
            'name' => 'novo',
            'label' => 'Novo',
            'method' => 'GET',
            'icon' => 'far fa-plus-square'
        ],
        [
            'name' => 'editar',
            'label' => 'Editar',
            'method' => 'GET',
            'icon' => 'far fa-edit'
        ],
        [
            'name' => 'destroy',
            'label' => 'Excluir',
            'method' => 'DELETE',
            'icon' => 'far fa-trash-alt'
        ],
    ],









    /**
     * Paginação: quantidade padrão de itens por página:
     * <Boolean>
     */
    'pagination' => true,
    
    
    /**
     * Paginação: quantidade máxima de itens por página: 
     * <Int>
     */
    'defaultPerPageMaximum' => 500,

    /**
     * Ações padrão da listagem:
     */
    'defaultActionInsert' => [ 'url' => '/create/', 'method' => 'get'],
    'defaultActionEdit'   => [ 'url' => '/edit/', 'method' => 'post'],
    'defaultActionDelete' => [ 'url' => '/destroy/', 'method' => 'post'],

    /**
     * Busca avançada
     * campos que serão ignorados por padrão na hora da busca:
     */
    'defaultFieldsRemovedFromAdvancedSearch' => [
        'flag_excluido',
    ],    
    
    /**
     * Textos para a flag:
     */
    'defaultFlagTexts' => ['off', 'on'],

    /**
     * Texto padrão para relacionamentos vazios:
     */
    'defaultEmptyRelationValue' => '-',
];

