<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

$pageTitle = "Registro de Grupos";
$current = "grupos_registro";

$CAREERS = [
  // Sociales y Administrativas
  "Administración de Empresas" => "AE",
  "Administración de Empresas Turísticas" => "AET",
  "Relaciones Internacionales" => "RI",
  "Contaduría Pública y Finanzas" => "CPF",
  "Derecho" => "DER",
  "Mercadotecnia y Publicidad" => "MYP",
  "Gastronomía" => "GAST",
  "Periodismo y Ciencias de la Comunicación" => "PCC",
  "Informática Administrativa y Fiscal" => "IAF",
  // Educación y Humanidades
  "Pedagogía" => "PED",
  "Cultura Física y Educación del Deporte" => "CFED",
  "Idiomas (Inglés y Francés)" => "IF",
  // Ingeniería y Tecnología
  "Diseño Gráfico" => "DG",
  "Diseño de Interiores" => "DINT",
  "Diseño de Modas" => "DMOD",
  "Ingeniería en Sistemas Computacionales" => "ISC",
  "Ingeniería Mecánica Automotriz" => "IMA",
  "Ingeniero Arquitecto" => "IARQ",
  "Ingeniería en Logística y Transporte" => "ILT",
  // Salud
  "Psicología" => "PSI"
];

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $carrera = trim($_POST["carrera"] ?? "");
  $turno = trim($_POST["turno"] ?? "");
  $grado = (int)($_POST["grado"] ?? 0);

  if ($carrera === "" || $turno === "" || $grado < 1 || $grado > 11 || !isset($CAREERS[$carrera])) {
    $error = "Verifica carrera, turno y grado.";
  } else {
    $sigla = $CAREERS[$carrera];
    $turnoInicial = ($turno === "Matutino") ? "M" : "V";

    // Buscar último consecutivo existente para esa combinación.
    // AJUSTA nombre de tabla/columnas si tu BD usa otros.
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

    // Código: SIGLA + GRADO + CONSEC2 + "-" + TurnoInicial
    // Ej: ISC + 8 + 01 + -V  => ISC801-V
    $codigo = $sigla . $grado . $consec2 . "-" . $turnoInicial;

    // Insertar grupo
    $ins = $pdo->prepare("
      INSERT INTO grupos (carrera, carrera_sigla, turno, grado, consecutivo, codigo)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $ins->execute([$carrera, $sigla, $turno, $grado, $nextConsec, $codigo]);

    $success = "Grupo registrado: {$codigo}";
  }
}

require_once "partials/header.php";
?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-soft p-4">
      <h4 class="mb-1">Registro de grupos</h4>
      <p class="text-secondary mb-4">Selecciona carrera, turno y grado. El <b>grupo se genera solo</b> y respeta consecutivos.</p>

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
            <?php foreach ($CAREERS as $name => $sig): ?>
              <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="muted">La sigla se toma de tu catálogo interno (ej. Ingeniería en Sistemas Computacionales → ISC).</small>
        </div>

        <div class="col-md-6">
          <label class="form-label">Turno</label>
          <select class="form-select" name="turno" id="turno" required>
            <option value="" selected disabled>Selecciona…</option>
            <option>Matutino</option>
            <option>Vespertino</option>
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
          <small class="muted">El consecutivo final (01,02,03...) lo confirma el servidor al registrar.</small>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary px-4" type="submit">Registrar grupo</button>
          <a class="btn btn-outline-secondary" href="alumnos_registro.php">Ir a registro de alumnos</a>
        </div>
      </form>
    </div>
  </div>

 

      <hr>

      
    </div>
  </div>
</div>

<script>
  const careers = <?= json_encode($CAREERS, JSON_UNESCAPED_UNICODE) ?>;

  function buildPreview(){
    const carrera = document.getElementById('carrera').value;
    const turno = document.getElementById('turno').value;
    const grado = document.getElementById('grado').value;
    const preview = document.getElementById('preview');

    if(!carrera || !turno || !grado || !careers[carrera]){
      preview.value = "—";
      return;
    }
    const sigla = careers[carrera];
    const t = (turno === "Matutino") ? "M" : "V";
    // El consecutivo real lo calcula el server; aquí mostramos 01 como referencia.
    preview.value = `${sigla}${grado}01-${t}`;
  }

  ['carrera','turno','grado'].forEach(id=>{
    document.getElementById(id).addEventListener('change', buildPreview);
  });
</script>

<?php require_once "partials/footer.php"; ?>
