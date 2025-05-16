<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        return view('admin.oncologicos.solicitudes.index');
    }
}
