<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Requerir la librería FPDF que ya está en tu carpeta
require('fpdf.php');

// Conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'login-php';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

class PDF_Profesores extends FPDF {
    // Encabezado del reporte de docentes
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(44, 62, 80); // Color #2c3e50 institucional
        $this->Cell(0, 10, utf8_decode('SISTEMA DE CONTROL ESCOLAR'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, utf8_decode('Plantilla Oficial de Profesores y Áreas Académicas'), 0, 1, 'C');
        $this->Ln(10);
        
        // Estilos para la cabecera de la tabla del documento
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(35, 8, utf8_decode('Nº Empleado'), 1, 0, 'C', true);
        $this->Cell(55, 8, utf8_decode('Nombre Completo'), 1, 0, 'L', true);
        $this->Cell(50, 8, utf8_decode('Correo Electrónico'), 1, 0, 'L', true);
        $this->Cell(45, 8, utf8_decode('Especialidad / Área'), 1, 1, 'L', true);
    }

    // Pie de página del documento
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Consultar los datos de la tabla de profesores
$query = "SELECT num_empleado, nombre, email, especialidad FROM profesores";
$resultado = mysqli_query($conexion, $query);

// Crear el objeto PDF en Vertical (P), Milímetros (mm) y Carta (Letter)
$pdf = new PDF_Profesores('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 51, 51);

// Dibujar los registros de los docentes en la tabla del PDF
if (mysqli_num_rows($resultado) > 0) {
    while($row = mysqli_fetch_assoc($resultado)) {
        $pdf->Cell(35, 8, utf8_decode($row['num_empleado']), 1, 0, 'C');
        $pdf->Cell(55, 8, utf8_decode($row['nombre']), 1, 0, 'L');
        $pdf->Cell(50, 8, utf8_decode($row['email']), 1, 0, 'L');
        $pdf->Cell(45, 8, utf8_decode($row['especialidad']), 1, 1, 'L');
    }
} else {
    $pdf->Cell(185, 10, utf8_decode('No hay profesores registrados actualmente.'), 1, 1, 'C');
}

mysqli_close($conexion);
// Renderizar el PDF directamente en la pestaña del navegador web
$pdf->Output('I', 'Reporte_Profesores.pdf');
?>
