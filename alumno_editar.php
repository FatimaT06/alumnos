<?php
require_once "config.php";

$pageTitle = "Editar alumno";
$current = "alumnos_lista";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { header("Location: alumnos_lista.php"); exit; }

$grupos = $pdo->query("SELECT id, codigo FROM grupos ORDER BY codigo ASC")->fetchAll();

// Obtener alumno
$stmt = $pdo->prepare("SELECT * FROM alumnos WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();

if (!$alumno) { header("Location: alumnos_lista.php"); exit; }

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nombre = trim($_POST["nombre"] ?? "");
  $apPat  = trim($_POST["ap_paterno"] ?? "");
  $apMat  = trim($_POST["ap_materno"] ?? "");
  $grupoId = (int)($_POST["grupo_id"] ?? 0);

  if ($nombre==="" || $apPat==="" || $apMat==="" || $grupoId<=0) {
    $error = "Completa todos los campos.";
  } else {
    $up = $pdo->prepare("
      UPDATE alumnos
      SET nombre = ?, ap_paterno = ?, ap_materno = ?, grupo_id = ?
      WHERE id = ?
    ");
    $up->execute([$nombre, $apPat, $apMat, $grupoId, $id]);
    $success = "Cambios guardados.";
    // refrescar datos
    $stmt->execute([$id]);
    $alumno = $stmt->fetch();
  }
}

require_once "partials/header.php";
?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-soft p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="mb-1">Editar alumno</h4>
          <p class="text-secondary mb-0">ID: <b><?= (int)$alumno["id"] ?></b></p>
        </div>
        <a class="btn btn-outline-secondary" href="alumnos_lista.php">Volver</a>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nombre(s)</label>
          <input class="form-control" name="nombre" value="<?= htmlspecialchars($alumno["nombre"]) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Apellido paterno</label>
          <input class="form-control" name="ap_paterno" value="<?= htmlspecialchars($alumno["ap_paterno"]) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Apellido materno</label>
          <input class="form-control" name="ap_materno" value="<?= htmlspecialchars($alumno["ap_materno"]) ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Grupo</label>
          <select class="form-select" name="grupo_id" required>
            <option value="" disabled>Selecciona…</option>
            <?php foreach($grupos as $g): ?>
              <option value="<?= (int)$g["id"] ?>" <?= ((int)$alumno["grupo_id"] === (int)$g["id"]) ? "selected" : "" ?>>
                <?= htmlspecialchars($g["codigo"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4">Guardar</button>
          <a class="btn btn-outline-danger" href="alumno_eliminar.php?id=<?= (int)$alumno["id"] ?>"
             onclick="return confirm('¿Seguro que quieres eliminar este alumno?');">
            Eliminar
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card card-soft p-4">
      <h5 class="mb-2">Estado</h5>
      <p class="text-secondary mb-3">Puedes activarlo o desactivarlo desde la lista de alumnos.</p>
      <?php if ((int)$alumno["activo"] === 1): ?>
        <div class="alert alert-success mb-0">Actualmente está <b>Activo</b>.</div>
      <?php else: ?>
        <div class="alert alert-danger mb-0">Actualmente está <b>Inactivo</b>.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once "partials/footer.php"; ?>
