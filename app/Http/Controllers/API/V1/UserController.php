<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Users\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 OpenApi",
 *      description="L5 Swagger OpenApi description",
 *      x={
 *          "logo": {
 *              "url": "https://via.placeholder.com/190x90.png?text=L5-Swagger"
 *          }
 *      },
 *      @OA\Contact(
 *          email="alexander.gallardo@itpeoplesas.net"
 *      ),
 *      @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     * Mostramos el listado de usuarios registrados.
     * @return Response
     *
     * @OA\Get(
     *     path="/api/user",
     *     tags={"user"},
     *     summary="Mostrar el listado de usuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todas los usuarios."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
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
     * @param  int  $id
     * @return Response
     * @OA\Get(
     *     path="/api/user/{id}",
     *     tags={"user"},
     *     summary="Mostrar info de un usuario",
     *     @OA\Parameter(
     *         description="Parámetro necesario para la consulta de datos de una empresa",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="int", value="1", summary="Introduce un número de id de usuario.")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar info de un usuario."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se ha encontrado el usuario."
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
            $user = User::findOrFail($id);

            return $this->sendResponse($user, 'Informacion del Usuario');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Usuario no localizado', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
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
