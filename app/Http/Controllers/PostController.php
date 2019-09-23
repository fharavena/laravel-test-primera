<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage','getPostsByCategory','getPostsByUser']]);
    }

    public function index()
    {
        $posts = Post::all()->load('category');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $posts
        ], 200);
    }

    public function show($id)
    {
        $post = Post::find($id)->load('category')
                                ->load('user');

        if (is_object($post)) {
            $data =  [
                'code' => 200,
                'status' => 'success',
                'categories' => $post
            ];
        } else {
            $data =  [
                'code' => 400,
                'status' => 'error',
                'message' => 'La entrada no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        //recoger datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //conseguiir usuario identificado
            $user = $this->getIdentity($request);
            //validar los datos

            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data =  [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->messages()
                ];
            } else {
                //guardar el articulo
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;

                $post->save();
                //Devolver la respuesta
                $data =  [
                    'code' => 200,
                    'status' => 'success',
                    'message' => $post
                ];
            }
        } else {
            $data =  [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        //recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        // Datos para devolver
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Datos enviados incorrecto'
        );


        if (!empty($params_array)) {
            //validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);
            //var_dump($validate->messages());die();
            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }
            //eliminar lo que no se quiera actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            //Conseguir usuario identificado
            $user = $this->getIdentity($request);

            //buscar el registro
            $post = Post::where('id', $id)
                ->where('user_id', $user->sub)
                ->first();

            if (!empty($post) && is_object($post)) {
                //actualizar el registro en concreto
                $post->update($params_array);


                //devolver algo
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {

        //Conseguir usuario identificado
        $user = $this->getIdentity($request);

        //Conseguir el registro
        $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

        if (!empty($post)) {
            //Borrarlo
            $post->delete();
            //Devolverlo
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'post' => 'El post no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity(Request $request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request)
    {
        //recoger la image de la peticion
        $image = $request->file('file0');
        
        //validar imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
            ]);
            
            //guardar la imagen
            if (!$image || $validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error al subir la imagen'
                ];
            } else {
                $image_name = time() . $image->getClientOriginalName();
                
                \Storage::disk('images')->put($image_name, \File::get($image));
                //var_dump("aqui");die();

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        //devolver datos
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        //comprobar si existe el fichero
        $isset = \Storage::disk('images')->exists($filename);

        if ($isset) {
            //conseguir la imagen
            $file = \Storage::disk('images')->get($filename);
            //devolver la imagen
            return new Response($file, 200);
        } else {
            //mostrar el error
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getPostsByCategory($id)
    {
        $posts = Post::where('category_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function getPostsByUser($id)
    {
        $posts = Post::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
