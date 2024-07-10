<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    /**
     * secret_key
     *
     * @var string
     */
    private $secret_key = "YOUR_SECRET_KEY";

    /**
     * generateToken
     *
     * @param  mixed $data
     * @return void
     */
    public function generateToken($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour
        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $data
        );

        $jwt = JWT::encode($payload, $this->secret_key, 'HS256');
        return $jwt;
    }

    /**
     * decodeToken
     *
     * @param  mixed $jwt
     * @return void
     */
    public function decodeToken($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * getBearerToken
     *
     * @return void
     */
    public function getBearerToken()
    {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            $token = explode(" ", $auth_header);
            return isset($token[1]) ? $token[1] : null;
        }

        return null;
    }
}
