<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function moduleView(string $view, array $data = []): View
    {
        /** @var view-string $viewName */
        $viewName = $view;

        return view($viewName, $data);
    }
}
