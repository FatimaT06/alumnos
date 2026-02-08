<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Config\Database;

class TurnoController {
    
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    // Lista de turnos
    public function admin(Request $request, Response $response) {
        $pdo = Database::getConnection();
        
        $turnos = $pdo->query("
            SELECT id, nombre, inicial, activo
            FROM turnos
            ORDER BY nombre ASC
        ")->fetchAll();
        
        $view = $this->container->get('view');
        return $view($response, 'turnos_admin.php', [
            'pageTitle' => 'Turnos',
            'current' => 'turnos_admin',
            'turnos' => $turnos
        ]);
    }
    
    // Cambiar estado de turno
    public function cambiarEstado(Request $request, Response $response, $args) {
        $pdo = Database::getConnection();
        $id = (int)$args['id'];
        $params = $request->getQueryParams();
        $activo = (int)($params["activo"] ?? -1);
        
        if ($id > 0 && ($activo === 0 || $activo === 1)) {
            $stmt = $pdo->prepare("SELECT nombre FROM turnos WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $t = $stmt->fetch();
            
            if ($t) {
                $pdo->prepare("UPDATE turnos SET activo = ? WHERE id = ?")->execute([$activo, $id]);
                
                if ($activo === 0) {
                    $pdo->prepare("UPDATE grupos SET activo = 0 WHERE turno_id = ?")->execute([$id]);
                }
            }
        }
        
        return $response
            ->withHeader('Location', '/turnos/admin')
            ->withStatus(302);
    }
}