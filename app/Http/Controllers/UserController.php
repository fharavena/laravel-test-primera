<?php

namespace App\Http\Controllers;

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
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
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
        return "Accion de login de usuarios";
    }
}
