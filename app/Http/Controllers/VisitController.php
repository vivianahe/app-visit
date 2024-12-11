<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{

    /**
     * @OA\Info(
     *     title="API de Visitas",
     *     description="API para manejar las visitas almacenadas en la base de datos.",
     *     version="1.0.0",
     *     @OA\Contact(
     *         email="contacto@tusitio.com"
     *     )
     * )
     * @OA\Get(
     *     path="/api/getVisit",
     *     summary="Obtener todas las visitas",
     *     description="Recupera todas las visitas almacenadas en la base de datos.",
     *     tags={"Visits"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de visitas obtenida con éxito.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Viviana Herrera"),
     *                 @OA\Property(property="email", type="string", example="vivianaherrerahe@gmail.com"),
     *                 @OA\Property(property="latitude", type="string", example="4.7110"),
     *                 @OA\Property(property="longitude", type="string", example="-74.0721"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-11T00:54:16.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron registros.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No se encontraron registros.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al obtener los datos."),
     *             @OA\Property(property="message", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $visits = Visit::all();

            if ($visits->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron registros.'
                ], 404);
            }

            return response()->json($visits, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los datos.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/setVisits",
     *     summary="Crear una nueva visita",
     *     description="Este endpoint permite crear una nueva visita con los datos proporcionados.",
     *     tags={"Visits"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la nueva visita.",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "latitude", "longitude"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juanperez@correo.com"),
     *             @OA\Property(property="latitude", type="string", example="4.7110"),
     *             @OA\Property(property="longitude", type="string", example="-74.0721")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Visita creada exitosamente.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Visita creada exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Datos inválidos"),
     *             @OA\Property(property="message", type="object", 
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="El campo name es obligatorio.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email es obligatorio."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al crear la visita.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al crear la visita"),
     *             @OA\Property(property="message", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'latitude' => 'required|string|max:255',
            'longitude' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'message' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            Visit::create([
                'name' => $request->name,
                'email' => $request->email,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            DB::commit();

            return response()->json(['message' => 'Visita creada exitosamente.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Error al crear la visita',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/visitId/{id}",
     *     summary="Obtener una visita por ID",
     *     description="Este endpoint permite obtener los detalles de una visita existente por su ID.",
     *     tags={"Visits"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la visita que se desea obtener.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita encontrada con éxito.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", example="juanperez@correo.com"),
     *             @OA\Property(property="latitude", type="string", example="4.7110"),
     *             @OA\Property(property="longitude", type="string", example="-74.0721"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-11T00:54:16.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-11T00:54:16.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Visita no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al obtener la visita.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error al obtener la visita"),
     *             @OA\Property(property="message", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */
    public function edit(string $id)
    {
        $visit = Visit::find($id);

        if (!$visit) {
            return response()->json([
                'error' => 'Visita no encontrada'
            ], 404);
        }
        return response()->json($visit, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/update",
     *     summary="Actualizar una visita",
     *     description="Este endpoint permite actualizar los detalles de una visita existente.",
     *     tags={"Visits"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la visita a actualizar.",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"id", "name", "email", "latitude", "longitude"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juanperez@correo.com"),
     *             @OA\Property(property="latitude", type="string", example="4.7110"),
     *             @OA\Property(property="longitude", type="string", example="-74.0721")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita actualizada exitosamente.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Visita actualizada exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Datos inválidos"),
     *             @OA\Property(property="message", type="object", 
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="El campo name es obligatorio.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email es obligatorio."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Visita no encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al actualizar la visita.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Ocurrió un error al actualizar la visita."),
     *             @OA\Property(property="message", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:visits,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'latitude' => 'required|string|max:255',
            'longitude' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'message' => $validator->errors()
            ], 400);
        }

        DB::beginTransaction();

        try {
            $visit = Visit::find($request->id);

            if (!$visit) {
                return response()->json(['error' => 'Visita no encontrada.'], 404);
            }

            $visit->update([
                'name' => $request->name,
                'email' => $request->email,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            DB::commit();

            return response()->json(['message' => 'Visita actualizada exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Ocurrió un error al actualizar la visita.'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/delete/{id}",
     *     summary="Eliminar una visita",
     *     description="Este endpoint permite eliminar una visita existente utilizando su ID.",
     *     tags={"Visits"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la visita a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita eliminada exitosamente.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Visita eliminada exitosamente!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Visita no encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al eliminar la visita.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Ocurrió un error al eliminar la visita."),
     *             @OA\Property(property="message", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $visit = Visit::find($id);

        if (!$visit) {
            return response()->json([
                'error' => 'Visita no encontrada.'
            ], 404);
        }

        try {
            $visit->delete();

            return response()->json([
                'message' => 'Visita eliminada exitosamente!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al eliminar la visita.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
