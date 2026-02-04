<?php
require_once "config.php";

$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {
  // AJUSTA si tu tabla/PK es diferente
  $stmt = $pdo->prepare("DELETE FROM alumnos WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: alumnos_lista.php");
exit;
