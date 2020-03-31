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
        $lastIndex = mb_strlen($string) - 1;        $string = mb_strrpos($string, '/') === $lastIndex
            ? mb_strrchr($string, '/', true)
            : $string;        return $string;
    }
}

if (!function_exists('startsWith')) {
    /**
     * Verifica se a string começa com o padão informado
     * @param $string
     * @param $startString
     * @return bool
     */
    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}

if(!function_exists('endsWith')) {
    /**
     * Verifica se a string termina com o padão informado
     * @param $string
     * @param $endString
     * @return bool
     */
    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}

if(!function_exists('listingRelationLabel')) {
    /**
     * Trata a string do label de busca avançada em caso de relacionamentos, removendo os pontos e deixando apenas o útimo item
     * @param $label
     * @return $string
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
