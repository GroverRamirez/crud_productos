# Guía Completa: CRUD de Productos con Laravel

Esta guía te enseñará paso a paso cómo crear un proyecto CRUD completo de productos con categorías, usando Laravel, Bootstrap y un menú lateral simple.

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer instalado
- Base de datos (MySQL, PostgreSQL o SQLite)
- Editor de código (VS Code, PHPStorm, etc.)

---

## Paso 1: Crear el Proyecto Laravel

### 1.1 Instalar Laravel

Abre tu terminal y ejecuta:

```bash
composer create-project laravel/laravel crud_productos
cd crud_productos
```

### 1.2 Configurar el archivo .env

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_productos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

**Nota:** Si usas SQLite, cambia `DB_CONNECTION=sqlite` y crea el archivo:
```bash
touch database/database.sqlite
```

### 1.3 Generar la clave de la aplicación

```bash
php artisan key:generate
```

---

## Paso 2: Crear la Base de Datos

### 2.1 Crear la base de datos

Crea la base de datos en tu gestor (phpMyAdmin, MySQL Workbench, etc.) o ejecuta:

```sql
CREATE DATABASE crud_productos;
```

### 2.2 Ejecutar migraciones iniciales

```bash
php artisan migrate
```

---

## Paso 3: Crear Migraciones

### 3.1 Migración de Categorías

```bash
php artisan make:migration create_categorias_table
```

Edita el archivo generado en `database/migrations/XXXX_create_categorias_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
```

### 3.2 Migración de Productos

```bash
php artisan make:migration create_productos_table
```

Edita el archivo generado en `database/migrations/XXXX_create_productos_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
```

### 3.3 Ejecutar las migraciones

```bash
php artisan migrate
```

---

## Paso 4: Crear los Modelos

### 4.1 Modelo Categoria

```bash
php artisan make:model Categoria
```

Edita `app/Models/Categoria.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
```

### 4.2 Modelo Producto

```bash
php artisan make:model Producto
```

Edita `app/Models/Producto.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
```

---

## Paso 5: Crear los Controladores

### 5.1 Controlador de Categorías

```bash
php artisan make:controller CategoriaController --resource
```

Edita `app/Http/Controllers/CategoriaController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return view('listacategorias', compact('categorias'));
    }

    public function create()
    {
        return view('createcategorias');
    }

    public function store(Request $request)
    {
        Categoria::create($request->all());
        return redirect()->route('listacategorias.index')->with('success', 'Categoría creada exitosamente');
    }

    public function edit(Categoria $listacategoria)
    {
        return view('editcategorias', compact('listacategoria'));
    }

    public function update(Request $request, Categoria $listacategoria)
    {
        $listacategoria->update($request->all());
        return redirect()->route('listacategorias.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Categoria $listacategoria)
    {
        $listacategoria->delete();
        return redirect()->route('listacategorias.index')->with('success', 'Categoría eliminada exitosamente');
    }
}
```

### 5.2 Controlador de Productos

```bash
php artisan make:controller ProductoController --resource
```

Edita `app/Http/Controllers/ProductoController.php`:

```php
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
```

---

## Paso 6: Configurar las Rutas

Edita `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('listacategorias', CategoriaController::class);
Route::resource('listaproductos', ProductoController::class);
```

---

## Paso 7: Crear las Vistas

### 7.1 Vista: Lista de Categorías

Crea `resources/views/listacategorias.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Categorías</h2>
                <a href="{{route('listacategorias.create')}}" class="btn btn-primary">Nueva Categoría</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{session('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $categoria)
                            <tr>
                                <td>{{$categoria->id}}</td>
                                <td>{{$categoria->nombre}}</td>
                                <td>{{$categoria->descripcion ?? '-'}}</td>
                                <td>
                                    <a href="{{route('listacategorias.edit', $categoria)}}" class="btn btn-sm btn-warning">Editar</a>
                                    <form action="{{route('listacategorias.destroy', $categoria)}}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay categorías registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 7.2 Vista: Crear Categoría

Crea `resources/views/createcategorias.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <h2 class="mb-3">Nueva Categoría</h2>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('listacategorias.store')}}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{old('nombre')}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3">{{old('descripcion')}}</textarea>
                        </div>
                        <div>
                            <a href="{{route('listacategorias.index')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 7.3 Vista: Editar Categoría

Crea `resources/views/editcategorias.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <h2 class="mb-3">Editar Categoría</h2>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('listacategorias.update', $listacategoria->id)}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{old('nombre', $listacategoria->nombre)}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3">{{old('descripcion', $listacategoria->descripcion)}}</textarea>
                        </div>
                        <div>
                            <a href="{{route('listacategorias.index')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 7.4 Vista: Lista de Productos

Crea `resources/views/listaproductos.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Productos</h2>
                <a href="{{route('listaproductos.create')}}" class="btn btn-primary">Nuevo Producto</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{session('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                            <tr>
                                <td>{{$producto->id}}</td>
                                <td>{{$producto->nombre}}</td>
                                <td>{{$producto->descripcion ?? '-'}}</td>
                                <td>{{number_format($producto->precio, 2)}} Bs</td>
                                <td>{{$producto->stock}}</td>
                                <td>{{$producto->categoria->nombre}}</td>
                                <td>
                                    <a href="{{route('listaproductos.edit', $producto)}}" class="btn btn-sm btn-warning">Editar</a>
                                    <form action="{{route('listaproductos.destroy', $producto)}}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay productos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 7.5 Vista: Crear Producto

Crea `resources/views/createproductos.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <h2 class="mb-3">Nuevo Producto</h2>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('listaproductos.store')}}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{old('nombre')}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3">{{old('descripcion')}}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" name="precio" id="precio" value="{{old('precio')}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="stock" value="{{old('stock', 0)}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" id="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{$categoria->id}}" {{old('categoria_id') == $categoria->id ? 'selected' : ''}}>
                                        {{$categoria->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <a href="{{route('listaproductos.index')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 7.6 Vista: Editar Producto

Crea `resources/views/editproductos.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <div class="bg-primary text-white" style="width: 200px; min-height: 100vh; padding: 20px;">
            <h5 class="mb-4">CRUD Productos</h5>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{route('listacategorias.index')}}" class="text-white text-decoration-none {{request()->routeIs('listacategorias.*') ? 'fw-bold' : ''}}">
                        Categorías
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{route('listaproductos.index')}}" class="text-white text-decoration-none {{request()->routeIs('listaproductos.*') ? 'fw-bold' : ''}}">
                        Productos
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            <h2 class="mb-3">Editar Producto</h2>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('listaproductos.update', $listaproducto->id)}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{old('nombre', $listaproducto->nombre)}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3">{{old('descripcion', $listaproducto->descripcion)}}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" name="precio" id="precio" value="{{old('precio', $listaproducto->precio)}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="stock" value="{{old('stock', $listaproducto->stock)}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" id="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{$categoria->id}}" {{old('categoria_id', $listaproducto->categoria_id) == $categoria->id ? 'selected' : ''}}>
                                        {{$categoria->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <a href="{{route('listaproductos.index')}}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## Paso 8: Probar la Aplicación

### 8.1 Iniciar el servidor

```bash
php artisan serve
```

### 8.2 Acceder a la aplicación

Abre tu navegador en: `http://localhost:8000`

### 8.3 Rutas disponibles

- **Categorías:**
  - Lista: `http://localhost:8000/listacategorias`
  - Crear: `http://localhost:8000/listacategorias/create`
  - Editar: `http://localhost:8000/listacategorias/{id}/edit`

- **Productos:**
  - Lista: `http://localhost:8000/listaproductos`
  - Crear: `http://localhost:8000/listaproductos/create`
  - Editar: `http://localhost:8000/listaproductos/{id}/edit`

---

## 🎨 Características Implementadas

✅ **CRUD completo** de Categorías y Productos  
✅ **Relaciones** entre modelos (Producto pertenece a Categoria)  
✅ **Menú lateral** simple con Bootstrap  
✅ **Diseño responsive** con Bootstrap 5  
✅ **Validación** de formularios  
✅ **Mensajes de éxito** al realizar acciones  
✅ **Confirmación** antes de eliminar  
✅ **Formato de precios** en Bolivianos (Bs)  

---

## 📝 Notas Importantes

1. **Seguridad:** Este es un proyecto de aprendizaje. Para producción, agrega:
   - Validación de formularios en el backend
   - Autenticación de usuarios
   - Protección CSRF (ya incluida con `@csrf`)
   - Sanitización de datos

2. **Mejoras posibles:**
   - Agregar paginación a las tablas
   - Implementar búsqueda y filtros
   - Agregar imágenes a los productos
   - Implementar autenticación

3. **Base de datos:** Asegúrate de tener al menos una categoría antes de crear productos.

---

## 🚀 Siguientes Pasos

- Agregar validación de formularios
- Implementar autenticación
- Agregar paginación
- Mejorar el diseño
- Agregar más funcionalidades

¡Feliz programación! 🎉

