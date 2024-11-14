<?php

namespace App\Modules\User\Services\Interfaces;

interface UserServiceInterface
{
    public function getAllUsers(array $data);
    public function getUserById($id);
    public function createUser(array $data);
    public function updateUser($id, array $data);
    public function deleteUser($id);
    public function login(array $credentials);

    public function getUserByToken();
}
