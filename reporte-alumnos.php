<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Incluir la librería oficial FPDF que debes descargar
require('fpdf.php');

// Conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'login-php';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

class PDF extends FPDF {
    // Encabezado del reporte PDF
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(44, 62, 80); // Color institucional #2c3e50
        $this->Cell(0, 10, utf8_decode('SISTEMA DE CONTROL ESCOLAR'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, utf8_decode('Reporte Oficial de Alumnos Registrados'), 0, 1, 'C');
        $this->Ln(10);
        
        // Cabecera de la tabla en el PDF
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 8, utf8_decode('Matrícula'), 1, 0, 'C', true);
        $this->Cell(55, 8, utf8_decode('Nombre Completo'), 1, 0, 'L', true);
        $this->Cell(55, 8, utf8_decode('Correo Electrónico'), 1, 0, 'L', true);
        $this->Cell(45, 8, utf8_decode('Grado / Curso'), 1, 1, 'L', true);
    }

    // Pie de página del reporte PDF
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Consultar los datos de la base de datos
$query = "SELECT matricula, nombre, email, grado FROM alumnos";
$resultado = mysqli_query($conexion, $query);

// Crear el documento PDF en orientación Vertical (P) y tamaño Carta (Letter)
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 51, 51);

// Rellenar las filas con los datos de los estudiantes
if (mysqli_num_rows($resultado) > 0) {
    while($row = mysqli_fetch_assoc($resultado)) {
        $pdf->Cell(30, 8, utf8_decode($row['matricula']), 1, 0, 'C');
        $pdf->Cell(55, 8, utf8_decode($row['nombre']), 1, 0, 'L');
        $pdf->Cell(55, 8, utf8_decode($row['email']), 1, 0, 'L');
        $pdf->Cell(45, 8, utf8_decode($row['grado']), 1, 1, 'L');
    }
} else {
    $pdf->Cell(185, 10, utf8_decode('No hay alumnos registrados actualmente.'), 1, 1, 'C');
}

mysqli_close($conexion);
// Mostrar el PDF directamente en el navegador web
$pdf->Output('I', 'Reporte_Alumnos.pdf');
?>
