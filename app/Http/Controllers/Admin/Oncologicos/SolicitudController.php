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

    public function create()
    {
        return view('admin.oncologicos.solicitudes.create');
    }

    public function store(Request $request)
    {
        return response()->json(
            $request->all(),
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }



}
