<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Config\Database;

class AlumnoController {
    
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    // Lista de alumnos
    public function lista(Request $request, Response $response) {
        $pdo = Database::getConnection();
        $params = $request->getQueryParams();
        $carreraSel = (int)($params["carrera"] ?? 0);
        
        $carreras = $pdo->query("
            SELECT id, nombre FROM carreras ORDER BY nombre ASC
        ")->fetchAll();
        
        $sql = "
            SELECT 
                a.id,
                CONCAT(a.nombre,' ',a.apellido_paterno,' ',a.apellido_materno) AS nombre_completo,
                g.nombre AS grupo_codigo,
                g.carrera_id,
                a.activo
            FROM alumnos a
            LEFT JOIN grupos g ON g.id = a.grupo_id
        ";
        
        $sqlParams = [];
        if ($carreraSel > 0) {
            $sql .= " WHERE g.carrera_id = ? ";
            $sqlParams[] = $carreraSel;
        }
        
        $sql .= " ORDER BY a.id DESC ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($sqlParams);
        $alumnos = $stmt->fetchAll();
        
        // Llamar a la vista correctamente
        $view = $this->container->get('view');
        return $view($response, 'alumnos_lista.php', [
            'pageTitle' => 'Alumnos registrados',
            'current' => 'alumnos_lista',
            'carreras' => $carreras,
            'alumnos' => $alumnos,
            'carreraSel' => $carreraSel
        ]);
    }
    
    // Registrar alumno
    public function registro(Request $request, Response $response) {
        $pdo = Database::getConnection();
        $success = "";
        $error = "";
        
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $nombre = trim($data["nombre"] ?? "");
            $apPat = trim($data["apellido_paterno"] ?? "");
            $apMat = trim($data["apellido_materno"] ?? "");
            $grupoId = (int)($data["grupo_id"] ?? 0);
            
            if ($nombre === "" || $apPat === "" || $apMat === "" || $grupoId <= 0) {
                $error = "Completa todos los campos.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO alumnos (nombre, apellido_paterno, apellido_materno, grupo_id, activo)
                    VALUES (?, ?, ?, ?, 1)
                ");
                $stmt->execute([$nombre, $apPat, $apMat, $grupoId]);
                $success = "Alumno registrado correctamente.";
            }
        }
        
        $grupos = $pdo->query("
            SELECT id, nombre FROM grupos WHERE activo = 1 ORDER BY nombre ASC
        ")->fetchAll();
        
        // Llamar a la vista correctamente
        $view = $this->container->get('view');
        return $view($response, 'alumnos_registro.php', [
            'pageTitle' => 'Registro de Alumnos',
            'current' => 'alumnos_registro',
            'grupos' => $grupos,
            'success' => $success,
            'error' => $error
        ]);
    }
    
    // Editar alumno
    public function editar(Request $request, Response $response, $args) {
        $pdo = Database::getConnection();
        $id = (int)$args['id'];
        $success = "";
        $error = "";
        
        if ($id <= 0) {
            return $response
                ->withHeader('Location', '/alumnos/lista')
                ->withStatus(302);
        }
        
        $grupos = $pdo->query("SELECT id, nombre FROM grupos ORDER BY nombre ASC")->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE id = ?");
        $stmt->execute([$id]);
        $alumno = $stmt->fetch();
        
        if (!$alumno) {
            return $response
                ->withHeader('Location', '/alumnos/lista')
                ->withStatus(302);
        }
        
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $nombre = trim($data["nombre"] ?? "");
            $apPat = trim($data["apellido_paterno"] ?? "");
            $apMat = trim($data["apellido_materno"] ?? "");
            $grupoId = (int)($data["grupo_id"] ?? 0);
            
            if ($nombre === "" || $apPat === "" || $apMat === "" || $grupoId <= 0) {
                $error = "Completa todos los campos.";
            } else {
                $up = $pdo->prepare("
                    UPDATE alumnos
                    SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, grupo_id = ?
                    WHERE id = ?
                ");
                $up->execute([$nombre, $apPat, $apMat, $grupoId, $id]);
                $success = "Cambios guardados.";
                
                // Recargar datos
                $stmt->execute([$id]);
                $alumno = $stmt->fetch();
            }
        }
        
        // Llamar a la vista correctamente
        $view = $this->container->get('view');
        return $view($response, 'alumno_editar.php', [
            'pageTitle' => 'Editar alumno',
            'current' => 'alumnos_lista',
            'alumno' => $alumno,
            'grupos' => $grupos,
            'success' => $success,
            'error' => $error
        ]);
    }
    
    // Cambiar estado
    public function cambiarEstado(Request $request, Response $response, $args) {
        $pdo = Database::getConnection();
        $id = (int)$args['id'];
        $params = $request->getQueryParams();
        $activo = (int)($params["activo"] ?? -1);
        
        if ($id > 0 && ($activo === 0 || $activo === 1)) {
            $stmt = $pdo->prepare("UPDATE alumnos SET activo = ? WHERE id = ?");
            $stmt->execute([$activo, $id]);
        }
        
        return $response
            ->withHeader('Location', '/alumnos/lista')
            ->withStatus(302);
    }
}