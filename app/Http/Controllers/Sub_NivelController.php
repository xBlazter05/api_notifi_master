<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Sub_Nivel;
use Illuminate\Http\Request;

class Sub_NivelController extends Controller
{
    public function getSubNiveles(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        //recuperamos idNivel
        $idNivel = $request->query('idNivel');
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $subNiveles = Sub_Nivel::query()->where(array('idNivel' => $idNivel))->get();
            //$niveles = Admin::query()->offset(0)->limit(25)->orderBy('created_at','asc')->get();
            //$niveles = Niveles::query()->where(array('name'=>'Preescolar'))->get();
            return response()->json(array('sub_niveles' => $subNiveles, 'status' => 'success', 'message' => 'Sub-Niveles encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function getSubNivel(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        //recuperamos idSubNivel
        $idSubNivel = $request->query('idSubNivel');
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $subNivel = Sub_Nivel::query()->where(array('id' => $idSubNivel))->first();
            return response()->json(array('sub_nivel' => $subNivel, 'status' => 'success', 'message' => 'Sub-Nivel encontrado', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }
}
