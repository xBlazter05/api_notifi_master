<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Estudiantes;
use App\Models\Notificaciones;
use Illuminate\Http\Request;

define('GOOGLE_API_KEY', 'AAAAtQK7CP4:APA91bEXot7USXGOGwpgTN5E3Pvb9yy6Thx-jIDcPazfrFiDSyq8Ux6EasHF1k4kEO8EE5KuSEz8t4UDy5PrEaBWte_xXC1SVmRlja-tarULZBK_XWVAryXLfyYrJ6-YGQGDlVHBb5LI');

class NotificacionesController extends Controller {
    public function notificacionesAll(Request $request) {
        //recibir datos
        $json = $request->input('json', null);
        $params = json_decode($json);
        $titulo = (!is_null($json) && isset($params->titulo)) ? $params->titulo : null;
        $mensaje = (!is_null($json) && isset($params->mensaje)) ? $params->mensaje : null;
        $date_limit = (!is_null($json) && isset($params->date_limit)) ? $params->date_limit : null;
        if (!is_null($titulo) && !is_null($mensaje) && !is_null($date_limit)) {
            $tokensStudents = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->join('tokensfcm', 'tokensfcm.idUser', '=', 'apoderado.id')
                ->where('estudiantes.idapoderado', '=', 0)
                ->where('tokensfcm.role', '=', 'student')
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante', 'tokensfcm.token as token')
                ->groupBy('apoderado.id','estudiantes.id','tokensfcm.token')
                ->get();
            $tokensProxies = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->join('tokensfcm', 'tokensfcm.idUser', '=', 'apoderado.id')
                ->where('estudiantes.idapoderado', '!=', 0)
                ->where('tokensfcm.role', '=', 'proxie')
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante', 'tokensfcm.token as token')
                ->groupBy('apoderado.id','estudiantes.id','tokensfcm.token')
                ->get();
            foreach ($tokensStudents as $val) {
                Notificaciones::query()->insert([
                    'idapoderado' => $val->idApoderado,
                    'idEstudiante' => $val->idEstudiante,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'date_limit' => $date_limit,
                ]);
                $this->sendNotificaciones($titulo, $val->token, $date_limit, $mensaje);
            }
            foreach ($tokensProxies as $val) {
                Notificaciones::query()->insert([
                    'idapoderado' => $val->idApoderado,
                    'idEstudiante' => $val->idEstudiante,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'date_limit' => $date_limit,
                ]);
                $this->sendNotificaciones($titulo, $val->token, $date_limit, $mensaje);
            }
            return response()->json(array('notiAllS' => $tokensStudents, 'notiAllA' => $tokensProxies, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return Response()->json(array('status' => 'error', 'message' => 'Faltan datos', 'code' => 400), 200);
        }
    }

    public function notificacionesGrades(Request $request, $idSubNivel) {
        //recibir datos
        $json = $request->input('json', null);
        $params = json_decode($json);
        $titulo = (!is_null($json) && isset($params->titulo)) ? $params->titulo : null;
        $mensaje = (!is_null($json) && isset($params->mensaje)) ? $params->mensaje : null;
        $date_limit = (!is_null($json) && isset($params->date_limit)) ? $params->date_limit : null;
        if (!is_null($titulo) && !is_null($mensaje) && !is_null($date_limit)) {
            $students = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->where('estudiantes.idapoderado', '=', 0)
                ->where('estudiantes.idSubNivel', '=', $idSubNivel)
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante')
                ->groupBy('apoderado.id','estudiantes.id')
                ->get();
            $proxies = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->where('estudiantes.idapoderado', '!=', 0)
                ->where('estudiantes.idSubNivel', '=', $idSubNivel)
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante')
                ->groupBy('apoderado.id','estudiantes.id')
                ->get();

            foreach ($students as $val) {
                Notificaciones::query()->insert([
                    'idapoderado' => $val->idApoderado,
                    'idEstudiante' => $val->idEstudiante,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'date_limit' => $date_limit,
                ]);
            }
            foreach ($proxies as $val) {
                Notificaciones::query()->insert([
                    'idapoderado' => $val->idApoderado,
                    'idEstudiante' => $val->idEstudiante,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'date_limit' => $date_limit,
                ]);
            }

            $tokensStudents = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->join('tokensfcm', 'tokensfcm.idUser', '=', 'apoderado.id')
                ->where('estudiantes.idapoderado', '=', 0)
                ->where('estudiantes.idSubNivel', '=', $idSubNivel)
                ->where('tokensfcm.role', '=', 'student')
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante', 'tokensfcm.token as token')
                ->groupBy('apoderado.id','estudiantes.id','tokensfcm.token')
                ->get();
            $tokensProxies = Estudiantes::query()
                ->join('apoderado', 'apoderado.id', '=', 'estudiantes.idapoderado')
                ->join('tokensfcm', 'tokensfcm.idUser', '=', 'apoderado.id')
                ->where('estudiantes.idapoderado', '!=', 0)
                ->where('estudiantes.idSubNivel', '=', $idSubNivel)
                ->where('tokensfcm.role', '=', 'proxie')
                ->select('apoderado.id as idApoderado', 'estudiantes.id as idEstudiante', 'tokensfcm.token as token')
                ->groupBy('apoderado.id','estudiantes.id','tokensfcm.token')
                ->get();
            foreach ($tokensStudents as $val) {

                $this->sendNotificaciones($titulo, $val->token, $date_limit, $mensaje);
            }
            foreach ($tokensProxies as $val) {
                $this->sendNotificaciones($titulo, $val->token, $date_limit, $mensaje);
            }
            return response()->json(array('notiGradeS' => $tokensStudents, 'notiGradeA' => $tokensProxies, 'status' => 'success', 'message' => 'Estudiantes encontrados', 'code' => 200), 200);
        } else {
            return Response()->json(array('status' => 'error', 'message' => 'Faltan datos', 'code' => 400), 200);
        }
    }

    public function getNotificaciones(Request $request) {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        //recuperamos idNivel
        $idEstudiante = $request->query('idEstudiante');
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $notif = Notificaciones::query()
                ->where(array('idEstudiante' => $idEstudiante))
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(array('notificaciones' => $notif, 'status' => 'success', 'message' => 'Notificaciones encontradas', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function getNotificacionesProximas(Request $request) {
        $token = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        //recuperamos idNivel
        $idEstudiante = $request->query('idEstudiante');
        $checkToken = $jwtAuth->checkToken($token);
        if (is_null($checkToken)) {
            $notif = Notificaciones::query()
                ->where(array('idEstudiante' => $idEstudiante))
                ->orderBy('date_limit', 'asc')
                ->get();
            return response()->json(array('notificaciones' => $notif, 'status' => 'success', 'message' => 'Notificaciones encontradas', 'code' => 200), 200);
        } else {
            return response()->json($checkToken, 200);
        }
    }

    public function sendNotificaciones($titulo, $token, $fecha, $mensaje) {
        //print($token);
        ignore_user_abort();
        ob_start();

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array('to' => $token,
            'notification' => array('title' => $titulo, 'body' => $mensaje),
            'data' => array('titulo' => $titulo, 'fecha' => $fecha, 'mensaje' => $mensaje,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'));


        $headers = array(
            'Authorization:key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false)
            die('Curl failed ' . curl_error($ch));
        curl_close($ch);
        //return $result;
    }

}
