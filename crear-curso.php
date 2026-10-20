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
    <title>Sistema Escolar - Nuevo Curso</title>
    <link rel="stylesheet" href="https://cloudflare.com" />
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/crear-materia.css"> <!-- Reutiliza los estilos de formulario -->
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="page-title">
                <h2>Gestión de Cursos</h2>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['name']) ?></span> 
            </div>
        </header>

        <div class="container">
            <div class="form-container">
                <h3><i class="fas fa-layer-group"></i> Registrar Nuevo Curso / Grado</h3>
                
                <form action="procesar-curso.php" method="POST">
                    
                    <div class="form-group">
                        <label for="nombre_curso">Nombre del Curso / Grado:</label>
                        <input type="text" id="nombre_curso" name="nombre_curso" placeholder="Ej: 1er Año Bachillerato, 6to Primaria" required>
                    </div>

                    <div class="form-group">
                        <label for="seccion">Sección / Turno (Opcional):</label>
                        <input type="text" id="seccion" name="seccion" placeholder="Ej: Sección A, Turno Matutino">
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Grupo:</label>
                        <textarea id="descripcion" name="descripcion" placeholder="Notas adicionales sobre este grado escolar..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Guardar Curso</button>
                    <a href="cursos.php" class="btn-cancel">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
