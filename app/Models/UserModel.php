<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'id_user';
    protected $allowedFields = [
        'username',
        'password', // Now will store hashed password
        'nama',
        'email',
        'telepon',
        'peran'
    ];


    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
    // Method to verify hashed password
    public function verifyPassword($username, $password)
    {
        $user = $this->where('username', $username)->first();
        
        if ($user) {
            // Use password_verify to check hashed password
            return password_verify($password, $user['password']);
        }
        
        return false;
    }

    // Method to hash password before saving
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}