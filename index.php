<?php
session_start();

require 'config/config.php';
require 'models/User.php';
require 'models/Category.php';
require 'views/JsonView.php';
require 'controllers/UserController.php';
require 'controllers/CategoryController.php';

$database = new Database();
$userController = new UserController($database);
$categoryController = new CategoryController($database);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$endpoint = $requestUri[1] ?? null;
$id = $requestUri[2] ?? null;

// Function to check if authentication is required
function isAuthenticated($endpoint)
{
    $publicEndpoints = [
        'login',
        'reset-password',
        'forgot-password'
    ];

    return !in_array($endpoint, $publicEndpoints) && isset($_SESSION['user_id']);
}

switch ($requestMethod) {
    case 'GET':
        // if (!isAuthenticated($endpoint)) {
        //     JsonView::render(['message' => 'Unauthorized request. Please log in.'], 401);
        //     break;
        // }
        if ($endpoint === 'category' && !$id) {
            $categoryController->index();
        } elseif ($endpoint === 'category' && $id) {
            $categoryController->show($id);
        }
        break;

    case 'POST':
        // if (!isAuthenticated($endpoint)) {
        //     JsonView::render(['message' => 'Unauthorized request. Please log in.'], 401);
        //     break;
        // }
        if ($endpoint === 'users') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->store($data);
        } else if ($endpoint === 'login') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->login($data);
        } else if ($endpoint === 'category') {
            $data = json_decode(file_get_contents('php://input'), true);
            $categoryController->store($data);
        }
        break;

    case 'PUT':
        // if (!isAuthenticated($endpoint)) {
        //     JsonView::render(['message' => 'Unauthorized request. Please log in.'], 401);
        //     break;
        // }
        if ($endpoint === 'category') {
            $data = json_decode(file_get_contents('php://input'), true);
            $categoryController->update($id, $data);
        }
        break;

    case 'PATCH':
        // if (!isAuthenticated($endpoint)) {
        //     JsonView::render(['message' => 'Unauthorized request. Please log in.'], 401);
        //     break;
        // }

        if ($endpoint === 'update-password' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->updatePassword($id, $data);
        } else if ($endpoint === 'update-username' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->updateUsername($id, $data);
        } else if ($endpoint === 'update-email' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->updateEmail($id, $data);
        }
        break;

    case 'DELETE':
        // if (!isAuthenticated($endpoint)) {
        //     JsonView::render(['message' => 'Unauthorized request. Please log in.'], 401);
        //     break;
        // }
        if ($endpoint === 'category' && $id) {
            $categoryController->destroy($id);
        }
        break;

    default:
        JsonView::render(['message' => 'Method not allowed'], 405);
}
