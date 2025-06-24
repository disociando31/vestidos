<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(20);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.crear');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_registro' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $cliente = Cliente::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'] ?? null,
            'telefono' => $validated['telefono'],
            'direccion' => $validated['direccion'] ?? null,
            'fecha_registro' => $validated['fecha_registro'] ?? now()
        ]);

        return redirect()->route('clientes.mostrar', $cliente)
            ->with('success', 'Cliente registrado correctamente');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['rentas' => function ($query) {
            $query->with(['items', 'pagos'])
                ->orderBy('fecha_renta', 'desc');
        }]);

        return view('clientes.mostrar', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_registro' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cliente->update($validator->validated());

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Datos del cliente actualizados');
    }
}
