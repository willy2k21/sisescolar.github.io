<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'login-php';

    $conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_error()) {
        exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
    }

    // Validar que el campo 'grado' también se reciba de forma obligatoria
    if (!isset($_POST['grado']) || empty($_POST['grado'])) {
        exit('Error: Debes asignar un curso o grado a la materia.');
    }

    $id = $_POST['id'];
    $codigo_materia = $_POST['codigo_materia'];
    $nombre_materia = $_POST['nombre_materia'];
    $grado = $_POST['grado']; // Captura de la nueva variable enviada por el formulario
    $descripcion = !empty($_POST['descripcion']) ? $_POST['descripcion'] : NULL;

    // CORREGIDO: Añadido 'grado = ?' dentro del comando UPDATE y agregado un parámetro string ('s') extra
    if ($stmt = $conexion->prepare('UPDATE materias SET codigo_materia = ?, nombre_materia = ?, grado = ?, descripcion = ? WHERE id = ?')) {
        // Cuatro strings ('ssss') y un entero ('i') para el ID al final
        $stmt->bind_param('ssssi', $codigo_materia, $nombre_materia, $grado, $descripcion, $id);
        
        if ($stmt->execute()) {
            header('Location: materias.php');
            exit;
        } else {
            echo "Error al actualizar los datos de la materia. Verifique que el código no esté duplicado o que la columna 'grado' exista en la base de datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conexion->error;
    }
    
    mysqli_close($conexion);
} else {
    header('Location: materias.php');
    exit;
}
?>
