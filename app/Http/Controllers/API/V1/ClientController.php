<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ClientController extends BaseController
{

    /**
     * Mostramos el listado de Clientes registrados.
     * @return Response
     *
     * @OA\Get(
     *     path="/api/client",
     *     operationId="getClientList",
     *     tags={"Client"},
     *     summary="Mostrar listado de clientes",
     *     description="Retorna lista de clientes",
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
        $clients = Client::latest()->paginate(10);

        return $this->sendResponse($clients, 'Lista de Clientes');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     * @OA\Post (
     *     path="/api/client",
     *     operationId="createClient",
     *     tags={"Client"},
     *     summary="Crear un  cliente",
     *     description="Crea un cliente y retorna Informacion del cliente creado",
     *     @OA\RequestBody(
     *         description="Client object that needs to be created",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Nombre del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="lastname",
     *                      type="string",
     *                      description="Apellidos del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="identification",
     *                      type="string",
     *                      description="Identificacion del cliente"
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
     *         description="Mostrar info de cliente creado."
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
            'name' => 'required|string',
            'lastname' => 'required|string',
            'identification' => 'required|unique:clients',
            'description' => 'sometimes|max:100',
        ]);

        //if($validator->fails())
        //    return $this->sendError('Datos invalidos.', $validator->errors(), 400);
        $validator->validate();

        //Damos de alta en la BD
        $client = Client::create([
            'name'           => $request->name,
            'lastname'       => $request->lastname,
            'identification' => $request->identification,
            'description'    => $request->description
        ]);

        return $this->sendResponse($client, 'Usuario creado arisfactoriamente');
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
     *     path="/api/client/{id}",
     *     operationId="updateClient",
     *     tags={"Client"},
     *     summary="Actualiza cliente por ID",
     *     description="Actualiza cliente y retorna Informacion del cliente actualizado",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID Cliente",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\RequestBody(
     *         description="Client object that needs to be created",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Nombre del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="lastname",
     *                      type="string",
     *                      description="Apellidos del cliente"
     *                 ),
     *                 @OA\Property(
     *                      property="identification",
     *                      type="string",
     *                      description="Identificacion del cliente"
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
     *         description="Mostrar info de cliente actualizado."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el cliente."
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
            'name' => 'sometimes|max:100',
            'lastname' => 'sometimes|max:100',
            'identification' => 'sometimes|unique:clients,identification,' . $id,
            'description' => 'sometimes|max:100'
        ]);

        //if($validator->fails())
        //    return $this->sendError('No se pudo realizar la operacion', $validator->errors(), 400);
        $validator->validate();

        try {
            $client = Client::findOrFail($id);

            $client->name           = $request->name           ?? $client->name;
            $client->lastname       = $request->lastname       ?? $client->lastname;
            $client->identification = $request->identification ?? $client->identification;
            $client->description    = $request->description    ?? $client->description;

            $client->save();

            return $this->sendResponse($client, 'Cliente actualizado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Cliente no localizado', [], Response::HTTP_NOT_FOUND);

        }
    }

    /**
     * Display the specified client.
     *
     * @param  int  $id
     * @return Response
     * @OA\Get(
     *     path="/api/client/{id}",
     *     operationId="getClientById",
     *     tags={"Client"},
     *     summary="Mostrar Informacion de cliente",
     *     description="Retorna informacion de un cliente",
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
     *         description="Mostrar info de un cliente."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el cliente."
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
            //Localizamos el usuario por ID
            $client = Client::findOrFail($id);

            return $this->sendResponse($client, 'Informacion del Usuario');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Cliente no localizado', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     * @OA\Delete (
     *     path="/api/client/{id}",
     *     operationId="deleteClient",
     *     tags={"Client"},
     *     summary="Elimina cliente por ID",
     *     description="Elimina cliente y retorna Informacion del cliente eliminado",
     *     @OA\Parameter(
     *          name="id",
     *          description="ID Cliente",
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
     *         description="Mostrar info de cliente eliminado."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el cliente."
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
            $client = Client::findOrFail($id);

            $client->delete();

            return $this->sendResponse([$client], 'Cliente eliminado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Cliente no localizado', [], Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function list()
    {
        $clients = Client::pluck('name', 'id');

        return $this->sendResponse($clients, 'Lista de Clientes');
    }
}
