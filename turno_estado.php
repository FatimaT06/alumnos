<?php
require_once "config.php";

$id = (int)($_GET["id"] ?? 0);
$activo = (int)($_GET["activo"] ?? -1);

if ($id > 0 && ($activo === 0 || $activo === 1)) {

  // Obtener nombre del turno
  $stmt = $pdo->prepare("SELECT nombre FROM turnos WHERE id = ? LIMIT 1");
  $stmt->execute([$id]);
  $t = $stmt->fetch();

  if ($t) {
    $pdo->prepare("UPDATE turnos SET activo = ? WHERE id = ?")->execute([$activo, $id]);

    // Si se INACTIVA turno => inactivar sus grupos
    if ($activo === 0) {
      $pdo->prepare("UPDATE grupos SET activo = 0 WHERE turno = ?")->execute([$t["nombre"]]);
    }
    // Si se ACTIVA turno => NO reactivamos grupos automáticamente
  }
}

header("Location: turnos_admin.php");
exit;
