<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Admin;
use App\Models\Niveles;
use Illuminate\Http\Request;

class NivelesController extends Controller
{
    public function getNiveles(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $niveles = Niveles::all();
            //$niveles = Admin::query()->offset(0)->limit(25)->orderBy('created_at','asc')->get();
            //$niveles = Niveles::query()->where(array('name'=>'Preescolar'))->get();
            return response()->json(array('niveles' => $niveles, 'status' => 'success', 'message' => 'Niveles encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }
    public function getNivel(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        //recuperamos idNivel
        $idNivel = $request->query('idNivel');
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $nivel = Niveles::query()->where(array('id' => $idNivel))->first();
            return response()->json(array('nivel' => $nivel, 'status' => 'success', 'message' => 'Nivel encontrado', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }
}
