<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

Use Illuminate\Support\Facades\DB;
Use App\User;

class UserController extends Controller
{
    public  function register(Request $request){
        //RECOGER POST

        $json = $request->input('json', null);
        $params = json_decode($json);

        $email      = (!is_null($json) && isset($params->email)) ? $params->email: null;
        $name       = (!is_null($json) && isset($params->name)) ? $params->name: null;
        $surname    = (!is_null($json) && isset($params->surname)) ? $params->surname: null;
        $role       = 'ROLE_USER';
        $password   = (!is_null($json) && isset($params->password)) ? $params->password: null;

        if(!is_null($email) && !is_null($password) && !is_null($name)){

            //Crear el Usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //COMPROBAA USUARIO DUPLICADO
            $isset_user = User::where('email', '=', $email)->first();

            if(count((array)$isset_user) == 0){
                //GUARDAR USUARIO
                $user->save();

                $data = [
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'Usuario registrado Correctamente'
                ];
            }else{
                // NO GUARDARLO PORQUE YA EXISTE
                $data = [
                    'status'    => 'error',
                    'code'      => 400,
                    'message'   => 'Usuario duplicado, no puede registrarse'
                ];
            }

        }else{
            $data = [
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Usuario no creado'
            ];
        }

        return response()->json($data, 200);
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        //RECIBIR POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email      = (!is_null($json) && isset($params->email)) ? $params->email: null;
        $password   = (!is_null($json) && isset($params->password)) ? $params->password: null;
        $getToken   = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken: null;

        //CIFRAR LA PASSWORD
        $pwd = hash('sha256', $password);

        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuth->signup($email, $pwd);

        }elseif ($getToken != null){
            $signup = $jwtAuth->signup($email, $pwd, $getToken);

        }else{
            $signup = [
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Envia tus datos por post'
            ];
        }

        return response()->json($signup, 200);

    }
}
