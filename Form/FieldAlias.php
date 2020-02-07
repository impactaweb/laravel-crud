<?php

namespace Impactaweb\Crud\Form;

/**
 * Class AliasCampos
 */
class FieldAlias
{

    /**
     * Alias to build form fields
     */
    const fields = [
        "text" => Fields\TextField::class,
        "password" => Fields\PasswordField::class,
        "number" => Fields\NumberField::class,
        "select" => Fields\SelectField::class,
        "textarea" => Fields\TextAreaField::class,
        "file" => Fields\FileField::class,
        "multiselect" => Fields\MultiSelectField::class,
        "flag" => Fields\FlagField::class,
        "html" => Fields\HtmlField::class,
        "show" => Fields\ShowField::class,
        "hidden" => Fields\HiddenField::class,
        "datetime" => Fields\DateTimeField::class,
        "time" => Fields\TimeField::class,
        "rtf" => Fields\RtfField::class,
        'multiselectgroup' => Fields\MultiSelectGroupField::class
    ];

}
