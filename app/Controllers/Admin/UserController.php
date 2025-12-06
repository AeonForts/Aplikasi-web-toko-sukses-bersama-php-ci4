<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Display list of users
    public function list()
    {
        $users = $this->userModel->findAll();
        
        return view('pages/admin/users/list', [
            'users' => $users
        ]);
    }

    public function save()
    {
        // Basic validation - just check if fields are not empty
        $validationRules = [
            'username' => 'required',
            'password' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'telepon' => 'required',
            'peran' => 'required'
        ];
    
        // Check validation
        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'error' => $this->validator->getErrors(),
                'message' => 'Semua field harus diisi'
            ])->setStatusCode(400);
        }
    
        // Prepare data with password hashing
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // Hash password
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'peran' => $this->request->getPost('peran')
        ];
    
        // Save user
        try {
            $result = $this->userModel->insert($data);
            
            return $this->response->setJSON([
                'message' => 'Pengguna berhasil dibuat',
                'id' => $result
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Gagal membuat pengguna: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // Edit user - fetch user data
    public function edit()
    {
        $id = $this->request->getPost('id_user');
        
        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                return $this->response->setJSON([
                    'error' => 'User not found'
                ])->setStatusCode(404);
            }
            
            return $this->response->setJSON($user);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to fetch user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

// Update user
public function update()
{
    $id = $this->request->getPost('id_user');
    
    // Validation rules
    $validationRules = [
        'username' => 'required',
        // 'password' => 'required',
        'nama' => 'required',
        'email' => 'required',
        'telepon' => 'required',
        'peran' => 'required'
    ];

    // Optional password update validation
    $password = $this->request->getPost('password');
    
    if ($password) {
        // Validate new password
        $validationRules['password'] = 'min_length[3]';
    }

    // Check validation
    if (!$this->validate($validationRules)) {
        return $this->response->setJSON([
            'error' => $this->validator->getErrors()
        ])->setStatusCode(400);
    }

    // Prepare update data
    $data = [
        'username' => $this->request->getPost('username'),
        'nama' => $this->request->getPost('nama'),
        'email' => $this->request->getPost('email'),
        'telepon' => $this->request->getPost('telepon'),
        'peran' => $this->request->getPost('peran')
    ];

    // Update password if provided
    if ($password) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Update user
    try {
        $result = $this->userModel->update($id, $data);
        
        return $this->response->setJSON([
            'message' => 'User updated successfully'
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'error' => 'Failed to update user: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}

    // Delete user
    public function delete()
    {
        $id = $this->request->getPost('id_user');
        
        try {
            $result = $this->userModel->delete($id);
            
            if ($result) {
                return $this->response->setJSON([
                    'message' => 'User deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'error' => 'Failed to delete user'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to delete user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}