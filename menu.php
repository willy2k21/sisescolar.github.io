<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Panel de Control</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <!-- Corregido: Ruta al CSS general del menú escolar -->
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Panel de Administración</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="welcome-box">
                <h2>¡Bienvenido al Sistema Escolar, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
                <p>Selecciona una opción del menú lateral para comenzar a gestionar la información de la institución académica.</p>
            </div>
        </div>
    </div>

</body>
</html>
