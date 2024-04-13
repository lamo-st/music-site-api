<?php

namespace App\Traits;

trait APIResponse
{
    public static function createAPIResponse($state, $contnet = null, $message = null,  $errors = null)
    {
        $result = [];

        $result['success'] = $state;
        $result['data'] = $contnet;
        $result['message'] = $message;
        $result['errors'] = $errors;

        return $result;
    }
}
