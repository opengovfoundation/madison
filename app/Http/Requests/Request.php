<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

abstract class Request extends FormRequest
{
    /**
     * Get the proper failed validation response for the request.
     *
     * @return \Illuminate\Http\Response
     */
    public function response(array $errors)
    {
        if ($this->ajax() || $this->wantsJson()) {
            // format the error messages so the client will display them
            // automatically, like the growlMessage method on the controller
            // class does (whose functionality should probably be broken out
            // into a injectable service class that we could use here instead)
            return new JsonResponse(['messages' => array_map(function ($msg) {
                return ['text' => $msg, 'severity' => 'error'];
            }, array_flatten($errors))], 422);
        }

        return parent::response($errors);
    }
}
