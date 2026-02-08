<?php require 'partials/header.php'; ?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card p-4">
      <h4>Registro de grupos</h4>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-3">

        <div class="col-12">
          <label>Carrera</label>
          <select class="form-select" name="carrera_id" id="carrera" required>
            <option value="">Selecciona…</option>
            <?php foreach ($CAREERS as $c): ?>
              <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label>Turno</label>
          <select class="form-select" name="turno_id" id="turno" required>
            <option value="">Selecciona…</option>
            <?php foreach ($TURNOS as $t): ?>
              <option value="<?= $t['id'] ?>"><?= $t['nombre'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label>Grado</label>
          <select class="form-select" name="grado_id" id="grado" required>
            <option value="">Selecciona…</option>
            <?php foreach ($GRADOS as $g): ?>
              <option value="<?= $g['id'] ?>"><?= $g['grado'] ?>°</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12">
          <label>Vista previa</label>
          <input class="form-control" id="preview" readonly>
        </div>

        <div class="col-12">
          <button class="btn btn-primary">Registrar grupo</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
async function buildPreview(){
  const carrera = document.getElementById('carrera');
  const turno   = document.getElementById('turno');
  const grado   = document.getElementById('grado');
  const preview = document.getElementById('preview');

  if (!carrera.value || !turno.value || !grado.value) {
    preview.value = "";
    return;
  }

  preview.value = "Consultando...";

  try {
    const res = await fetch(
      `/grupos/registro?ajax=get_next&carrera_id=${carrera.value}&turno_id=${turno.value}&grado_id=${grado.value}`
    );
    const data = await res.json();
    preview.value = data.codigo ?? "";
  } catch {
    preview.value = "";
  }
}

['carrera','turno','grado'].forEach(id => {
  document.getElementById(id).addEventListener('change', buildPreview);
});
</script>

<?php require 'partials/footer.php'; ?>