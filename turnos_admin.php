<?php
require_once "config.php";

$pageTitle = "Turnos";
$current = "turnos_admin";

$turnos = $pdo->query("
  SELECT id, nombre, inicial, activo
  FROM turnos
  ORDER BY nombre ASC
")->fetchAll();

require_once "partials/header.php";
?>

<div class="card card-soft p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Turnos</h4>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover">
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
          <tr><td colspan="5" class="text-center text-secondary py-5">Sin registros</td></tr>
        <?php endif; ?>

        <?php foreach ($turnos as $t): ?>
          <?php
            $isActive = (int)$t["activo"] === 1;
            $rowClass = $isActive ? "row-active" : "row-inactive";
          ?>
          <tr class="<?= $rowClass ?>">
            <td class="fw-semibold"><?= (int)$t["id"] ?></td>
            <td><?= htmlspecialchars($t["nombre"]) ?></td>
            <td><span class="badge text-bg-light border"><?= htmlspecialchars($t["inicial"]) ?></span></td>
            <td>
              <?php if ($isActive): ?>
                <span class="badge text-bg-success">Activo</span>
              <?php else: ?>
                <span class="badge text-bg-danger">Inactivo</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="icon-btn me-1"
                 href="turno_estado.php?id=<?= (int)$t["id"] ?>&activo=0"
                 title="Inactivar"
                 onclick="return confirm('¿Inactivar turno?');"
                 style="border-color: rgba(220,53,69,.35);">
                ❌
              </a>
              <a class="icon-btn"
                 href="turno_estado.php?id=<?= (int)$t["id"] ?>&activo=1"
                 title="Activar"
                 onclick="return confirm('¿Activar turno?');"
                 style="border-color: rgba(25,135,84,.35); font-weight:700;">
                A
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once "partials/footer.php"; ?>
