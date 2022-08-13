<?php

namespace App\Http\Controllers\App;

/**
 * @group Dashboard
 * @authenticated
 */
class DashboardController extends Controller
{
    /**
     * App Dashboard
     */
    public function index()
    {
        return response()->json(['message' => 'success']);
    }
}
