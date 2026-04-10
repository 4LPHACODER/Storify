<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use Illuminate\Http\Request;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        AdminDashboardController $adminDashboardController,
        CustomerDashboardController $customerDashboardController,
    ): Response
    {
        if ($request->user()->isAdmin()) {
            return $adminDashboardController();
        }

        return $customerDashboardController($request);
    }
}
