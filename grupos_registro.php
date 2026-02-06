<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

$pageTitle = "Registro de Grupos";
$current = "grupos_registro";

$success = "";
$error = "";

/* SOLO CARRERAS ACTIVAS */
$carreras = $pdo->query("
  SELECT nombre, sigla
  FROM carreras
  WHERE activo = 1
  ORDER BY nombre ASC
")->fetchAll();

/* SOLO TURNOS ACTIVOS */
$turnos = $pdo->query("
  SELECT nombre, inicial
  FROM turnos
  WHERE activo = 1
  ORDER BY nombre ASC
")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $carrera = trim($_POST["carrera"] ?? "");
  $turno = trim($_POST["turno"] ?? "");
  $grado = (int)($_POST["grado"] ?? 0);

  if ($carrera === "" || $turno === "" || $grado < 1 || $grado > 11) {
    $error = "Verifica los datos.";
  } else {

    // validar carrera activa + obtener sigla
    $stmtC = $pdo->prepare("SELECT sigla FROM carreras WHERE nombre = ? AND activo = 1 LIMIT 1");
    $stmtC->execute([$carrera]);
    $cRow = $stmtC->fetch();

    // validar turno activo + obtener inicial
    $stmtT = $pdo->prepare("SELECT inicial FROM turnos WHERE nombre = ? AND activo = 1 LIMIT 1");
    $stmtT->execute([$turno]);
    $tRow = $stmtT->fetch();

    if (!$cRow || !$tRow) {
      $error = "Carrera o turno inactivo.";
    } else {
      $sigla = $cRow["sigla"];
      $turnoInicial = $tRow["inicial"];

      // Buscar último consecutivo
      $stmt = $pdo->prepare("
        SELECT consecutivo
        FROM grupos
        WHERE carrera_sigla = ? AND grado = ? AND turno = ?
        ORDER BY consecutivo DESC
        LIMIT 1
      ");
      $stmt->execute([$sigla, $grado, $turno]);
      $row = $stmt->fetch();

      $nextConsec = $row ? ((int)$row["consecutivo"] + 1) : 1;
      $consec2 = str_pad((string)$nextConsec, 2, "0", STR_PAD_LEFT);

      $codigo = $sigla . $grado . $consec2 . "-" . $turnoInicial;

      // Insertar grupo (activo = 1)
      $ins = $pdo->prepare("
        INSERT INTO grupos (carrera, carrera_sigla, turno, grado, consecutivo, codigo, activo)
        VALUES (?, ?, ?, ?, ?, ?, 1)
      ");
      $ins->execute([$carrera, $sigla, $turno, $grado, $nextConsec, $codigo]);

      $success = "Grupo registrado: {$codigo}";
    }
  }
}

require_once "partials/header.php";
?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-soft p-4">
      <h4 class="mb-1">Registro de grupos</h4>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-3" id="grupoForm">
        <div class="col-12">
          <label class="form-label">Carrera</label>
          <select class="form-select" name="carrera" id="carrera" required>
            <option value="" selected disabled>Selecciona…</option>
            <?php foreach ($carreras as $c): ?>
              <option value="<?= htmlspecialchars($c["nombre"]) ?>">
                <?= htmlspecialchars($c["nombre"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Turno</label>
          <select class="form-select" name="turno" id="turno" required>
            <option value="" selected disabled>Selecciona…</option>
            <?php foreach ($turnos as $t): ?>
              <option value="<?= htmlspecialchars($t["nombre"]) ?>">
                <?= htmlspecialchars($t["nombre"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Grado (cuatrimestre)</label>
          <select class="form-select" name="grado" id="grado" required>
            <option value="" selected disabled>Selecciona…</option>
            <?php for($i=1;$i<=11;$i++): ?>
              <option value="<?= $i ?>"><?= $i ?>° cuatrimestre</option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Vista previa del grupo</label>
          <input type="text" class="form-control" id="preview" value="—" readonly>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4" type="submit">Registrar grupo</button>
          <a class="btn btn-outline-secondary" href="alumnos_registro.php">Ir a registro de alumnos</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Carreras activas con su sigla (desde BD)
  const careers = <?= json_encode(array_column($carreras, 'sigla', 'nombre'), JSON_UNESCAPED_UNICODE) ?>;

  // Turnos activos con su inicial (desde BD)
  const turnos = <?= json_encode(array_column($turnos, 'inicial', 'nombre'), JSON_UNESCAPED_UNICODE) ?>;

  function buildPreview(){
    const carrera = document.getElementById('carrera').value;
    const turno = document.getElementById('turno').value;
    const grado = document.getElementById('grado').value;
    const preview = document.getElementById('preview');

    if(!carrera || !turno || !grado || !careers[carrera] || !turnos[turno]){
      preview.value = "—";
      return;
    }
    const sigla = careers[carrera];
    const t = turnos[turno];
    preview.value = `${sigla}${grado}01-${t}`;
  }

  ['carrera','turno','grado'].forEach(id=>{
    document.getElementById(id).addEventListener('change', buildPreview);
  });
</script>

<?php require_once "partials/footer.php"; ?>
