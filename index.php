<?php
session_start();

require 'config/config.php';
require 'models/User.php';
require 'models/Category.php';
require 'models/Customer.php';
require 'models/Vendor.php';
require 'models/Material.php';
require 'models/Order.php';
require 'models/OrderItem.php';
require 'views/JsonView.php';
require 'controllers/UserController.php';
require 'controllers/CategoryController.php';
require 'controllers/CustomerController.php';
require 'controllers/VendorController.php';
require 'controllers/MaterialController.php';
require 'controllers/OrderController.php';
require 'controllers/OrderItemController.php';

$database = new Database();
$userController = new UserController($database);
$categoryController = new CategoryController($database);
$customerController = new CustomerController($database);
$vendorController = new VendorController($database);
$materialController = new MaterialController($database);
$orderController = new OrderController($database);
$orderItemController = new OrderItemController($database);

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
        } elseif ($endpoint === 'customer' && !$id) {
            $customerController->index();
        } elseif ($endpoint === 'customer' && $id) {
            $customerController->show($id);
        } elseif ($endpoint === 'vendor' && !$id) {
            $vendorController->index();
        } elseif ($endpoint === 'vendor' && $id) {
            $vendorController->show($id);
        } elseif ($endpoint === 'material' && !$id) {
            $materialController->index();
        } elseif ($endpoint === 'material' && $id) {
            $materialController->show($id);
        } elseif ($endpoint === 'material-category' && $id) {
            $materialController->indexByCategory($id);
        } elseif ($endpoint === 'order' && !$id) {
            $orderController->index();
        } elseif ($endpoint === 'order' && $id) {
            $orderController->show($id);
        } elseif ($endpoint === 'order-item' && $id) {
            $orderItemController->index($id);
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
        } else if ($endpoint === 'customer') {
            $data = json_decode(file_get_contents('php://input'), true);
            $customerController->store($data);
        } else if ($endpoint === 'vendor') {
            $data = json_decode(file_get_contents('php://input'), true);
            $vendorController->store($data);
        } else if ($endpoint === 'material') {
            $data = json_decode(file_get_contents('php://input'), true);
            $materialController->store($data);
        } elseif ($endpoint === 'order') {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderController->store($data);
        } elseif ($endpoint === 'order-item') {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderItemController->store($data);
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
        } elseif ($endpoint === 'customer') {
            $data = json_decode(file_get_contents('php://input'), true);
            $customerController->update($id, $data);
        } elseif ($endpoint === 'vendor') {
            $data = json_decode(file_get_contents('php://input'), true);
            $vendorController->update($id, $data);
        } elseif ($endpoint === 'material') {
            $data = json_decode(file_get_contents('php://input'), true);
            $materialController->update($id, $data);
        } elseif ($endpoint === 'order' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderController->update($id, $data);
        } elseif ($endpoint === 'order-item' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderItemController->update($id, $data);
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
        } elseif ($endpoint === 'update-username' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->updateUsername($id, $data);
        } elseif ($endpoint === 'update-email' && $id) {
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
        } else if ($endpoint === 'customer' && $id) {
            $customerController->destroy($id);
        } else if ($endpoint === 'vendor' && $id) {
            $vendorController->destroy($id);
        } else if ($endpoint === 'material' && $id) {
            $materialController->destroy($id);
        } elseif ($endpoint === 'order' && $id) {
            $orderController->destroy($id);
        } elseif ($endpoint === 'order-item' && $id) {
            $orderItemController->destroy($id);
        }
        break;

    default:
        JsonView::render(['message' => 'Method not allowed'], 405);
}
