<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\UserController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$controller = new UserController();

if (isset($uri[2])) {
    switch ($uri[2]) {
        case 'signup':
            $controller->signup();
            break;
        case 'login':
            $controller->login();
            break;
        case 'songs':
            $controller->getAllSongs();
            break;
        case 'addToPlaylist':
            $controller->addToPlaylist();
            break;
        case 'removeFromPlaylist':
            $controller->removeFromPlaylist();
            break;
        case 'playlist':
            $controller->getPlaylist();
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["message" => "Endpoint not found."]);
            break;
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Endpoint not found."]);
}
