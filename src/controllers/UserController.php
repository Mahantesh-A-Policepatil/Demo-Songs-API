<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Helpers\JWTHandler;

class UserController
{
    /**
     * db
     *
     * @var mixed
     */
    private $db;
    /**
     * user
     *
     * @var mixed
     */
    private $user;
    /**
     * jwt
     *
     * @var mixed
     */
    private $jwt;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->jwt = new JWTHandler();
    }

    /**
     * signup
     *
     * @return void
     */
    public function signup()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
            $this->user->name = $data->name;
            $this->user->email = $data->email;
            $this->user->password = $data->password;

            if ($this->user->signup()) {
                http_response_code(200);
                echo json_encode(["message" => "User registered successfully."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "User already exists."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
        }
    }

    /**
     * login
     *
     * @return void
     */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->email) && !empty($data->password)) {
            $this->user->email = $data->email;
            $this->user->password = $data->password;

            $user_data = $this->user->login();

            if ($user_data) {
                $token = $this->jwt->generateToken($user_data);
                http_response_code(200);
                echo json_encode(["message" => "Login successful.", "token" => $token]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid email or password."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
        }
    }

    /**
     * getAllSongs
     *
     * @return void
     */
    public function getAllSongs()
    {
        $token = $this->jwt->getBearerToken();

        if ($token) {
            $user_data = $this->jwt->decodeToken($token);

            if ($user_data) {
                $songs = $this->user->getSongs($user_data['id']);

                http_response_code(200);
                echo json_encode($songs);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid token."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Access denied. Token not provided."]);
        }
    }

    /**
     * addToPlaylist
     *
     * @return void
     */
    public function addToPlaylist()
    {
        $token = $this->jwt->getBearerToken();

        if ($token) {
            $user_data = $this->jwt->decodeToken($token);

            if ($user_data) {
                $data = json_decode(file_get_contents("php://input"));

                if (!empty($data->song_id)) {
                    $song_id = $data->song_id;

                    if ($this->user->addToPlaylist($user_data['id'], $song_id)) {
                        http_response_code(200);
                        echo json_encode(["message" => "Song added to playlist successfully."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Failed to add song to playlist."]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Song ID is required."]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid token."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Access denied. Token not provided."]);
        }
    }

    /**
     * removeFromPlaylist
     *
     * @return void
     */
    public function removeFromPlaylist()
    {
        $token = $this->jwt->getBearerToken();

        if ($token) {
            $user_data = $this->jwt->decodeToken($token);

            if ($user_data) {
                $data = json_decode(file_get_contents("php://input"));

                if (!empty($data->song_id)) {
                    $song_id = $data->song_id;

                    if ($this->user->removeFromPlaylist($user_data['id'], $song_id)) {
                        http_response_code(200);
                        echo json_encode(["message" => "Song removed from playlist successfully."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Failed to remove song from playlist."]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Song ID is required."]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid token."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Access denied. Token not provided."]);
        }
    }    
}
