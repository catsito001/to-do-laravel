<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TareaController extends Controller
{

    public function index()
    {
        $tareas = Tarea::paginate(9); // Obtener 9 tareas por página
    return view('tareas.index', compact('tareas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('imagenes', 'public');
        }

        $tarea = Tarea::create($data);
        return response()->json($tarea);
    }

    public function update(Request $request, Tarea $tarea)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($tarea->imagen) {
                Storage::disk('public')->delete($tarea->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('imagenes', 'public');
        }

        $tarea->update($data);
        return response()->json($tarea);
    }

    public function destroy(Tarea $tarea)
    {
        if ($tarea->imagen) {
            Storage::disk('public')->delete($tarea->imagen);
        }
        $tarea->delete();
        return response()->json(['success' => true]);
    }


}
