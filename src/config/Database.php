<?php

namespace App\Config;

class Database
{
    /**
     * host
     *
     * @var string
     */
    private $host = 'localhost';
    /**
     * db_name
     *
     * @var string
     */
    private $db_name = 'demo-songs-db';
    /**
     * username
     *
     * @var string
     */
    private $username = 'root';
    /**
     * password
     *
     * @var string
     */
    private $password = '';
    /**
     * conn
     *
     * @var mixed
     */
    public $conn;

    /**
     * getConnection
     *
     * @return void
     */
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (\PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

