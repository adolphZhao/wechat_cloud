<?php

if (!function_exists('definedResponse')) {
    function definedResponse($content, $code = 200, $state = true)
    {
        if ($state && $code == 200) {
            return \Illuminate\Http\Response::create(json_encode([
                'success' => $state,
                'errors' => [],
                'data' => $content
            ]), $code);
        }
        return \Illuminate\Http\Response::create(json_encode([
            'success' => $state,
            'errors' => $content,
            'data' => []
        ]), $code);
    }
}