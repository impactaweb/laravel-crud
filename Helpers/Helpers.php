<?php

# Funções genericas para uso em qualquer rotina


/**
 * ###############
 * ### STRINGS ###
 * ###############
 */


if (!function_exists('unbackSlash')) {
    /**
     * Remove / do inicio e do fim de uma string
     * @param string $string
     * @return string
     */
    function unbackSlash(string $string): string
    {
        $string = ($string[0] === "/" ? mb_substr($string, 1) : $string);
        $lastIndex = mb_strlen($string) - 1;
        $string = mb_strrpos($string, '/') === $lastIndex
            ? mb_strrchr($string, '/', true)
            : $string;
        return $string;
    }
}

if (!function_exists('listingRelationLabel')) {
    /**
     * Trata a string do label de busca avançada em caso de relacionamentos,
     * removendo os pontos e deixando apenas o útimo item
     * @param $label
     * @return mixed|string $string
     */
    function listingRelationLabel($label)
    {
        $array = explode('.', $label);
        if (count($array) > 1) {
            $label = end($array);
        }
        return $label;
    }
}
