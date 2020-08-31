<?php

return [

    'templates' => [

        'form' => 'form::form',
        'panel' => 'form::panel',
        'actions' => 'form::actions',

        'fields' => [
            'text' => 'form::fields.text',
            'search' => 'form::fields.search',
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

    // Important: Date formats in java.text.SimpleDateFormat
    // Reference: https://docs.oracle.com/javase/7/docs/api/java/text/SimpleDateFormat.html
    'fields' => [
        'dateTime' => [
            'formatDates' => true,
            'formatClient' => 'YYYY-MM-DD HH:mm:ss',
            'formatServer' => 'YYYY-MM-DD HH:mm:ss',
        ],
        'date' => [
            'formatDates' => true,
            'formatClient' => 'YYYY-MM-DD',
            'formatServer' => 'YYYY-MM-DD',
        ]
    ],

];
