<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\AlumnoController;
use App\Controllers\CarreraController;
use App\Controllers\GrupoController;
use App\Controllers\TurnoController;

require __DIR__ . '/../vendor/autoload.php';

$container = new \DI\Container();

$container->set('view', function() {
    return function($response, $template, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../templates/' . $template;
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    };
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    return $response
        ->withHeader('Location', '/alumnos/registro')
        ->withStatus(302);
});

// Rutas de alumnos
$app->get('/alumnos/lista', [AlumnoController::class, 'lista']);
$app->get('/alumnos/registro', [AlumnoController::class, 'registro']);
$app->post('/alumnos/registro', [AlumnoController::class, 'registro']);
$app->get('/alumnos/{id}/editar', [AlumnoController::class, 'editar']);
$app->post('/alumnos/{id}/editar', [AlumnoController::class, 'editar']);
$app->get('/alumnos/{id}/estado', [AlumnoController::class, 'cambiarEstado']);

// Rutas de carreras
$app->get('/carreras/admin', [CarreraController::class, 'admin']);
$app->get('/carreras/{id}/estado', [CarreraController::class, 'cambiarEstado']);

// Rutas de grupos
$app->get('/grupos/admin', [GrupoController::class, 'admin']);
$app->get('/grupos/registro', [GrupoController::class, 'registro']);
$app->post('/grupos/registro', [GrupoController::class, 'registro']);
$app->get('/grupos/{id}/estado', [GrupoController::class, 'cambiarEstado']);

// Rutas de turnos
$app->get('/turnos/admin', [TurnoController::class, 'admin']);
$app->get('/turnos/{id}/estado', [TurnoController::class, 'cambiarEstado']);

$app->run();