<?php

namespace App\Models;

class User {    
    /**
     * conn
     *
     * @var mixed
     */
    private $conn;    
    /**
     * table_name
     *
     * @var string
     */
    private $table_name = "users";    
    /**
     * songs_table
     *
     * @var string
     */
    private $songs_table = "songs";    
    /**
     * playlist_table
     *
     * @var string
     */
    private $playlist_table = "playlist";
    
    /**
     * id
     *
     * @var mixed
     */
    public $id;    
    /**
     * name
     *
     * @var mixed
     */
    public $name;    
    /**
     * email
     *
     * @var mixed
     */
    public $email;    
    /**
     * password
     *
     * @var mixed
     */
    public $password;
    
    /**
     * __construct
     *
     * @param  mixed $db
     * @return void
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * signup
     *
     * @return void
     */
    public function signup() {
        if ($this->isAlreadyExist()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET name = :name, email = :email, password = :password";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash(htmlspecialchars(strip_tags($this->password)), PASSWORD_BCRYPT);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    
    /**
     * login
     *
     * @return void
     */
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (password_verify($this->password, $row['password'])) {
                return [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "email" => $row['email']
                ];
            }
        }

        return false;
    }
    
    /**
     * isAlreadyExist
     *
     * @return void
     */
    public function isAlreadyExist() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
    
    /**
     * getSongs
     *
     * @param  mixed $user_id
     * @return void
     */
    public function getSongs($user_id) {
        $query = "SELECT id, title, artist, album FROM " . $this->songs_table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $songs = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $songs[] = [
                "id" => $row['id'],
                "title" => $row['title'],
                "artist" => $row['artist'],
                "album" => $row['album']
            ];
        }

        return $songs;
    }
    
    /**
     * addToPlaylist
     *
     * @param  mixed $user_id
     * @param  mixed $song_id
     * @return void
     */
    public function addToPlaylist($user_id, $song_id) {
        $query = "INSERT INTO " . $this->playlist_table . " (user_id, song_id) VALUES (:user_id, :song_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':song_id', $song_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    
    /**
     * removeFromPlaylist
     *
     * @param  mixed $user_id
     * @param  mixed $song_id
     * @return void
     */
    public function removeFromPlaylist($user_id, $song_id) {
        $query = "DELETE FROM " . $this->playlist_table . " WHERE user_id = :user_id AND song_id = :song_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':song_id', $song_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
       
}
