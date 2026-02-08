<?php require 'partials/header.php'; ?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-soft p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="mb-1">Editar alumno</h4>
          <p class="text-secondary mb-0">ID: <b><?= (int)$alumno["id"] ?></b></p>
        </div>
        <a class="btn btn-outline-secondary" href="/alumnos/lista">Volver</a>
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
          <input class="form-control" name="apellido_paterno" value="<?= htmlspecialchars($alumno["apellido_paterno"]) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Apellido materno</label>
          <input class="form-control" name="apellido_materno" value="<?= htmlspecialchars($alumno["apellido_materno"]) ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Grupo</label>
          <select class="form-select" name="grupo_id" required>
            <option value="" disabled>Selecciona…</option>
            <?php foreach($grupos as $g): ?>
              <option value="<?= (int)$g["id"] ?>" <?= ((int)$alumno["grupo_id"] === (int)$g["id"]) ? "selected" : "" ?>>
                <?= htmlspecialchars($g["nombre"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4">Guardar</button>
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

<?php require 'partials/footer.php'; ?>