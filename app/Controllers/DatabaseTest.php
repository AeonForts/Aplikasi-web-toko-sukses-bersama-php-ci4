<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DatabaseTest extends Controller
{
    public function index()
    {
        // Load the database connection
        $db = \Config\Database::connect();

        // Test the connection
        if ($db->connID) {
            echo "Database connection successful!";
        } else {
            echo "Failed to connect to the database.";
        }
    }
}
