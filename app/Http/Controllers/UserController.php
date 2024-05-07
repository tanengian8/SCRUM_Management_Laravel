<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        return $this->userService->login($request);
    }

    public function register(Request $request)
    {
        return $this->userService->register($request);
    }

    public function logout(Request $request)
    {
        return $this->userService->logout($request);
    }

    public function checkUserExist(Request $request)
    {
        return $this->userService->checkUserExist($request);
    }
}
