<?php

namespace Impactaweb\Crud\Listing\Mask;

use Carbon\Carbon;

class Dates {

    public static function dm($s) {
        return $s ? Carbon::parse($s)->format('d/m') : "s";
    }

    public static function dmY($s) {
        return $s ? Carbon::parse($s)->format('d/m/Y') : "";
    }

    public static function dmYHis($s) {
        return $s ? Carbon::parse($s)->format('d/m/Y H:i:s') : "";
    }

    public static function dmYHi($s) {
        return $s ? Carbon::parse($s)->format('d/m/Y H:i') : "";
    }


}