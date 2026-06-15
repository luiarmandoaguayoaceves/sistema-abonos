<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->latest()
            ->paginate(10);

        return view('clientes', compact('clientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cve_cliente' => ['nullable', 'integer'],
            'nombre' => ['required', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'frecuencia_pago' => ['required', 'string', 'max:50'],
        ]);

        Cliente::create($validated);

        return back()->with('success', 'Cliente agregado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->delete();

        return back()->with('success', 'Cliente eliminado correctamente.');
    }
}