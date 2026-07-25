<?php

namespace App\Core\Debug;

use App\Http\Request;

class RequestCollector implements Collector
{
    public function getName(): string
    {
        return 'request';
    }

    public function render(): string
    {
        $method = Request::method();
        $url = Request::url();

        return "<span>{$method} {$url}</span>";
    }
}
