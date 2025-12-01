<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index()
    {
        $categorias = Categoria::all(); //obtiene todas las categoria
        return view('listacategorias', compact('categorias')); // Muestra la vista con las categorias
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('createcategorias'); // vista con formulario para crear una nueva categoria
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        Categoria::create($request->all()); // crea una nueva categoria con los datos del formulario
        return redirect()->route('listacategorias.index')->with('success', 'Categoría creada exitosamente'); // redirecciona a la lista de categorias
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Categoria $listacategoria)
    {
        return view('editcategorias', compact('listacategoria')); // Muestra la vista con el formulario para editar la categoria
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, Categoria $listacategoria)
    {
        $listacategoria->update($request->all()); // Actualiza la categoria con los datos del formulario
        return redirect()->route('listacategorias.index')->with('success', 'Categoría actualizada exitosamente'); // Redirecciona a la lista de categorias
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Categoria $listacategoria)
    {
        $listacategoria->delete(); // Elimina la categoria
        return redirect()->route('listacategorias.index')->with('success', 'Categoría eliminada exitosamente'); // Redirecciona a la lista de categorias
    }
}
