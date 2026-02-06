<?php
require_once "config.php";

$pageTitle = "Registro de Alumnos";
$current = "alumnos_registro";

$success = "";
$error = "";

/* Solo grupos válidos: grupo activo + carrera activa + turno activo */
$grupos = $pdo->query("
  SELECT g.id, g.codigo
  FROM grupos g
  INNER JOIN carreras c ON c.nombre = g.carrera AND c.activo = 1
  INNER JOIN turnos t ON t.nombre = g.turno AND t.activo = 1
  WHERE g.activo = 1
  ORDER BY g.codigo ASC
")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nombre = trim($_POST["nombre"] ?? "");
  $apPat  = trim($_POST["ap_paterno"] ?? "");
  $apMat  = trim($_POST["ap_materno"] ?? "");
  $grupoId = (int)($_POST["grupo_id"] ?? 0);

  if (!$nombre || !$apPat || !$apMat || $grupoId <= 0) {
    $error = "Datos inválidos";
  } else {
    // Validar que el grupo siga siendo válido al momento de guardar
    $chk = $pdo->prepare("
      SELECT g.id
      FROM grupos g
      INNER JOIN carreras c ON c.nombre = g.carrera AND c.activo = 1
      INNER JOIN turnos t ON t.nombre = g.turno AND t.activo = 1
      WHERE g.activo = 1 AND g.id = ?
      LIMIT 1
    ");
    $chk->execute([$grupoId]);

    if (!$chk->fetch()) {
      $error = "Grupo no disponible";
    } else {
      $stmt = $pdo->prepare("
        INSERT INTO alumnos (nombre, ap_paterno, ap_materno, grupo_id, activo)
        VALUES (?, ?, ?, ?, 1)
      ");
      $stmt->execute([$nombre, $apPat, $apMat, $grupoId]);
      $success = "Alumno registrado";
    }
  }
}

require_once "partials/header.php";
?>

<div class="card card-soft p-4">
  <h4 class="mb-3">Registro de alumnos</h4>

  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-4">
      <input class="form-control" name="nombre" placeholder="Nombre" required>
    </div>

    <div class="col-md-4">
      <input class="form-control" name="ap_paterno" placeholder="Apellido paterno" required>
    </div>

    <div class="col-md-4">
      <input class="form-control" name="ap_materno" placeholder="Apellido materno" required>
    </div>

    <div class="col-12">
      <select class="form-select" name="grupo_id" required>
        <option value="" disabled selected>Grupo</option>
        <?php foreach ($grupos as $g): ?>
          <option value="<?= (int)$g["id"] ?>"><?= htmlspecialchars($g["codigo"]) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12">
      <button class="btn btn-primary px-4">Registrar</button>
    </div>
  </form>
</div>

<?php require_once "partials/footer.php"; ?>
