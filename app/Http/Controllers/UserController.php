<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Validator;
use \App\User;


class UserController extends Controller
{

    public function pruebas(Request $request)
    {
        return "Accion de pruebas de User-Controller";
    }

    public function register(Request $request)
    {
        //Recoger datos de usuario por post
        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array

        if (!empty($params) && !empty($params_array)) {
            //limpiar datos
            $params_array = array_map('trim', $params_array);

            $rules = array(
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            );

            $validate = Validator::make($params_array, $rules);

            if ($validate->fails()) {
                //Validacion pasada incorrectamente
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors' => $validate->errors()
                );
            } else {
                //cifrar la contraseña
                $pwd = hash('sha256', $params->password);
                //crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //Guardar usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth = new \JwtAuth();

        //recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {
            //limpiar datos
            $params_array = array_map('trim', $params_array);

            $rules = array(
                'email' => 'required|email',
                'password' => 'required'
            );

            $validate = Validator::make($params_array, $rules);

            //var_dump($params_array);die();
            // $validator = Validator::make($params_array, $rules, null);
            // $validate = Validator::make($params_array,[
            //     'email'=>'required|email',
            //     'password' => 'required'
            // ]);

            // $validate = Validator::make($params_array, $rules);
            if ($validate->fails()) {
                //Validacion pasada incorrectamente
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha podido identificar',
                    'errors' => $validate->errors()
                );
            } else {
                //cifrar contraseña
                $pwd = hash('sha256', $params->password);
                //devolver token o datos
                $signup = $jwtAuth->signup($params->email, $pwd);

                if (!empty($params->gettoken)) {
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }
            }
        }else {//envio de json vacio
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            echo "<h1>Login correcto</h1>";
        }else{
            echo "<h1>Login INCORRECTO</h1>";
        }

        die();
    }
}
