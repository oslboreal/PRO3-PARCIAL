<?php

/* Models */
require_once './Models/Usuario.php';
require_once './Models/Precio.php';
require_once './Models/Auto.php';

/* Controllers */
require_once './Controllers/LoginController.php';
require_once './Controllers/UserController.php';
require_once './Controllers/PrecioController.php';
require_once './Controllers/AutoController.php';

/* Libraries */
require_once './Components/JWT.php';
require_once './Components/GenericResponse.php';
require_once './Components/PassManager.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? 0;

try {
    $fixedPath = explode('/', $path)[1];

    switch ('/' . $fixedPath) {
            /* Punto 2 - Terminado */
        case '/login':
            if ($method == 'POST')
                LoginController::Login();
            break;
            /* Punto 1 - Terminado */
        case '/registro':
            if ($method == 'POST')
                UserController::Create();

            if ($method == 'GET')
                UserController::GetAll();
            break;
            /* Punto 3 - Terminado */
        case '/precio':
            if ($method == 'POST') {
                if (LoginController::IsInRole('admin')) {
                    PrecioController::Create();
                }
            } else {
                echo GenericResponse::obtain(false, 'Unauthorized.');
            }
            break;
        case '/ingreso':
            /* Punto 4 - Terminado */
            if ($method == 'POST') {
                if (LoginController::IsInRole('user')) {
                    AutoController::Create();
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }
            /* Punto 6 */
            if ($method == 'GET') {
                // No especifica para que rol es posible, lo habilito para todos.
                if (LoginController::IsInRole('user') || LoginController::IsInRole('admin')) {
                    AutoController::GetEstacionados();
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }
            break;
            /* Punto 5 - Terminado */
        case '/retiro':
            if ($method == 'GET') {
                if (LoginController::IsInRole('user')) {
                    $patente = explode('/', $path)[2];
                    AutoController::Remove($patente);
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }
            break;

        default:
            echo GenericResponse::obtain(false, 'Invalid Endpoint.');
            break;
    }
} catch (Exception $e) {
    echo GenericResponse::obtain(false, 'Internal Server Error.');
}
