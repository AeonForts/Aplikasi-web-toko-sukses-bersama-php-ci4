<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{SisaTerkumpulModel.MeityModel};
use CodeIgniter\HTTP\ResponseInterface;

class SisaController extends BaseController
{
    protected $sisaTerkumpulModel;
    protected $meityModel;

    public function __construct() {
        $this->sisaTerkumpulModel = new SisaTerkumpulModel();
        $this->meityModel = new MeityModel();
    }

    public function list()
    {
        return view('pages/admin/sisa/list');
    }

    public function datatables()
    {

    }

    public function insertSisaToMeity()
    {

    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
