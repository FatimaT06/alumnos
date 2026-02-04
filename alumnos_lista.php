<?php
require_once "config.php";

$pageTitle = "Alumnos registrados";
$current = "alumnos_lista";

/* Carreras (mismas del registro de grupos) */
$CAREERS = [
  "Administración de Empresas" => "AE",
  "Administración de Empresas Turísticas" => "AET",
  "Relaciones Internacionales" => "RI",
  "Contaduría Pública y Finanzas" => "CPF",
  "Derecho" => "DER",
  "Mercadotecnia y Publicidad" => "MYP",
  "Gastronomía" => "GAST",
  "Periodismo y Ciencias de la Comunicación" => "PCC",
  "Informática Administrativa y Fiscal" => "IAF",
  "Pedagogía" => "PED",
  "Cultura Física y Educación del Deporte" => "CFED",
  "Idiomas (Inglés y Francés)" => "IF",
  "Diseño Gráfico" => "DG",
  "Diseño de Interiores" => "DINT",
  "Diseño de Modas" => "DMOD",
  "Ingeniería en Sistemas Computacionales" => "ISC",
  "Ingeniería Mecánica Automotriz" => "IMA",
  "Ingeniero Arquitecto" => "IARQ",
  "Ingeniería en Logística y Transporte" => "ILT",
  "Psicología" => "PSI"
];

/* Filtro seleccionado */
$carreraSel = trim($_GET["carrera"] ?? "");
if ($carreraSel !== "" && !array_key_exists($carreraSel, $CAREERS)) {
  $carreraSel = "";
}

/* Query con filtro */
$sql = "
  SELECT 
    a.id,
    CONCAT(a.nombre,' ',a.ap_paterno,' ',a.ap_materno) AS nombre_completo,
    g.codigo AS grupo_codigo,
    g.carrera AS carrera,
    a.activo
  FROM alumnos a
  LEFT JOIN grupos g ON g.id = a.grupo_id
";

$params = [];
if ($carreraSel !== "") {
  $sql .= " WHERE g.carrera = ? ";
  $params[] = $carreraSel;
}

$sql .= " ORDER BY a.id DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$alumnos = $stmt->fetchAll();

require_once "partials/header.php";
?>

<div class="card card-soft p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Alumnos registrados</h4>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary" href="alumnos_registro.php">+ Alumno</a>
      <a class="btn btn-outline-secondary" href="grupos_registro.php">+ Grupo</a>
    </div>
  </div>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-6 col-lg-4">
      <select class="form-select" name="carrera" onchange="this.form.submit()">
        <option value="">Todas las carreras</option>
        <?php foreach ($CAREERS as $nombre => $sigla): ?>
          <option value="<?= htmlspecialchars($nombre) ?>" <?= ($carreraSel === $nombre) ? "selected" : "" ?>>
            <?= htmlspecialchars($nombre) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if ($carreraSel !== ""): ?>
      <div class="col-md-6 col-lg-3">
        <a class="btn btn-outline-secondary w-100" href="alumnos_lista.php">Quitar filtro</a>
      </div>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table class="table align-middle table-hover">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Alumno</th>
          <th style="width:140px;">Grupo</th>
          <th style="width:120px;">Estado</th>
          <th style="width:220px;" class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>

      <?php if (count($alumnos) === 0): ?>
        <tr>
          <td colspan="5" class="text-center text-secondary py-5">
            Sin registros
          </td>
        </tr>
      <?php endif; ?>

      <?php foreach ($alumnos as $a): ?>
        <?php
          $isActive = (int)$a["activo"] === 1;
          $rowClass = $isActive ? "row-active" : "row-inactive";
        ?>
        <tr class="<?= $rowClass ?>">
          <td class="fw-semibold"><?= (int)$a["id"] ?></td>

          <td><?= htmlspecialchars($a["nombre_completo"]) ?></td>

          <td>
            <span class="badge text-bg-light border">
              <?= htmlspecialchars($a["grupo_codigo"] ?? "—") ?>
            </span>
          </td>

          <td>
            <?php if ($isActive): ?>
              <span class="badge text-bg-success">Activo</span>
            <?php else: ?>
              <span class="badge text-bg-danger">Inactivo</span>
            <?php endif; ?>
          </td>

          <td class="text-end">
            <a class="icon-btn me-1"
               href="alumno_editar.php?id=<?= (int)$a["id"] ?>"
               title="Editar">
              ✏️
            </a>

            <a class="icon-btn me-1"
               href="alumno_estado.php?id=<?= (int)$a["id"] ?>&activo=0"
               title="Inactivar"
               onclick="return confirm('¿Inactivar alumno?');"
               style="border-color: rgba(220,53,69,.35);">
              ❌
            </a>

            <a class="icon-btn"
               href="alumno_estado.php?id=<?= (int)$a["id"] ?>&activo=1"
               title="Activar"
               onclick="return confirm('¿Activar alumno?');"
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
