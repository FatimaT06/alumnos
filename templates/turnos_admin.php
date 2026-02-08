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
    <h4 class="mb-0">Turnos</h4>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Turno</th>
          <th style="width:100px;">Inicial</th>
          <th style="width:120px;">Estado</th>
          <th style="width:200px;" class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php if (count($turnos) === 0): ?>
          <tr>
            <td colspan="5" class="text-center text-secondary py-5">
              Sin registros
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($turnos as $t): ?>
          <?php
            $isActive = (int)$t["activo"] === 1;
            $rowClass = $isActive ? "row-active" : "row-inactive";
          ?>
          <tr class="<?= $rowClass ?>">
            <td class="fw-semibold"><?= (int)$t["id"] ?></td>
            <td><?= htmlspecialchars($t["nombre"]) ?></td>
            <td>
              <span class="badge text-bg-light border">
                <?= htmlspecialchars($t["inicial"]) ?>
              </span>
            </td>
            <td>
              <?= $isActive
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-warning text-dark">Inactivo</span>' ?>
            </td>
            <td class="text-end">
              <?php if ($isActive): ?>
                <a class="icon-btn me-1"
                  href="/turnos/<?= (int)$t["id"] ?>/estado?activo=0"
                  onclick="return confirm('¿Inactivar turno?');">
                  ❌
                </a>
              <?php else: ?>
                <span class="icon-btn disabled me-1">❌</span>
              <?php endif; ?>

              <?php if (!$isActive): ?>
                <a class="icon-btn"
                  href="/turnos/<?= (int)$t["id"] ?>/estado?activo=1"
                  onclick="return confirm('¿Activar turno?');">
                  ✅
                </a>
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