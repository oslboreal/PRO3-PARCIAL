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
            /* Punto 2 - */
        case '/login':
            if ($method == 'POST')
                LoginController::Login();
            break;
            /* Punto 1 - */
        case '/registro':
            if ($method == 'POST')
                UserController::Create();

            if ($method == 'GET')
                UserController::GetAll();
            break;
            /* Punto 3  */
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
            /* Punto 4  */
            if ($method == 'POST') {
                if (LoginController::IsInRole('user')) {
                    AutoController::Create();
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }
            /* Punto 6  */
            if ($method == 'GET' && empty($_SERVER['QUERY_STRING'])) {
                // No se especifica para que rol en las consignas, lo habilito para todos.
                if (LoginController::IsInRole('user') || LoginController::IsInRole('admin')) {
                    AutoController::GetEstacionados();
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }

            /* Punto 7  */
            if ($method == 'GET' && !empty($_SERVER['QUERY_STRING'])) {
                // No se especifica para que rol en las consignas, lo habilito para todos.
                if (LoginController::IsInRole('user') || LoginController::IsInRole('admin')) {
                    $queries = array();
                    parse_str($_SERVER['QUERY_STRING'], $queries);
                    AutoController::GetByPatente($queries['patente']);
                } else {
                    echo GenericResponse::obtain(false, 'Unauthorized.');
                }
            }
            break;
            /* Punto 5  */
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

            /* Punto 8 */
        case '/importe':
            if ($method == 'GET') {
                if (LoginController::IsInRole('admin')) {
                    $tipo = explode('/', $path)[2];
                    var_dump($tipo);
                    AutoController::GetTotalByTipo($tipo);
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
