<?php

namespace Impactaweb\Crud\Listing\Traits;

use Illuminate\Support\Facades\Storage;

trait Util {

    public static function fillUrlParameters(string $url, $data = []): string
    {
        if (getType($data) == "object") {
            if (!method_exists($data, 'toArray')) {
                $data = (array)$data;
            }
            $data = $data->toArray();
        }

        preg_match_all("|{[^}]+}+|U", $url, $parameters);
        if (!($parameters[0] && count($parameters[0]) > 0)) {
            return $url;
        }

        foreach ($parameters[0] as $parameter) {
            $parameter2 = str_replace(['{','}'], '', $parameter);

            if (substr($parameter2, 0, 10) == 'storageUrl') {
                if (substr($parameter2, 10, 1) == '.') {
                    $disk = substr($parameter2, 11, strlen($parameter2));
                    $baseUrl = Storage::disk($disk)->url("");
                } else {
                    $baseUrl = Storage::url("");
                }
                $url = str_replace($parameter, rtrim($baseUrl ?? "", '/'), $url);
                continue;
            }

            if (substr($parameter2, 0, 4) == 'url.') {
                $parameter2 = substr($parameter2, 4, strlen($parameter2));
                $routeParams = request()->route()->parameters();
                $url = str_replace($parameter, $routeParams[$parameter2] ?? "", $url);
                continue;
            }
            
            if (array_key_exists($parameter2, $data)) {
                $url = str_replace($parameter, $data[$parameter2] ?? "", $url);
            }

        }

        return $url;
    }

}
