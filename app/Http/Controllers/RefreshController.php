<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\TokensSession;
use Illuminate\Http\Request;

class RefreshController extends Controller
{
    public function refresh(Request $request){
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $refreshToken = $jwtAuth->refresh($token);
        return response()->json($refreshToken, 200);
    }

    public function logOut(Request $request)
    {
        $token = $request->header('Authorization', null);
        $tokenSess = TokensSession::query()->where('token', '=', $token)->delete();
        if ($tokenSess) {
            return Response()->json(array('status' => 'success', 'message' => 'Sesión cerrada satisfactoriamente', 'code' => 200), 200);
        } else {
            return Response()->json(array('status' => 'error', 'message' => 'No se pudo cerrar la sesión', 'code' => 400), 200);
        }
    }
}
