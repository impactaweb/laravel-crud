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

if (!function_exists('getQueryString')) {
    /**
     * Search for specific parameter inside a querystring
     * @param $request
     * @param array $ignoreList
     * @return string
     */
    function getQueryString($request, $ignoreList = [])
    {
        parse_str($request->getQueryString(), $queryArray);
        foreach ($ignoreList as $ignoreItem) {
            if (array_key_exists($ignoreItem, $queryArray)) {
                unset($queryArray[$ignoreItem]);
            }
        }
        return http_build_query($queryArray);
    }
}

if (!function_exists('getParameterFromRequest')) {
    /**
     * Search defined parameter in request
     * @param string $parameter
     * @return bool|string
     */
    function getParameterFromRequest($request, string $parameter)
    {
        // Search 'parameter' in request
        if ($request->has($parameter)) {
            return urldecode($request->get($parameter));
        }

        // Search 'parameter' in json
        if ($request->json()->has($parameter)) {
            return urldecode($request->json()->get($parameter));
        }

        return false;
    }

}


if (!function_exists('clearUrl')) {
    /**
     * Clear querystring form URL
     * @param $url
     * @return string
     */
    function clearUrl($url)
    {

        if (strpos($url, "?") !== false) {
            return substr($url, 0, strpos($url, "?"));
        }
        return $url;
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
