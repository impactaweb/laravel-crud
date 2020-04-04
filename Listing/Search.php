<?php

namespace Impactaweb\Crud\Listing;

class Search {

    public $fields;
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

    public function __construct()
    {
        
    }

}
