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

if (mysqli_connect_error()) {
    exit('Fallo en la conexión de MySQL:' . mysqli_connect_error());
}

class PDF_Calificaciones extends FPDF {
    // Encabezado del reporte PDF
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(44, 62, 80); // Color institucional #2c3e50
        $this->Cell(0, 10, utf8_decode('SISTEMA DE CONTROL ESCOLAR'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, utf8_decode('Consolidado Oficial de Calificaciones Rendidas'), 0, 1, 'C');
        $this->Ln(10);
        
        // Cabecera de la tabla incluyendo la columna "Curso" de forma explícita
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 8, utf8_decode('Estudiante'), 1, 0, 'L', true);
        $this->Cell(30, 8, utf8_decode('Curso / Grado'), 1, 0, 'C', true); // Nueva columna
        $this->Cell(45, 8, utf8_decode('Materia'), 1, 0, 'L', true);
        $this->Cell(30, 8, utf8_decode('Periodo'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('Nota'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('Estado'), 1, 1, 'C', true);
    }

    // Pie de página del reporte PDF
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Consulta relacional modificada para incluir el grado/curso del alumno
$query = "SELECT a.nombre AS alumno, a.grado, m.nombre_materia AS materia, c.nota, c.periodo 
          FROM calificaciones c
          INNER JOIN alumnos a ON c.alumno_id = a.id
          INNER JOIN materias m ON c.materia_id = m.id
          ORDER BY a.nombre ASC";
$resultado = mysqli_query($conexion, $query);

// Crear el documento PDF en orientación Vertical (P) y tamaño Carta (Letter)
$pdf = new PDF_Calificaciones('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 51, 51);

// Rellenar las filas con los datos correspondientes
if (mysqli_num_rows($resultado) > 0) {
    while($row = mysqli_fetch_assoc($resultado)) {
        $pdf->Cell(45, 8, utf8_decode($row['alumno']), 1, 0, 'L');
        $pdf->Cell(30, 8, utf8_decode($row['grado']), 1, 0, 'C'); // Imprime el Curso/Grado dinámico
        $pdf->Cell(45, 8, utf8_decode($row['materia']), 1, 0, 'L');
        $pdf->Cell(30, 8, utf8_decode($row['periodo']), 1, 0, 'C');
        
        // 1. Imprimir la nota sin decimales
        $pdf->Cell(20, 8, number_format($row['nota'], 0), 1, 0, 'C');
        
        // 2. Evaluar el estado y cambiar el color únicamente para la última celda
        if ($row['nota'] >= 70) {
            $pdf->SetTextColor(46, 204, 113); // Verde RGB (#2ecc71)
            $estado = 'Aprobado';
        } else {
            $pdf->SetTextColor(231, 76, 60);  // Rojo RGB (#e74c3c)
            $estado = 'Reprobado';
        }

        // 3. Imprimir el estado en su celda independiente
        $pdf->Cell(25, 8, utf8_decode($estado), 1, 1, 'C');

        // 4. Resetear el color del texto a gris oscuro para el inicio de la siguiente fila
        $pdf->SetTextColor(51, 51, 51);
    }
} else {
    $pdf->Cell(195, 10, utf8_decode('No hay calificaciones registradas actualmente en el sistema.'), 1, 1, 'C');
}

// ==========================================
// SECCIÓN DE FIRMA Y SELLO DE RECIBIDO
// ==========================================
$pdf->Ln(15); // Espacio después de terminar la tabla

// Controlar el salto de página: si queda menos de 45mm en la hoja, los pasa a la siguiente hoja de forma limpia.
if ($pdf->GetY() > 220) {
    $pdf->AddPage();
    $pdf->Ln(10);
}

// Guardar la posición Y inicial para alinear los dos bloques paralelamente
$y_inicial = $pdf->GetY();

// --- Bloque Izquierdo: Firma Autorizada ---
$pdf->SetX(20); // Margen desde la izquierda
$pdf->Cell(65, 20, '', 'B', 1, 'C'); // Línea horizontal para firmar (usando el borde 'B' de un cuadro vacío)
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(65, 6, utf8_decode('Firma de la Autoridad'), 0, 1, 'C');
$pdf->SetX(20);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(127, 140, 141);
$pdf->Cell(65, 4, utf8_decode('Control de Estudios / Dirección'), 0, 0, 'C');

// Volver a la posición Y inicial para dibujar el bloque de la derecha
$pdf->SetY($y_inicial);

// --- Bloque Derecho: Sello de Recibido ---
$pdf->SetX(120); // Posicionar hacia la derecha de la página
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(127, 140, 141);
// Recuadro cerrado simulando el espacio físico para estampar el sello o firma de tutor
$pdf->MultiCell(65, 6, utf8_decode("\n\n [ Firma del Tutor ] \n\n ______________________ \n\n Fecha: ____ / ____ / ____ \n\n"), 1, 'C');

mysqli_close($conexion);
// Mostrar el PDF directamente en una nueva pestaña del navegador
$pdf->Output('I', 'Reporte_Calificaciones.pdf');
?>
