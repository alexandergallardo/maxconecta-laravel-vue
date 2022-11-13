<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Movie;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class MovieController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $movies = Movie::latest()->paginate(10);

        return $this->sendResponse($movies, 'Lista de Peliculas');
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
            'title' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'year' => 'required|numeric|min:4|max:4',
            'stock' => 'required|numeric|min:0',
            'description' => 'sometimes|max:200'
        ]);

        //if($validator->fails())
        //    return $this->sendError('Datos invalidos.', $validator->errors(), 400);
        $validator->validate();

        //Damos de alta en la BD
        $movie = Movie::create([
            'title'       => $request->title,
            'category'    => $request->category,
            'description' => $request->description,
            'year'        => $request->year,
            'stock'       => $request->stock
        ]);

        return $this->sendResponse($movie, 'Pelicula creada arisfactoriamente');
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
            'title'       => 'sometimes|max:100',
            'category'    => 'sometimes|max:50',
            'description' => 'sometimes|max:200',
            'year'        => 'required|numeric|min:4|max:4',
            'stock'       => 'required|numeric|min:0'
        ]);

        //if($validator->fails())
        //    return $this->sendError('No se pudo realizar la operacion', $validator->errors(), 400);
        $validator->validate();

        try {
            $movie = Movie::findOrFail($id);

            $movie->title       = $request->title ?? $movie->title;
            $movie->category    = $request->category ?? $movie->category;
            $movie->description = $request->description ?? $movie->description;
            $movie->year        = $request->year ?? $movie->year;
            $movie->stock       = $request->stock ?? $movie->stock;

            $movie->save();

            return $this->sendResponse($movie, 'Pelicula actualizada satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Pelicula no localizada', [], Response::HTTP_NOT_FOUND);

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
            $movie = Movie::findOrFail($id);

            return $this->sendResponse($movie, 'Informacion de la Pelicula');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Pelicula no localizada', [], Response::HTTP_NOT_FOUND);
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
            $movie = Movie::findOrFail($id);

            $movie->delete();

            return $this->sendResponse([$movie], 'Pelicula eliminada satisfactoriamente');
        }catch (ModelNotFoundException $e)
        {
            return $this->sendError('Pelicula no localizada', [], Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function list()
    {
        $movies = Movie::pluck('title as name', 'id');

        return $this->sendResponse($movies, 'Lista de Peliculas');
    }
}
