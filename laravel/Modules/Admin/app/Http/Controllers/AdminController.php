<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin::index');
    }
}
