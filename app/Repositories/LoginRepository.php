<?php
/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/17
 * Time: 下午10:24
 */

namespace App\Repositories;


use App\User;

class LoginRepository
{
    public function createUser($username, $password, $email = 'example@example.com')
    {
        if (!empty($password)) {
            return User::query()->create([
                'name' => $username,
                'password' => User::encrypt($password),
                'email' => $email
            ]);
        }
        throw new \Exception('密码不能为空');
    }
}