<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

require('fpdf.php');

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'login-php';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

class PDF_Materias extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 10, utf8_decode('SISTEMA DE CONTROL ESCOLAR'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, utf8_decode('Plan de Estudios / Asignaturas Vigentes'), 0, 1, 'C');
        $this->Ln(10);
        
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 8, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(45, 8, utf8_decode('Nombre de la Materia'), 1, 0, 'L', true);
        $this->Cell(35, 8, utf8_decode('Curso / Grado'), 1, 0, 'C', true); 
        $this->Cell(75, 8, utf8_decode('Descripción'), 1, 1, 'L', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$query = "SELECT codigo_materia, nombre_materia, grado, descripcion FROM materias ORDER BY grado ASC, nombre_materia ASC";
$resultado = mysqli_query($conexion, $query);

$pdf = new PDF_Materias('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 51, 51);

if (mysqli_num_rows($resultado) > 0) {
    while($row = mysqli_fetch_assoc($resultado)) {
        $pdf->Cell(30, 8, utf8_decode($row['codigo_materia']), 1, 0, 'C');
        $pdf->Cell(45, 8, utf8_decode($row['nombre_materia']), 1, 0, 'L');
        
        // CORRECCIÓN: Si el grado viene vacío, nulo o con espacios, se procesa una alerta clara
        $valor_grado = trim($row['grado']);
        $grado_final = (!empty($valor_grado)) ? $valor_grado : 'Sin Asignar (Editar)';
        
        $pdf->Cell(35, 8, utf8_decode($grado_final), 1, 0, 'C');
        
        $descripcion = (strlen($row['descripcion']) > 40) ? substr($row['descripcion'], 0, 37) . '...' : $row['descripcion'];
        $pdf->Cell(75, 8, utf8_decode($descripcion ?? 'Sin descripción'), 1, 1, 'L');
    }
} else {
    $pdf->Cell(185, 10, utf8_decode('No hay materias dadas de alta actualmente.'), 1, 1, 'C');
}

mysqli_close($conexion);
$pdf->Output('I', 'Reporte_Materias.pdf');
?>
