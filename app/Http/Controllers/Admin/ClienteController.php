<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Clientes\ClienteMezclasOncoExport;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Controller
{

    public function index()
    {
        $clientes = Cliente::orderBy('id', 'desc')->paginate(10);

        return view('admin.clientes.index', compact('clientes'));
    }


    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'razon_social' => ['required', 'string', 'max:255'],
        ]);

        Cliente::create($data);

        // Si ya manejas Swal por sesión en tu layout:
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cliente creado',
            'text'  => 'El cliente se registró correctamente.',
        ]);

        return redirect()->route('admin.clientes.create');
        // Si prefieres ir al listado (cuando hagamos index):
        // return redirect()->route('admin.clientes.index');
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }


    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'razon_social' => ['required', 'string', 'max:255'],
        ]);

        $cliente->update($data);

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cliente actualizado',
            'text'  => 'Los datos del cliente se actualizaron correctamente.',
        ]);

        return redirect()->route('admin.clientes.edit', $cliente);
        // más adelante, si quieres index:
        // return redirect()->route('admin.clientes.index');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Cliente eliminado',
            'text'  => 'El cliente fue eliminado correctamente.',
        ]);

        return redirect()->route('admin.clientes.index');
    }

    public function exportarMezclasOnco(Cliente $cliente)
    {
        $filename = 'reporte_mezclas_onco_cliente_' . $cliente->id . '.xlsx';

        return Excel::download(new ClienteMezclasOncoExport($cliente->id), $filename);
    }
}
