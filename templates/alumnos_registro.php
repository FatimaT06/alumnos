<?php require 'partials/header.php'; ?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-soft p-4">
      <h4 class="mb-1">Registro de alumnos</h4>
      <p class="text-secondary mb-4">Captura datos del alumno y asigna un grupo existente.</p>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nombre(s)</label>
          <input class="form-control" name="nombre" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Apellido paterno</label>
          <input class="form-control" name="apellido_paterno" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Apellido materno</label>
          <input class="form-control" name="apellido_materno" required>
        </div>

        <div class="col-12">
          <label class="form-label">Grupo</label>
          <select class="form-select" name="grupo_id" required>
            <option value="" selected disabled>Selecciona…</option>
            <?php foreach($grupos as $g): ?>
              <option value="<?= (int)$g["id"] ?>"><?= htmlspecialchars($g["nombre"]) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted">Si no aparece el grupo, primero regístralo en "Registro Grupos".</small>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4">Registrar alumno</button>
          <a class="btn btn-outline-secondary" href="/alumnos/lista">Ver alumnos registrados</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require 'partials/footer.php'; ?>