<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ClientController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
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
     * Display the specified user.
     *
     * @param  int  $id
     * @return Response
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