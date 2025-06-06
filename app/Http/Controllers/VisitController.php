<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VisitsImport;



class VisitController extends Controller
{
    /**
     * Muestra una lista de todas las visitas.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function index()
    {
        // Obtiene todas las visitas de la base de datos.
        $visits = Visit::all();
        // Retorna las visitas en formato JSON con un código de estado 200 (OK).
        return response()->json($visits, 200);
    }

    /**
     * Almacena una nueva visita en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Define las reglas de validación para los datos de entrada.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Si la validación falla, retorna una respuesta JSON con los errores y un código 422 (Unprocessable Entity).
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crea una nueva instancia de Visit y la guarda en la base de datos.
        $visit = Visit::create($request->all());
        // Retorna la visita creada en formato JSON con un código de estado 201 (Created).
        return response()->json($visit, 201);
    }

    /**
     * Muestra los detalles de una visita específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Busca una visita por su ID. Si no se encuentra, retorna null.
        $visit = Visit::find($id);

        // Si la visita no se encuentra, retorna una respuesta de error 404 (Not Found).
        if (!$visit) {
            return response()->json(['message' => 'Visita no encontrada'], 404);
        }

        // Retorna la visita encontrada en formato JSON con un código de estado 200 (OK).
        return response()->json($visit, 200);
    }

    /**
     * Actualiza una visita existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Busca una visita por su ID. Si no se encuentra, retorna null.
        $visit = Visit::find($id);

        // Si la visita no se encuentra, retorna una respuesta de error 404 (Not Found).
        if (!$visit) {
            return response()->json(['message' => 'Visita no encontrada'], 404);
        }

        // Define las reglas de validación para los datos de entrada.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Si la validación falla, retorna una respuesta JSON con los errores y un código 422 (Unprocessable Entity).
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualiza los datos de la visita con los valores del request.
        $visit->update($request->all());
        // Retorna la visita actualizada en formato JSON con un código de estado 200 (OK).
        return response()->json($visit, 200);
    }

    /**
     * Elimina una visita de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Busca una visita por su ID. Si no se encuentra, retorna null.
        $visit = Visit::find($id);

        // Si la visita no se encuentra, retorna una respuesta de error 404 (Not Found).
        if (!$visit) {
            return response()->json(['message' => 'Visita no encontrada'], 404);
        }

        // Elimina la visita de la base de datos.
        $visit->delete();
        // Retorna una respuesta vacía con un código de estado 204 (No Content) para indicar éxito.
        return response()->json(null, 204);
    }

    public function importFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new VisitsImport, $request->file('file'));

        return response()->json(['message' => 'Visitas importadas correctamente', 'data' => Visit::all()]);
    }
}

