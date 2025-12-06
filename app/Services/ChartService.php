<?php

namespace App\Services;

use App\Models\ViewSummaryModel;

class ChartService
{
    protected $viewSummaryModel;

    public function __construct()
    {
        $this->viewSummaryModel = new ViewSummaryModel();
    }


}