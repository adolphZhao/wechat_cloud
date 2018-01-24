<?php

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CrosMiddleware
{
    private $headers;

    public function handle(Request $request, \Closure $next)
    {
        $this->headers = [
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers'),
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => 1728000
        ];

        if ($request->isMethod('options')) {
            return $this->setCorsHeaders(new Response('OK', 200));
        }
        $response = $next($request);
        $methodVariable = array($response, 'header');

        if (is_callable($methodVariable, false, $callable_name)) {
            return $this->setCorsHeaders($response);
        }
        return $response;
    }

    public function setCorsHeaders($response)
    {
        foreach ($this->headers as $key => $value) {
            $response->header($key, $value);
        }

        $response->header('Access-Control-Allow-Origin', '*');

        return $response;
    }
}