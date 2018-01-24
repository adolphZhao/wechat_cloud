<?php
namespace App\Http\Controllers;

use App\Services\LoginService;
use \Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(Request $request)
    {
        $name = $request->input('username', '');
        $password = $request->input('password', '');
        $token = $this->loginService->login($name, $password);
        return definedResponse($token);
    }

    public function detail(Request $request)
    {
        $token = $request->input('token', '');
        $token = $this->loginService->detail($token);
        return definedResponse($token);
    }
}