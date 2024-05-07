<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryContract;

class UserService
{
    
    private $userRepository;
    
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function login($request)
    {
        return $this->userRepository->login($request);
    }

    public function register($request)
    {
        return $this->userRepository->register($request);
    }

    public function logout($request)
    {
        return $this->userRepository->logout($request);
    }

    public function checkUserExist($request)
    {
        return $this->userRepository->checkUserExist($request);
    }

    
}