<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Users\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       // if (!Gate::allows('isAdmin')) {
       //     return $this->unauthorizedResponse();
       // }

        $users = User::latest()->paginate(10);

        return $this->sendResponse($users, 'Lista de Usuarios');
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
            'type'     => 'required|in:Administrador,Encargado',
            'name'     => 'required|string|max:100',
            'username' => 'required|string|min:5|max:20|unique:users',
            'email'    => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:5'
        ]);

        //if($validator->fails())
        //    return $this->sendError('Datos invalidos.', $validator->errors(), 400);
        $validator->validate();

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'type'     => $request->type
        ]);

        return $this->sendResponse($user, 'Usuario creado arisfactoriamente');
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
            'type'     => 'sometimes|in:Administrador,Encargado',
            'name'     => 'sometimes|string|max:100',
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'email'    => 'sometimes|string|email|unique:users,email,' . $id,
        ]);

        //if($validator->fails())
        //    return $this->sendError('No se pudo realizar la operacion', $validator->errors()->toArray(), 400);
        $validator->validate();

        try {
            $user = User::findOrFail($id);

            $user->name     = $request->name ?? $user->name;
            $user->type     = $request->type ?? $user->type;
            $user->username = $request->username ?? $user->username;
            $user->email    = $request->email ?? $user->email;
            $user->password = $request->password ?? Hash::make($request->password);

            $user->save();

            return $this->sendResponse($user, 'Usuario actualizado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Usuario no localizado', [], Response::HTTP_NOT_FOUND);

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
            $user = User::findOrFail($id);

            return $this->sendResponse($user, 'Informacion del Usuario');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Usuario no localizado', [], Response::HTTP_NOT_FOUND);
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
            $user = User::findOrFail($id);

            $user->delete();

            return $this->sendResponse([$user], 'Usuario eliminado satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Usuario no localizado', [], Response::HTTP_NOT_FOUND);
        }

    }
}
