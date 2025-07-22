<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::when($request->filled('buscar'), function ($query) use ($request) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%")
                ->orWhere('email', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('nombre')
        ->paginate(20);

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
            'telefono2' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'fecha_cumpleanos' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existe = Cliente::where('nombre', $request->nombre)
                        ->where('telefono', $request->telefono)
                        ->exists();

        if ($existe) {
            return redirect()->back()
                ->withErrors(['telefono' => 'Ya existe un cliente con ese nombre y teléfono.'])
                ->withInput();
        }

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'telefono2' => $request->telefono2,
            'direccion' => $request->direccion,
            'fecha_registro' => $request->fecha_registro ?? now(),
            'fecha_cumpleanos' => $request->fecha_cumpleanos
        ]);

        return redirect()->route('clientes.mostrar', $cliente)
            ->with('exito', 'Cliente registrado correctamente');
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
            'telefono2' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'fecha_cumpleanos' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cliente->update($validator->validated());

        return redirect()->route('clientes.show', $cliente)
            ->with('exito', 'Datos del cliente actualizados correctamente');
    }
}
