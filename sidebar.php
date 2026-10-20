<?php
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

function marcarActivo($archivoObjetivo) {
    $archivoActual = basename($_SERVER['PHP_SELF']);
    return ($archivoActual === $archivoObjetivo) ? 'class="active"' : '';
}
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i> Control Escolar
    </div>
    
    <ul class="sidebar-menu">
        <li <?= marcarActivo('menu.php') ?>>
            <a href="menu.php"><i class="fas fa-home"></i> Panel de Inicio</a>
        </li>
        
        <div class="menu-category">Gestión Escolar</div>
        <li <?= marcarActivo('alumnos.php') ?>>
            <a href="alumnos.php"><i class="fas fa-user-graduate"></i> Alumnos</a>
        </li>
        <li <?= marcarActivo('profesores.php') ?>>
            <a href="profesores.php"><i class="fas fa-chalkboard-teacher"></i> Profesores</a>
        </li>
        
        <!-- NUEVA ESTRUCTURA: Separación de Cursos y Materias -->
        <li <?= marcarActivo('cursos.php') ?>>
            <a href="cursos.php"><i class="fas fa-layer-group"></i> Cursos / Grados</a>
        </li>
        <li <?= marcarActivo('materias.php') ?>>
            <a href="materias.php"><i class="fas fa-book"></i> Materias</a>
        </li>
        
        <li <?= marcarActivo('calificaciones.php') ?>>
            <a href="calificaciones.php"><i class="fas fa-star"></i> Calificaciones</a>
        </li>
        
        <div class="menu-category">Administración</div>
        <li <?= marcarActivo('asistencia.php') ?>>
            <a href="asistencia.php"><i class="fas fa-calendar-check"></i> Asistencias</a>
        </li>
        <li <?= marcarActivo('horarios.php') ?>>
            <a href="horarios.php"><i class="fas fa-clock"></i> Horarios</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="cerrar-sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</div>
