<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        return view('listaproductos', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('createproductos', compact('categorias'));
    }

    public function store(Request $request)
    {
        Producto::create($request->all());
        return redirect()->route('listaproductos.index')->with('success', 'Producto creado exitosamente');
    }

    public function show(Producto $producto)
    {
        //
    }

    public function edit(Producto $listaproducto)
    {
        $categorias = Categoria::all();
        return view('editproductos', compact('listaproducto', 'categorias'));
    }

    public function update(Request $request, Producto $listaproducto)
    {
        $listaproducto->update($request->all());
        return redirect()->route('listaproductos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $listaproducto)
    {
        $listaproducto->delete();
        return redirect()->route('listaproductos.index')->with('success', 'Producto eliminado exitosamente');
    }
}
