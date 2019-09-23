<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category; //se obtiene el modelo de la base de datos de categoria

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }


    public function index()
    {
        $categories = Category::all(); //saca todas las categorias de la tabla de base de datos
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id)
    {

        $category = Category::find($id);

        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'mensaje' => 'Codigo de categoria erroneo'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        //recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //validar datos
        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'name' => 'required|unique:categories'
            ]);

            //guardar la categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'
            ];
        }
        //devolver los resultados
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        //recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        var_dump("aqui");die();

        if (!empty($params_array)) {
            //validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);
            //quitar lo que no se quiera actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            //actualizar el registro(categoria)
            $category = Category::where('id', $id)->update($params_array);
            if($category){
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $params_array
                ];
            }else{
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Objeto con id no encontrado'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'
            ];
        }
        //devolver los resultados
        return response()->json($data, $data['code']);
    }
}
