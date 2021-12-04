<?php


namespace App\Helpers;


use App\Models\Admin;
use App\Models\Apoderado;
use App\Models\Estudiantes;
use App\Models\TokensFCM;
use App\Models\TokensSession;
use Firebase\JWT\JWT;

class JwtAuth {
    public $key;

    /**
     * JwtAuth constructor.
     * @param $key
     */
    public function __construct() {
        $this->key = '123jasdasm2323423msdasd3n213casdas';
    }


    public function singupAdmin($correo, $password) {
        $admin = Admin::query()->where(array('correo' => $correo, 'password' => $password))->first();

        if (!is_null($admin)) {
            $token = array('sub' => $admin->id, 'correo' => $admin->correo, 'iat' => time(), 'exp' => time() + (7 * 24 * 60 * 60));
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $tokensSession = new TokensSession();
            $tokensSession->idUser = $admin->id;
            $tokensSession->token = $jwt;
            $tokensSession->role = 'admin';
            $tokensSession->save();
            return array('status' => 'success', 'message' => 'Login correcto', 'code' => 200, 'jwt' => $jwt);
        } else {
            return array('status' => 'error', 'message' => 'Login incorrecto', 'code' => 400, 'jwt' => null);
        }
    }

    public function singupApoderado($correo, $password, $tokenFCM) {
        $apoderado = Apoderado::query()->where(array('correo' => $correo, 'password' => $password))->first();

        if (!is_null($apoderado)) {

            $tokens = TokensSession::query()
                ->where('idUser', '=', $apoderado->id)
                ->where('role', '=', 'proxie')
                ->count();

            if (!is_null($tokens) && $tokens >= 2) {
                return array('status' => 'error', 'message' => 'Usted ya inicio sesión en dos dispositivos', 'code' => 400, 'jwt' => null);
            } else {
                $token = array('sub' => $apoderado->id,
                    'correo' => $apoderado->correo,
                    'iat' => time(),
                    'exp' => time() + (7 * 24 * 60 * 60));
                $jwt = JWT::encode($token, $this->key, 'HS256');
                $tokensSession = new TokensSession();
                $tokensSession->idUser = $apoderado->id;
                $tokensSession->token = $jwt;
                $tokensSession->role = 'proxie';
                $tokensSession->save();

                $tokensFcm = new TokensFCM();
                $tokensFcm->idUser = $apoderado->id;
                $tokensFcm->token = $tokenFCM;
                $tokensFcm->role = 'proxie';
                $tokensFcm->save();

                return array('status' => 'success', 'message' => 'Login correcto', 'code' => 200, 'jwt' => $jwt);
            }
        } else {
            return array('status' => 'error', 'message' => 'Login incorrecto', 'code' => 400, 'jwt' => null);
        }
    }

    public function singupEstudiante($correo, $password, $tokenFCM) {
        $estudiante = Estudiantes::query()->where(array('correo' => $correo, 'password' => $password))
            ->first();

        if (!is_null($estudiante)) {
            if ($estudiante->idapoderado == 0) {
                $tokens = TokensSession::query()
                    ->where('idUser', '=', $estudiante->id)
                    ->where('role', '=', 'student')
                    ->count();
                if (!is_null($tokens) && $tokens >= 2) {
                    return array('status' => 'error', 'message' => 'Usted ya inicio sesión en dos dispositivos', 'code' => 400, 'jwt' => null);
                } else {
                    $token = array('sub' => $estudiante->id,
                        'correo' => $estudiante->correo,
                        'iat' => time(),
                        'exp' => time() + (7 * 24 * 60 * 60));
                    $jwt = JWT::encode($token, $this->key, 'HS256');

                    $tokensSession = new TokensSession();
                    $tokensSession->idUser = $estudiante->id;
                    $tokensSession->token = $jwt;
                    $tokensSession->role = 'student';
                    $tokensSession->save();

                    $tokensFcm = new TokensFCM();
                    $tokensFcm->idUser = $estudiante->id;
                    $tokensFcm->token = $tokenFCM;
                    $tokensFcm->role = 'student';
                    $tokensFcm->save();

                    return array('status' => 'success', 'message' => 'Login correcto', 'code' => 200, 'jwt' => $jwt);
                }
            } else {
                return array('status' => 'error', 'message' => 'El estudiante tiene un apoderado asociado', 'code' => 400, 'jwt' => null);
            }
        } else {
            return array('status' => 'error', 'message' => 'Usuario y/o contraseña incorrectos', 'code' => 400, 'jwt' => null);
        }
    }

    public function checkToken($jwt): ?array {
        try {
            $decode = JWT::decode($jwt, $this->key, array('HS256'));
            if (is_object($decode) && isset($decode->sub)) {
                $token = TokensSession::query()->where(array('token' => $jwt))->first();
                if (!is_null($token)) {
                    $today = time();
                    if ($decode->exp <= $today) {
                        return array('status' => 'error', 'message' => 'Token expiro', 'code' => 400);
                    } else {
                        return null;
                    }
                } else {
                    return array('status' => 'error', 'message' => 'Token no encontrado', 'code' => 401);
                }
            } else {
                return array('status' => 'error', 'message' => 'Token invalido', 'code' => 401);
            }
        } catch (\UnexpectedValueException | \DomainException $e) {
            return array('status' => 'error', 'message' => 'Token invalido', 'code' => 401);
        }
    }

    public function refresh($jwt): array {
        try {
            $decode = JWT::decode($jwt, $this->key, array('HS256'));
            if (is_object($decode) && isset($decode->sub)) {
                $token = TokensSession::query()->where(array('token' => $jwt))->first();
                if (!is_null($token)) {
                    $tokenRefresh = array('sub' => $decode->sub,
                        'correo' => $decode->correo,
                        'iat' => time(),
                        'exp' => time() + (7 * 24 * 60 * 60));
                    $jwtRefresh = JWT::encode($tokenRefresh, $this->key, 'HS256');
                    TokensSession::query()->where(array('token' => $jwt))->update(['idUser' => $decode->sub, 'token' => $jwtRefresh]);
                    return array('status' => 'success', 'message' => 'Token refrescado', 'code' => 200, 'jwt' => $jwtRefresh);
                } else {
                    return array('status' => 'error', 'message' => 'Token no encontrado', 'code' => 401, 'jwt' => null);
                }
            } else {
                return array('status' => 'error', 'message' => 'Token invalido', 'code' => 401, 'jwt' => null);

            }
        } catch (\UnexpectedValueException | \DomainException $e) {
            return array('status' => 'error', 'message' => 'Token invalido', 'code' => 401, 'jwt' => null);
        }
    }
}
