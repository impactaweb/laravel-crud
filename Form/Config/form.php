<?php

return [

    'templates' => [

        'form' => 'form::form',
        'panel' => 'form::panel',
        'actions' => 'form::actions',

        'fields' => [
            'text' => 'form::fields.text',
            'password' => 'form::fields.password',
            'number' => 'form::fields.number',
            'select' => 'form::fields.select',
            'textarea' => 'form::fields.textarea',
            'file' => 'form::fields.file',
            'file_async' => 'form::fields.file_async',
            'multiselect' => 'form::fields.multiselect',
            'flag' => 'form::fields.flag',
            'html' => 'form::fields.html',
            'show' => 'form::fields.show',
            'hidden' => 'form::fields.hidden',
            'id' => 'form::fields.hidden',
            'datetime' => 'form::fields.datetime',
            'date' => 'form::fields.date',
            'time' => 'form::fields.time',
            'rtf' => 'form::fields.rtf',
            'multiselectgroup' => 'form::fields.multiselectgroup'
        ],

    ],

    'fields' => [
        'dateTime' => [
            'formatDates' => true,
            'formatClient' => 'd/m/Y H:i:s',
            'formatServer' => 'Y-m-d H:i:s',
        ],
        'date' => [
            'formatDates' => true,
            'formatClient' => 'd/m/Y',
            'formatServer' => 'Y-m-d',
        ]
    ],

];
