<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configs do Crud
    |--------------------------------------------------------------------------
    |
    */

    'field_class' => [
        "text" => Impactaweb\Crud\Form\Fields\TextField::class,
        "password" => Impactaweb\Crud\Form\Fields\PasswordField::class,
        "number" => Impactaweb\Crud\Form\Fields\NumberField::class,
        "select" => Impactaweb\Crud\Form\Fields\SelectField::class,
        "textarea" => Impactaweb\Crud\Form\Fields\TextAreaField::class,
        "file" => Impactaweb\Crud\Form\Fields\FileField::class,
        "file_async" => Impactaweb\Crud\Form\Fields\FileAsyncField::class,
        "multiselect" => Impactaweb\Crud\Form\Fields\MultiSelectField::class,
        "flag" => Impactaweb\Crud\Form\Fields\FlagField::class,
        "html" => Impactaweb\Crud\Form\Fields\HtmlField::class,
        "show" => Impactaweb\Crud\Form\Fields\ShowField::class,
        "hidden" => Impactaweb\Crud\Form\Fields\HiddenField::class,
        "datetime" => Impactaweb\Crud\Form\Fields\DateTimeField::class,
        "date" => Impactaweb\Crud\Form\Fields\DateField::class,
        "time" => Impactaweb\Crud\Form\Fields\TimeField::class,
        "rtf" => Impactaweb\Crud\Form\Fields\RtfField::class,
        'multiselectgroup' => Impactaweb\Crud\Form\Fields\MultiSelectGroupField::class
    ],

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

    // Important: Date formats in java.text.SimpleDateFormat
    // Reference: https://docs.oracle.com/javase/7/docs/api/java/text/SimpleDateFormat.html
    'fields' => [
        'dateTime' => [
            'formatDates' => true,
            'formatClient' => 'DD/MM/YYYY HH:mm:ss',
            'formatServer' => 'YYYY-MM-DD HH:mm:ss',
        ],
        'date' => [
            'formatDates' => true,
            'formatClient' => 'DD/MM/YYYY',
            'formatServer' => 'YYYY-MM-DD',
        ]
    ],

];
