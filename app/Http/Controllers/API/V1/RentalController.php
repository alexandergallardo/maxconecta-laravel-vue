<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Client;
use App\Models\Movie;
use App\Models\Rental;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RentalController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @OA\Get(
     *     path="/api/rental",
     *     operationId="getRentalList",
     *     tags={"Rental"},
     *     summary="Mostrar listado de alquiler",
     *     description="Retorna lista de alquiler",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index()
    {
        $rentals = Rental::latest()->with('movies', 'clients')->paginate(10);

        return $this->sendResponse($rentals, 'Lista de Peliculas Alquiladas');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     * @OA\Post (
     *     path="/api/rentall",
     *     operationId="createRental",
     *     tags={"Rental"},
     *     summary="Crear un alquiler",
     *     description="Crea un alquiler y retorna Informacion del alquiler creado",
     *     @OA\RequestBody(
     *         description="Rental object that needs to be created",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      property="delivery",
     *                      type="date",
     *                      description="Fecha de alquiler - entrega a cliente (YYYY-MM-DD)"
     *                 ),
     *                 @OA\Property(
     *                      property="client_id",
     *                      type="integer",
     *                      description="ID del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="movie_id",
     *                      type="integer",
     *                      description="ID de la pelicula"
     *                 ),
     *                 @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Descripcion"
     *                 ),
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         description="Mostrar info de alquiler creado."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el cliente o pelicula."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        //validamos los datos
        $validator = Validator::make($request->all(), [
            'delivery'  => 'required|date',
            'client_id'       => 'required|integer',
            'movie_id'        => 'required|integer',
        ]);

        //if($validator->fails())
        //    return $this->sendError('Datos invalidos.', $validator->errors(), 400);
        $validator->validate();


        try{
            //Localizaos el ID del cliente para  validar que existe en la BD
            $client = Client::findOrFail($request->client_id);
        }catch (ModelNotFoundException $e)
        {
            //Si no hay respuesta de la busqueda del cliente por ID
            return $this->sendError('Cliente no encontrado.', [], Response::HTTP_NOT_FOUND);

        }

        try{
            //Localizaos el ID de la película para  validar que existe en la BD
            $movie = Movie::findOrFail($request->movie_id);

            //Se valida que se tenga stock de la pelicula
            if(!$movie->stock)
                return $this->sendError('Sin Stock', [], Response::HTTP_NOT_FOUND);
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Pelicula no encontrada.', [], Response::HTTP_NOT_FOUND);
        }

        $rental = new Rental();
        $rental->delivery  = $request->delivery;
        $rental->client_id = $request->client_id;
        $rental->movie_id  = $request->movie_id;
        $rental->description = $request->description ? $request->description : '';

        $rental->save();

        $data = ["rental_id" => $rental->id,
                "delivery" => $rental->delivery,
                "client_id" => $rental->client_id,
                "client_name" => $client->name.' '.$client->lastname,
                "movie_id" => $rental->movie_id,
                "movie_title" => $movie->title,
                "movie_category" => $movie->category];

        $movie->stock       = $movie->stock - 1;
        $movie->save();  //Actualizamos el Stock de pelicula

        return $this->sendResponse($data, 'Alquiler registrado sarisfactoriamente');
    }

    /**
     * Update the resource in storage
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     * @throws ValidationException
     * @OA\Put (
     *     path="/api/rentañ/{id}",
     *     operationId="updateRental",
     *     tags={"Rental"},
     *     summary="Actualiza alquiler por ID",
     *     description="Actualiza alquiler y retorna Informacion actualizada",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID Alquiler",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         description="Rental object that needs to be created",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      property="delivery",
     *                      type="date",
     *                      description="Fecha de alquiler - entrega a cliente (YYYY-MM-DD)"
     *                 ),
     *                  @OA\Property(
     *                      property="entry",
     *                      type="date",
     *                      description="Fecha de entrada - devolución del cliente (YYYY-MM-DD)"
     *                 ),
     *                 @OA\Property(
     *                      property="client_id",
     *                      type="integer",
     *                      description="ID del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="movie_id",
     *                      type="integer",
     *                      description="ID de la pelicula"
     *                 ),
     *                 @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Descripcion"
     *                 ),
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         description="Mostrar info de alquiler creado."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado del alquiler o cliente o pelicula."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        //validamos los datos
        $validator = Validator::make($request->all(), [
            'entry'      => 'required|date',
            'delivery'  => 'sometimes',
            'client_id'       => 'sometimes',
            'movie_id'        => 'sometimes',
            'description'     => 'sometimes',
        ]);

        //if($validator->fails())
        //     return $this->sendError('No se pudo realizar la operacion', $validator->errors(), 400);
        $validator->validate();

        try {
            //Localizamos el Alquiler  por ID
            $rental = Rental::findOrFail($id);

            //Localizamos la película para actualizar el stock
            $movie = Movie::findOrFail($rental->movie_id);

            if($request->entry_date && !$rental->entry)
            {
                $movie->stock       = $movie->stock + 1;
                $movie->save();  //Actualizamos el Stock de pelicula
            }

            $rental->entry       = $request->entry ?? $rental->entry;
            $rental->delivery    = $request->delivery ?? $rental->delivery;
            $rental->cliente_id  = $request->cliente_id ?? $rental->cliente_id;
            $rental->movie_id    = $request->movie_id ?? $rental->movie_id;
            $rental->description = $request->description ?? $rental->description;

            $rental->save(); //Actualizamos el alquiler

            return $this->sendResponse($rental, 'Alquiler actualizado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Alquiler no localizado', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified rental.
     *
     * @param  int  $id
     * @return Response
     * @OA\Get(
     *     path="/api/rental/{id}",
     *     operationId="getRentalById",
     *     tags={"Rental"},
     *     summary="Mostrar Informacion del alquiler",
     *     description="Retorna informacion de un alquiler",
     *     @OA\Parameter(
     *         description="Parámetro necesario para la consulta de datos",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="int", value="1", summary="Introduce un número de id de usuario.")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar info de un alquiler."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el alquiler"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function show($id): Response
    {
        try{
            $rental = Rental::with('movies', 'clients')->findOrFail($id);

            return $this->sendResponse($rental, 'Informacion del Alquiler de la Pelicula');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Alquiler no localizado', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     * @OA\Delete (
     *     path="/api/rental/{id}",
     *     operationId="deleteRental",
     *     tags={"Rental"},
     *     summary="Elimina lquiler por ID",
     *     description="Elimina alquiler y retorna Informacion de alquielr eliminado",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID alquiler",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         description="Mostrar info de alquiler eliminado."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el alquiler."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function destroy($id)
    {
        try{
            $rental = Rental::with('movies', 'clients')->findOrFail($id);

            $movie = Movie::findOrFail($rental->movie_id);
            $movie->stock       = $movie->stock + 1;
            $movie->save();  //Actualizamos el Stock de pelicula

            $rental->delete(); //Eliminamos el Alquiler

            return $this->sendResponse([$rental], 'Alquiler eliminado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Alquiler no localizado', [], Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function list()
    {
        $rentals = Rental::all();

        return $this->sendResponse($rentals, 'Lista de Peliculas');
    }
}
