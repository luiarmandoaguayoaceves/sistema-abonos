<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:255',
            'frecuencia_pago' => 'required',
            'correo' => 'nullable|email',
        ]);

        Cliente::create($request->all());
        return back()->with('success', 'Cliente agregado correctamente.');
    }

    public function destroy($id)
    {
        Cliente::destroy($id);
        return back()->with('success', 'Cliente eliminado.');
    }
}
