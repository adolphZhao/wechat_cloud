<?php

namespace App\Services;

use App\User;

class LoginService
{
    public function login($name, $password)
    {
        /**
         * @var User $user ;
         */
        $user = User::query()->where('name', trim($name))->first();

        if ($user && User::encrypt($password) == $user->getAuthPassword()) {
            return $user->token();
        }
        throw new \Exception('用户名或者密码错误', 422);
    }

    public function detail($token)
    {
        /**
         * @var User $user ;
         */
        $user = User::query()->where('token', trim($token))->first();

        if ($user) {
            return $user;
        }
        throw new \Exception('无效的token', 401);
    }
}