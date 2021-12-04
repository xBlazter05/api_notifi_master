<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Estudiantes;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class EstudiantesController extends Controller
{
    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();
        //recibir datos
        $json = $request->input('json', null);
        $params = json_decode($json);
        $correo = (!is_null($json) && isset($params->correo)) ? $params->correo : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $token = (!is_null($json) && isset($params->token)) ? $params->token : null;
        if (!is_null($correo) && !is_null($password)) {
            $sigupAdmin = $jwtAuth->singupEstudiante($correo, $password,$token);
            return response()->json($sigupAdmin, 200);
        } else {
            return Response()->json(array('status' => 'error', 'message' => 'Faltan datos', 'code' => 400), 200);
        }
    }

    public function getEstudiantesNoApoderado(Request $request)
    {
        $idSubNivel = $request->query('idSubNivel');
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $estudiantes = Estudiantes::query()->where('idapoderado', '=', 0)
                ->with('apoderado')
                ->where('idSubNivel', '=', $idSubNivel)->get();
            return response()->json(array('estudiantes' => $estudiantes, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function getEstudiantesApoderado(Request $request)
    {
        $idSubNivel = $request->query('idSubNivel');
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $estudiantes = Estudiantes::query()->where('idapoderado', '!=', 0)
                ->with('apoderado')
                ->where('idSubNivel', '=', $idSubNivel)->get();
            return response()->json(array('estudiantes' => $estudiantes, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function getEstudiantesForApoderado(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $decode = JWT::decode($token, '123jasdasm2323423msdasd3n213casdas', array('HS256'));
            $estudiantes = Estudiantes::query()->where('idapoderado', '=', $decode->sub)->get();
            return response()->json(array('estudiantes' => $estudiantes, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function getEstudiante(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $decode = JWT::decode($token, '123jasdasm2323423msdasd3n213casdas', array('HS256'));
            $estudiante = Estudiantes::query()->where('id', '=', $decode->sub)->first();
            return response()->json(array('estudiante' => $estudiante, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function create(Request $request)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            //recibir datos
            $json = $request->input('json', null);
            $params = json_decode($json);
            $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
            $lastname = (!is_null($json) && isset($params->lastname)) ? $params->lastname : null;
            $correo = (!is_null($json) && isset($params->correo)) ? $params->correo : null;
            $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
            $idSubNivel = (!is_null($json) && isset($params->idSubNivel)) ? $params->idSubNivel : null;
            if (!is_null($name) && !is_null($lastname) && !is_null($correo) && !is_null($password) && !is_null($idSubNivel)) {
                $student = new Estudiantes();
                $student->name = $name;
                $student->lastname = $lastname;
                $student->correo = $correo;
                $student->password = $password;
                $student->idSubNivel = $idSubNivel;
                $student->save();
                return Response()->json(array('estudiante' => $student, 'status' => 'success', 'message' => 'Estudiante creado satisfactoriament', 'code' => 200), 200);
            } else {
                return Response()->json(array('status' => 'error', 'message' => 'Faltan datos', 'code' => 400), 200);
            }
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function update(Request $request, $id)
    {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            //recibir datos
            $json = $request->input('json', null);
            $params = json_decode($json);

            $proxie = Estudiantes::query()->where('id', $id)->update([
                'idapoderado' => $params->idapoderado,
                'name' => $params->name,
                'lastname' => $params->lastname,
                'correo' => $params->correo,
                'password' => $params->password,
                'idSubNivel' => $params->idSubNivel,
            ]);
            if ($proxie) {
                return Response()->json(array('estudiante' => $proxie, 'status' => 'success', 'message' => 'estudiante actualizado satisfactoriamente', 'code' => 200), 200);
            } else {
                return Response()->json(array('estudiante' => null, 'status' => 'error', 'message' => 'No se pudo actualizar al estudiante', 'code' => 400), 200);
            }

        } else {
            return response()->json($checkToken, 200);
        }
    }

}
