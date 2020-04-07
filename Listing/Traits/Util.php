<?php

namespace Impactaweb\Crud\Listing\Traits;

trait Util {

    public static function fillUrlParameters(string $url, $data = []): string
    {
        preg_match_all("|{[^}]+}+|U", $url, $parameters);
        if (!($parameters[0] && count($parameters[0]) > 0)) {
            return $url;
        }

        foreach ($parameters[0] as $parameter) {
            $parameter2 = str_replace(['{','}'], '', $parameter);

            if (substr($parameter2, 0, 4) == 'url.') {
                $parameter2 = substr($parameter2, 4, strlen($parameter2));
                $routeParams = request()->route()->parameters();
                if (isset($routeParams[$parameter2])) {
                    $url = str_replace($parameter, $routeParams[$parameter2], $url);
                    continue;
                }
            }

            if (isset($data->$parameter2)) {
                $url = str_replace($parameter, $data->$parameter2, $url);
                continue;
            }
        }

        return $url;
    }

}
