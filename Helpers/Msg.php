<?php


namespace Impactaweb\Crud\Helpers;

use Illuminate\Support\Facades\Session;

class Msg
{
    static function success($message)
    {
        return Session::flash('success', $message);
    }

    static function info($message)
    {
        return Session::flash('info', $message);
    }

    static function danger($message)
    {
        return Session::flash('danger', $message);
    }

    static function warning($message)
    {
        return Session::flash('warning', $message);
    }
}
