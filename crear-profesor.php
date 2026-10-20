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
    <title>Sistema Escolar - Nuevo Profesor</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-profesor.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Profesores</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-chalkboard-teacher"></i> Registrar Nuevo Profesor</h3>
                
                <form action="procesar-profesor.php" method="POST">
                    
                    <div class="form-group">
                        <label for="num_empleado">Número de Empleado / Ficha:</label>
                        <input type="text" id="num_empleado" name="num_empleado" placeholder="Ej: PROF-2026-004" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre del Profesor:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre y Apellidos" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" placeholder="correo@institucion.com" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" placeholder="Ej: 555-5678">
                    </div>

                    <div class="form-group">
                        <label for="especialidad">Especialidad / Materia Dictada:</label>
                        <input type="text" id="especialidad" name="especialidad" placeholder="Ej: Literatura, Física, Química" required>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Profesor</button>
                    <a href="profesores.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
