<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Car;


class CarController extends Controller
{
    public  function index(){
        $cars = Car::all()->load('user');
        return response()->json(array(
            'cars' => $cars,
            'status' => 'success'
        ), 200);

    }

    public function show($id){
        $car = Car::find($id);

        if(is_object($car)){
            $car = Car::find($id)->load('user');
            return response()->json(array('car' => $car, 'status' => 'success'),200);
        }else {
            return response()->json(array('message' => 'El coche no existe', 'status' => 'error'),200);
        }

    }

    public function store(Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checktoken = $jwtAuth->checkToken($hash);


        if($checktoken){
            //RECOGER EL DATOS POR POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            // CONSEGUIR EL USUARIO IDENTIFICADO
            $user = $jwtAuth->checkToken($hash,true);

            //VALIDACION
            $validate = \Validator::make($params_array, [
                'title'         => 'required|min:5',
                'description'   => 'required',
                'price'         => 'required',
                'status'        => 'required'
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }

            //GUARDAR EL COCHE}
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->status = $params->status;
            $car->price = $params->price;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );
        }else{
            //DEVOLVER ERROR
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }

        return response()->json($data, 200);

    }

    public function update($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checktoken = $jwtAuth->checkToken($hash);


        if($checktoken){
            //RECOGER PARAMETROS POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //VALIDAR DATOS
            $validate = \Validator::make($params_array, [
                'title'         => 'required|min:5',
                'description'   => 'required',
                'price'         => 'required',
                'status'        => 'required'
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }

            //ACTUALIZAR EL REGISTRO
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $car = Car::where('id', $id)->update($params_array);

            $data = array(
                'car'   => $params,
                'status' =>'success',
                'code'  => 200
            );
        }else{
            //DEVOLVER ERROR
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checktoken = $jwtAuth->checkToken($hash);
        if($checktoken){
            //COMPROBAR QUE EXISTE
            $car = Car::find($id);

            //BORRARLO
            $car->delete();

            //DEVOLVERLO
            $data = array(
                'car'   => $car,
                'status' =>'success',
                'code'  => 200
            );
        }else{
            //DEVOLVER ERROR
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }
} // END CLASS
