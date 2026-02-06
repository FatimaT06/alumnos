<?php
require_once "config.php";

$pageTitle = "Grupos";
$current = "grupos_admin";

$grupos = $pdo->query("
  SELECT g.id, g.codigo, g.carrera, g.turno, g.grado, g.activo
  FROM grupos g
  ORDER BY g.codigo ASC
")->fetchAll();

require_once "partials/header.php";
?>

<div class="card card-soft p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Grupos</h4>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Grupo</th>
          <th>Carrera</th>
          <th style="width:140px;">Turno</th>
          <th style="width:100px;">Grado</th>
          <th style="width:120px;">Estado</th>
          <th style="width:200px;" class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($grupos) === 0): ?>
          <tr><td colspan="7" class="text-center text-secondary py-5">Sin registros</td></tr>
        <?php endif; ?>

        <?php foreach ($grupos as $g): ?>
          <?php
            $isActive = (int)$g["activo"] === 1;
            $rowClass = $isActive ? "row-active" : "row-inactive";
          ?>
          <tr class="<?= $rowClass ?>">
            <td class="fw-semibold"><?= (int)$g["id"] ?></td>
            <td><span class="badge text-bg-light border"><?= htmlspecialchars($g["codigo"]) ?></span></td>
            <td><?= htmlspecialchars($g["carrera"]) ?></td>
            <td><?= htmlspecialchars($g["turno"]) ?></td>
            <td><?= (int)$g["grado"] ?></td>
            <td>
              <?php if ($isActive): ?>
                <span class="badge text-bg-success">Activo</span>
              <?php else: ?>
                <span class="badge text-bg-danger">Inactivo</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="icon-btn me-1"
                 href="grupo_estado.php?id=<?= (int)$g["id"] ?>&activo=0"
                 title="Inactivar"
                 onclick="return confirm('¿Inactivar grupo?');"
                 style="border-color: rgba(220,53,69,.35);">
                ❌
              </a>
              <a class="icon-btn"
                 href="grupo_estado.php?id=<?= (int)$g["id"] ?>&activo=1"
                 title="Activar"
                 onclick="return confirm('¿Activar grupo?');"
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
