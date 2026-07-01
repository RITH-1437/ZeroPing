<?php

namespace App\Controllers;

class UserController
{

    public function show(string $id): void
    {
        echo "User ID : " . $id;
    }
}