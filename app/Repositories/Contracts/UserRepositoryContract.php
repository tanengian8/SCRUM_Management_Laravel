<?php

namespace App\Repositories\Contracts;


interface UserRepositoryContract
{
    public function login($request);
    public function register($request);
    public function logout($request);
    public function checkUserExist($request);
}