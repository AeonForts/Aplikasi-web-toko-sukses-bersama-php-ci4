<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Use the verifyPassword method from UserModel
        if ($this->userModel->verifyPassword($username, $password)) {
            // Find user again to get full details
            $user = $this->userModel->where('username', $username)->first();

            // Set session data
            $sessionData = [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'peran' => $user['peran'],
                'logged_in' => true,
            ];

            session()->set($sessionData);

            // Redirect based on role
            switch ($user['peran']) {
                case 'Admin':
                    return redirect()->to('/admin/dashboard');
                case 'Owner':
                    return redirect()->to('/owner/dashboard');
                case 'Petugas':
                    return redirect()->to('/petugas/dashboard');
                default:
                    return redirect()->to('/login')->with('error', 'Invalid user role');
            }
        } else {
            // Login failed
            return redirect()->back()->with('error', 'Invalid username or password');
        }
    }

    public function logout()
    {
        // Destroy session
        session()->destroy();
        return redirect()->to('/login');
    }
    
    // protected $session;

    // public function __construct()
    // {
    //     // Load the session service
    //     $this->session = \Config\Services::session();
    // }

    // public function login()
    // {
    //     return view('auth/login');
    // }

    // public function doLogin()
    // {
    //     $username = $this->request->getPost('username');
    //     $password = $this->request->getPost('password_hash');

    //     $userModel = new UserModel();
    //     $user = $userModel->where('username', $username)->first();

    //     if ($user && $password === $user['password_hash']) {
    //         $sessionData = [
    //             'id_user' => $user['id_user'],
    //             'username' => $user['username'],
    //             'nama' => $user['nama'],
    //             'peran' => $user['peran'],
    //             'logged_in' => true,
    //         ];

    //         $this->session->set($sessionData);

    //         switch ($user['peran']) {
    //             case 'Admin':
    //                 return redirect()->to('/admin/dashboard');
    //             case 'Owner':
    //                 return redirect()->to('/owner/dashboard');
    //             case 'Petugas':
    //                 return redirect()->to('/petugas/dashboard');
    //             default:
    //                 return redirect()->to('/login')->with('error', 'User role not recognized')->withInput();
    //         }
    //     } else {
    //         return redirect()->to('/login')->with('error', 'Login failed, please check your username/password!')->withInput();
    //     }
    // }

    // public function logout()
    // {
    //     $this->session->destroy();
    //     return redirect()->to('/login');
    // }
}
