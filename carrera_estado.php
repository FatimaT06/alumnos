<?php
require_once "config.php";

$id = (int)($_GET["id"] ?? 0);
$activo = (int)($_GET["activo"] ?? -1);

if ($id > 0 && ($activo === 0 || $activo === 1)) {

  // Obtener nombre de carrera
  $stmt = $pdo->prepare("SELECT nombre FROM carreras WHERE id = ? LIMIT 1");
  $stmt->execute([$id]);
  $c = $stmt->fetch();

  if ($c) {
    $pdo->prepare("UPDATE carreras SET activo = ? WHERE id = ?")->execute([$activo, $id]);

    // Si se INACTIVA carrera => inactivar sus grupos
    if ($activo === 0) {
      $pdo->prepare("UPDATE grupos SET activo = 0 WHERE carrera = ?")->execute([$c["nombre"]]);
    }
    // Si se ACTIVA carrera => NO reactivamos grupos automáticamente (para no activar de más)
  }
}

header("Location: carreras_admin.php");
exit;
