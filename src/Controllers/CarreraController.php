<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Config\Database;

class CarreraController {
    
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    // Lista de carreras
    public function admin(Request $request, Response $response) {
        $pdo = Database::getConnection();
        
        $carreras = $pdo->query("
            SELECT id, nombre, abreviatura, activo
            FROM carreras
            ORDER BY nombre ASC
        ")->fetchAll();
        
        $view = $this->container->get('view');
        return $view($response, 'carreras_admin.php', [
            'pageTitle' => 'Carreras',
            'current' => 'carreras_admin',
            'carreras' => $carreras
        ]);
    }
    
    // Cambiar estado de carrera
    public function cambiarEstado(Request $request, Response $response, $args) {
        $pdo = Database::getConnection();
        $id = (int)$args['id'];
        $params = $request->getQueryParams();
        $activo = (int)($params["activo"] ?? -1);
        
        if ($id > 0 && ($activo === 0 || $activo === 1)) {
            $stmt = $pdo->prepare("SELECT nombre FROM carreras WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $c = $stmt->fetch();
            
            if ($c) {
                $pdo->prepare("UPDATE carreras SET activo = ? WHERE id = ?")->execute([$activo, $id]);
                if ($activo === 0) {
                    $pdo->prepare("UPDATE grupos SET activo = 0 WHERE carrera_id = ?")->execute([$id]);
                }
            }
        }
        
        return $response
            ->withHeader('Location', '/carreras/admin')
            ->withStatus(302);
    }
}