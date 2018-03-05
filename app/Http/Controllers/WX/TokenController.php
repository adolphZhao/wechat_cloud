<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


class TokenController extends Controller
{
    public function token(Request $request)
    {
        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $echostr = $request->input('echostr');
        $nonce = $request->input('nonce');
        $token = 'c4ca4238a0b923820d';

        $list = [
            $token,
            $timestamp,
            $nonce
        ];
        sort($list);
        $signatureSHA1 = sha1(implode($list));

        if ($signature == $signatureSHA1) {
            echo $echostr;
        }
        file_put_contents(storage_path('wx.log'), $signatureSHA1 . '===' . $signature, 8);
        echo '';
        exit;

    }
}