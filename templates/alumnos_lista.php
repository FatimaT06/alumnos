<?php require 'partials/header.php'; ?>

<style>
  tr.row-active > td {
    background-color: #d1e7dd !important;
  }
  tr.row-inactive > td {
    background-color: #fff3cd !important;
  }
  .icon-btn.disabled {
    opacity: .4;
    cursor: not-allowed;
    pointer-events: none;
  }
</style>

<div class="card card-soft p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Alumnos registrados</h4>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary" href="/alumnos/registro">+ Alumno</a>
    </div>
  </div>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-6 col-lg-4">
      <select class="form-select" name="carrera" onchange="this.form.submit()">
        <option value="0">Todas las carreras</option>
        <?php foreach ($carreras as $c): ?>
          <option value="<?= (int)$c["id"] ?>"
            <?= ($carreraSel === (int)$c["id"]) ? "selected" : "" ?>>
            <?= htmlspecialchars($c["nombre"]) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if ($carreraSel > 0): ?>
      <div class="col-md-6 col-lg-3">
        <a class="btn btn-outline-secondary w-100" href="/alumnos/lista">
          Quitar filtro
        </a>
      </div>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Alumno</th>
          <th>Grupo</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($alumnos as $a): ?>
        <?php
          $isActive = (int)$a["activo"] === 1;
          $rowClass = $isActive ? "row-active" : "row-inactive";
        ?>
        <tr class="<?= $rowClass ?>">
          <td><?= (int)$a["id"] ?></td>
          <td><?= htmlspecialchars($a["nombre_completo"]) ?></td>
          <td><?= htmlspecialchars($a["grupo_codigo"] ?? "—") ?></td>
          <td>
            <?= $isActive
              ? '<span class="badge bg-success">Activo</span>'
              : '<span class="badge bg-warning text-dark">Inactivo</span>' ?>
          </td>
          <td class="text-end">
            <a class="icon-btn me-1" href="/alumnos/<?= (int)$a["id"] ?>/editar">✏️</a>

            <?php if ($isActive): ?>
              <a class="icon-btn me-1"
                href="/alumnos/<?= (int)$a["id"] ?>/estado?activo=0"
                onclick="return confirm('¿Inactivar alumno?');">❌</a>
            <?php else: ?>
              <span class="icon-btn disabled me-1">❌</span>
            <?php endif; ?>

            <?php if (!$isActive): ?>
              <a class="icon-btn"
                href="/alumnos/<?= (int)$a["id"] ?>/estado?activo=1"
                onclick="return confirm('¿Activar alumno?');">✅</a>
            <?php else: ?>
              <span class="icon-btn disabled">✅</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'partials/footer.php'; ?>