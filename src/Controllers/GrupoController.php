<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Config\Database;

class GrupoController {
    
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    // Lista de grupos
    public function admin(Request $request, Response $response) {
        $pdo = Database::getConnection();
        
        $grupos = $pdo->query("
            SELECT 
                g.id,
                g.nombre,
                c.nombre AS carrera,
                t.nombre AS turno,
                gr.grado,
                g.activo
            FROM grupos g
            LEFT JOIN carreras c ON c.id = g.carrera_id
            LEFT JOIN turnos t   ON t.id = g.turno_id
            LEFT JOIN grado gr   ON gr.id = g.grado_id
            ORDER BY g.nombre ASC
        ")->fetchAll();
        
        $view = $this->container->get('view');
        return $view($response, 'grupos_admin.php', [
            'pageTitle' => 'Grupos',
            'current' => 'grupos_admin',
            'grupos' => $grupos
        ]);
    }
    
    // Registro de grupos
    public function registro(Request $request, Response $response) {
        $pdo = Database::getConnection();
        
        // Si es una petición AJAX para obtener el siguiente código
        $params = $request->getQueryParams();
        if (isset($params['ajax']) && $params['ajax'] === 'get_next') {
            $carrera_id = (int)($params["carrera_id"] ?? 0);
            $turno_id   = (int)($params["turno_id"] ?? 0);
            $grado_id   = (int)($params["grado_id"] ?? 0);
            
            if (!$carrera_id || !$turno_id || !$grado_id) {
                $response->getBody()->write(json_encode(["error" => "Datos incompletos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    c.abreviatura,
                    g.grado,
                    t.nombre AS turno
                FROM carreras c
                JOIN grado g  ON g.id = ?
                JOIN turnos t ON t.id = ?
                WHERE c.id = ?
            ");
            $stmt->execute([$grado_id, $turno_id, $carrera_id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                $response->getBody()->write(json_encode(["error" => "Datos inválidos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            $sigla = $data["abreviatura"];
            $grado = $data["grado"];
            
            $turnoInicial = match ($data["turno"]) {
                "Matutino"   => "M",
                "Vespertino" => "V",
                default      => "MX"
            };
            
            $stmt = $pdo->prepare("
                SELECT MAX(
                    CAST(
                        SUBSTRING(
                            nombre,
                            LENGTH(?) + LENGTH(?) + 1,
                            2
                        ) AS UNSIGNED
                    )
                )
                FROM grupos
                WHERE carrera_id = ?
                  AND turno_id   = ?
                  AND grado_id   = ?
            ");
            $stmt->execute([
                $sigla,
                (string)$grado,
                $carrera_id,
                $turno_id,
                $grado_id
            ]);
            
            $ultimo     = (int)$stmt->fetchColumn();
            $siguiente  = $ultimo + 1;
            $consec2    = str_pad($siguiente, 2, "0", STR_PAD_LEFT);
            $codigo     = "{$sigla}{$grado}{$consec2}-{$turnoInicial}";
            
            $response->getBody()->write(json_encode(["codigo" => $codigo]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        // Lógica normal de registro
        $success = "";
        $error = "";
        
        $CAREERS = $pdo->query("
            SELECT id, nombre, abreviatura
            FROM carreras
            WHERE activo = 1
            ORDER BY nombre ASC
        ")->fetchAll();
        
        $TURNOS = $pdo->query("
            SELECT id, nombre
            FROM turnos
            WHERE activo = 1
        ")->fetchAll();
        
        $GRADOS = $pdo->query("
            SELECT id, grado
            FROM grado
        ")->fetchAll();
        
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $carrera_id = (int)($data["carrera_id"] ?? 0);
            $turno_id   = (int)($data["turno_id"] ?? 0);
            $grado_id   = (int)($data["grado_id"] ?? 0);
            
            if (!$carrera_id || !$turno_id || !$grado_id) {
                $error = "Todos los campos son obligatorios.";
            } else {
                $stmt = $pdo->prepare("
                    SELECT 
                        c.abreviatura,
                        g.grado,
                        t.nombre AS turno
                    FROM carreras c
                    JOIN grado g  ON g.id = ?
                    JOIN turnos t ON t.id = ?
                    WHERE c.id = ?
                ");
                $stmt->execute([$grado_id, $turno_id, $carrera_id]);
                $dataGrupo = $stmt->fetch();
                
                if (!$dataGrupo) {
                    $error = "Datos inválidos.";
                } else {
                    $sigla = $dataGrupo["abreviatura"];
                    $grado = $dataGrupo["grado"];
                    
                    $turnoInicial = match ($dataGrupo["turno"]) {
                        "Matutino"   => "M",
                        "Vespertino" => "V",
                        default      => "MX"
                    };
                    
                    $stmt = $pdo->prepare("
                        SELECT MAX(
                            CAST(
                                SUBSTRING(
                                    nombre,
                                    LENGTH(?) + LENGTH(?) + 1,
                                    2
                                ) AS UNSIGNED
                            )
                        )
                        FROM grupos
                        WHERE carrera_id = ?
                          AND turno_id   = ?
                          AND grado_id   = ?
                    ");
                    $stmt->execute([
                        $sigla,
                        (string)$grado,
                        $carrera_id,
                        $turno_id,
                        $grado_id
                    ]);
                    
                    $ultimo    = (int)$stmt->fetchColumn();
                    $siguiente = $ultimo + 1;
                    $consec2   = str_pad($siguiente, 2, "0", STR_PAD_LEFT);
                    $codigo    = "{$sigla}{$grado}{$consec2}-{$turnoInicial}";
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO grupos (nombre, carrera_id, turno_id, grado_id)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$codigo, $carrera_id, $turno_id, $grado_id]);
                    
                    $success = "Grupo registrado correctamente: <b>$codigo</b>";
                }
            }
        }
        
        $view = $this->container->get('view');
        return $view($response, 'grupos_registro.php', [
            'pageTitle' => 'Registro de Grupos',
            'current' => 'grupos_registro',
            'CAREERS' => $CAREERS,
            'TURNOS' => $TURNOS,
            'GRADOS' => $GRADOS,
            'success' => $success,
            'error' => $error
        ]);
    }
    
    // Cambiar estado de grupo
    public function cambiarEstado(Request $request, Response $response, $args) {
        $pdo = Database::getConnection();
        $id = (int)$args['id'];
        $params = $request->getQueryParams();
        $activo = (int)($params["activo"] ?? -1);
        
        if ($id > 0 && ($activo === 0 || $activo === 1)) {
            $stmt = $pdo->prepare("UPDATE grupos SET activo = ? WHERE id = ?");
            $stmt->execute([$activo, $id]);
            
            if ($activo === 0) {
                $pdo->prepare("UPDATE alumnos SET activo = 0 WHERE grupo_id = ?")->execute([$id]);
            }
            if ($activo === 1) {
                $pdo->prepare("
                    UPDATE carreras c JOIN grupos g ON g.carrera_id = c.id SET c.activo = 1 WHERE g.id = ?")->execute([$id]);
            }
        }
        
        return $response
            ->withHeader('Location', '/grupos/admin')
            ->withStatus(302);
    }
}