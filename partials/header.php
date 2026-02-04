<?php
// /partials/header.php
if (!isset($pageTitle)) $pageTitle = "Sistema Escuela";
$current = $current ?? "";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="bg-soft">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Escuela · Control</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link <?= $current==='alumnos_registro'?'active':'' ?>" href="alumnos_registro.php">Registro Alumnos</a></li>
        <li class="nav-item"><a class="nav-link <?= $current==='grupos_registro'?'active':'' ?>" href="grupos_registro.php">Registro Grupos</a></li>
        <li class="nav-item"><a class="nav-link <?= $current==='alumnos_lista'?'active':'' ?>" href="alumnos_lista.php">Alumnos Registrados</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
