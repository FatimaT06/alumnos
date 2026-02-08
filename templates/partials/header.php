<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistema Escolar') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="/css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Sistema Escolar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'alumnos_registro' ? 'active' : '' ?>" 
                        href="/alumnos/registro">Registro Alumnos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'alumnos_lista' ? 'active' : '' ?>" 
                        href="/alumnos/lista">Lista Alumnos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'grupos_registro' ? 'active' : '' ?>" 
                        href="/grupos/registro">Registro Grupos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'grupos_admin' ? 'active' : '' ?>" 
                        href="/grupos/admin">Grupos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'carreras_admin' ? 'active' : '' ?>" 
                        href="/carreras/admin">Carreras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current ?? '') === 'turnos_admin' ? 'active' : '' ?>" 
                        href="/turnos/admin">Turnos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>