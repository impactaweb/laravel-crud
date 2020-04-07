<?php

namespace Impactaweb\Crud\Listing\Mask;

use Carbon\Carbon;

class Dates {

    public static function dm($s) {
        return Carbon::parse($s)->format('d/m');
    }

    public static function dmY($s) {
        return Carbon::parse($s)->format('d/m/Y');
    }

    public static function dmYHis($s) {
        return Carbon::parse($s)->format('d/m/Y H:i:s');
    }

    public static function dmYHi($s) {
        return Carbon::parse($s)->format('d/m/Y H:i');
    }


}