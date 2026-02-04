<?php
require_once "config.php";

$id = (int)($_GET["id"] ?? 0);
$activo = (int)($_GET["activo"] ?? -1);

if ($id > 0 && ($activo === 0 || $activo === 1)) {
  $stmt = $pdo->prepare("UPDATE alumnos SET activo = ? WHERE id = ?");
  $stmt->execute([$activo, $id]);
}

header("Location: alumnos_lista.php");
exit;
