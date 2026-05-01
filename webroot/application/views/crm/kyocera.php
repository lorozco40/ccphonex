<?php
require_once(APPPATH . "views/reportes/fpdf.php");

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(192, 192, 192);
$pdf->SetTextColor(60, 60, 60);
$pdf->SetAutoPageBreak(false);

//$y = 15; // Y inicial
$W = 189; // Ancho completo contenido sin espacio medio 196 total
$H = 240; // Alto total contenido
$X = 10;
$Y = 20;

//Marco
$pdf->Rect($X, $Y, $W, $H);

// Imágenes
$y = 18;

//ENCABEZADO 12
$pdf->SetFont('Arial', 'B', 9.60);
$pdf->Rect($X, $Y, $W, 12);
$pdf->Image(APPPATH . '../assets/img/logo_kyocera.png', $X+12, $Y+1.5, 35);
$pdf->SetY($Y+4);
$pdf->Cell($W, 4, utf8_decode('REPORTE SERVICIO TECNICO'), 0, 0, 'C');

//DATOS DEL CLIENTE
$y = $Y+12;
$pdf->SetFont('Arial', 'B', 5.52);
$pdf->SetY($y);
$pdf->Cell($W, 3, utf8_decode('DATOS DEL CLIENTE'), 1, 1, 'C', 1);
$pdf->SetFont('Arial', '', 5.52);

$y = $Y+15;
$pdf->SetY($y);
$pdf->Cell(140, 3, 'CLIENTE', 1, 0, 'L', 1);
$pdf->SetX($X+140);
$pdf->Cell(49, 3, 'No. REPORTE', 1, 0, 'L', 1);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 4.5, utf8_decode($reg->cliente), 1, 0, 'L', 0);
$pdf->SetX($X+140);
$pdf->Cell(49, 4.5, $reg->id, 1, 0, 'L', 0);

$y += 4.5;
$pdf->SetY($y);
$pdf->Cell(140, 3, 'DIRECCION', 1, 0, 'L', 1);
$pdf->SetX($X+140);
$pdf->Cell(49, 3, 'FECHA Y HORA DE LLAMADA', 1, 0, 'L', 1);

$y += 3;
$pdf->Rect($X, $y, 140, 5);
$pdf->SetXY($X, $y);
$pdf->MultiCell(140, 2.5, utf8_decode($reg->ubicacion.' '.$reg->ciudad.' '.$reg->estado), 0);
$pdf->SetXY($X+140, $y);
$pdf->Cell(49, 5, get_format_time($reg->apertura_iso_8601, 'date').' '.get_format_time($reg->apertura_iso_8601, 'time'), 1, 0, 'L');

$y += 5;
$pdf->SetY($y);
$pdf->Cell(90, 3, 'CONTACTO', 1, 0, 'L', 1);
$pdf->Cell(50, 3, utf8_decode('TELEFONO OFICINA Ó MÓVIL'), 1, 0, 'L', 1);
$pdf->Cell(49, 3, utf8_decode('TECNICO Ó DISTRIBUIDOR ASIGNADO'), 1, 0, 'L', 1);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(90, 5, utf8_decode($reg->nombre_solicitante), 1, 0, 'L');
$pdf->Cell(50, 5, utf8_decode($reg->tel_solicitante), 1, 0, 'L');
$pdf->Cell(49, 5, utf8_decode($reg->asignacion), 1, 0, 'L');

//DATOS DEL EQUIPO
$y +=5;
$pdf->SetFont('Arial', 'B', 5.52);
$pdf->SetY($y);
$pdf->Cell($W, 3, 'DATOS DEL EQUIPO', 1, 1, 'C', 1);
$pdf->SetFont('Arial', '', 5.52);

$y += 3;
$pdf->SetY($y);
$pdf->Cell(90, 3, 'MODELO', 1, 0, 'C', 1);
$pdf->Cell(50, 3, 'SERIE', 1, 0, 'C', 1);
$pdf->Cell(49, 3, 'CONTADOR INICIAL', 1, 0, 'C', 1);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(90, 6.5, utf8_decode($reg->modelo), 1, 0, 'C');
$pdf->Cell(50, 6.5, utf8_decode($reg->serie), 1, 0, 'C');
$pdf->Cell(23, 2, 'B & N', 1, 0, 'C', 1);
$pdf->SetX($X+163);
$pdf->Cell(26, 2, 'COLOR', 1, 0, 'C', 1);
$pdf->Rect($X+140, $y+2, 23, 4.5);
$pdf->Rect($X+163, $y+2, 26, 4.5);

$y += 6.5;
$pdf->SetXY($X, $y);
$pdf->Cell($W, 3, 'FALLA REPORTADA', 1, 0, 'L', 1);
$pdf->Rect($X, $y+3, $W, 8);
$pdf->SetXY($X, $y+3);
$pdf->MultiCell($W, 4, utf8_decode($reg->detalle), 0);

//INFORMACION DEL SERVICIO REALIZADO
$y = $y+11;
$pdf->SetFont('Arial', 'B', 5.52);
$pdf->SetY($y);
$pdf->Cell($W, 3, utf8_decode('INFORMACION DEL SERVICIO REALIZADO'), 1, 1, 'C', 1);
$pdf->SetFont('Arial', '', 5.52);

$y += 3;
$pdf->SetY($y);
$pdf->Cell(90, 3, 'FECHA DE ATENCION', 1, 0, 'C', 1);
$pdf->SetX($X+90);
$pdf->Cell(25, 3, 'HORA DE LLEGADA', 1, 0, 'C', 1);
$pdf->SetX($X+115);
$pdf->Cell(25, 3, 'HORA DE SALIDA', 1, 0, 'C', 1);
$pdf->SetX($X+140);
$pdf->Cell(25, 3, 'TIEMPO ADMINISTRATIVO', 1, 0, 'C', 1);
$pdf->SetX($X+165);
$pdf->Cell(24, 3, 'TIEMPO DE TRASLADO', 1, 0, 'C', 1);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(90, 4.5, get_format_time($reg->fecha_llegada,'date'), 1, 0, 'C');
$pdf->Cell(25, 4.5, get_format_time($reg->fecha_llegada, 'time'), 1, 0, 'C');
$pdf->Cell(25, 4.5, get_format_time($reg->fecha_salida, 'time'), 1, 0, 'C');
$pdf->Cell(25, 4.5, '', 1, 0, 'C');
$pdf->Cell(24, 4.5, '', 1, 0, 'C');

$y += 4.5;
$pdf->SetXY($X, $y);
$pdf->Cell($W, 3, 'PROBLEMA ENCONTRADO', 1, 0, 'L', 1);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->MultiCell($W, 4, utf8_decode($reg->tipo_del_problema), 0);

$y += 8;
$pdf->SetY($y);
$pdf->Cell(140, 3, utf8_decode('ACCION REALIZADA PARA LA REPARACIÓN'), 1, 0, 'L', 1);
$pdf->SetX($X+140);
$pdf->Cell(49, 3, 'CONTADOR FINAL', 1, 0, 'C', 1);
$pdf->Rect($X, $y+3, 140, 14.5);
$pdf->SetXY($X, $y+3);
$pdf->MultiCell(140, 3.6, regkey($reg, 'acciones_realizadas'), 0);

$pdf->SetY($y+3);
$pdf->SetX($X+140);
$pdf->Cell(23, 3, 'B & N', 1, 0, 'C', 1);
$pdf->SetX($X+163);
$pdf->Cell(26, 3, 'COLOR', 1, 0, 'C', 1);
$pdf->Rect($X+140, $y+6, 23, 11.5);
$pdf->Rect($X+163, $y+6, 26, 11.5);

$y += 17.5;
$pdf->SetXY($X, $y);
$pdf->Cell(45, 3, 'No. PARTE', 1, 0, 'C', 1);
$pdf->SetX($X+45);
$pdf->Cell(45, 3, 'DESCRIPCION', 1, 0, 'C', 1);
$pdf->SetX($X+90);
$pdf->Cell(25, 3, 'CANT.', 1, 0, 'C', 1);
$pdf->SetX($X+115);
$pdf->Cell(25, 3, 'PROFORMA', 1, 0, 'C', 1);
$pdf->SetX($X+140);
$pdf->Cell(23, 3, 'SERIE ANTERIOR', 1, 0, 'C', 1);
$pdf->SetX($X+163);
$pdf->Cell(26, 3, 'SERIE ACTUAL', 1, 0, 'C', 1);

$y += 3;
for ($i = 0; $i < 10; $i++) {
    if (empty($reg->busqueda_de_articulo[$i])) {
        $pdf->Rect($X,     $y, 45, 4.5);
        $pdf->Rect($X+45,  $y, 45, 4.5);
        $pdf->Rect($X+90,  $y, 25, 4.5);
        $pdf->Rect($X+115, $y, 25, 4.5);
        $pdf->Rect($X+140, $y, 23, 4.5);
        $pdf->Rect($X+163, $y, 26, 4.5);
    } else {
        $pdf->SetXY($X, $y);
        $pdf->Cell(45, 4.5, $reg->busqueda_de_articulo[$i]->no_parte, 1);
        $pdf->Cell(45, 4.5, $reg->busqueda_de_articulo[$i]->descripcion, 1);
        $pdf->Cell(25, 4.5, $reg->busqueda_de_articulo[$i]->cantidad, 1);
        $pdf->Cell(25, 4.5, $reg->busqueda_de_articulo[$i]->proforma, 1);
        $pdf->Cell(23, 4.5, $reg->busqueda_de_articulo[$i]->serie_parte_anterior, 1);
        $pdf->Cell(26, 4.5, $reg->busqueda_de_articulo[$i]->serie_parte_actual, 1);
    }
    $y += 4.5;
}

$pdf->SetXY($X, $y);
$pdf->Cell(140, 3, 'COMENTARIOS:', 0, 0, 'L');
$pdf->Cell(49, 3, 'REGRESA', 1, 0, 'C', 1);
$pdf->Rect($X, $y, 140, 11);
$pdf->SetXY($X+1, $y+3);
$pdf->MultiCell(140, 2.7, regkey($reg,'comentario_servicio'), 0);

$pdf->SetFont('Arial', '', 6.96);
$pdf->SetXY($X+140, $y+3);
$pdf->Multicell(23, 8,'SI', 1,'C');
$pdf->SetXY($X+163, $y+3);
$pdf->Multicell(26, 8,'NO', 1,'C');

$pdf->SetFont('Arial', 'B', 5.16);
$y += 11;
$pdf->SetXY($X, $y);
$pdf->Cell($W, 5, utf8_decode('Con el propósito de continuar mejorando nuestro servicio, agradecemos que evalué la satisfacción del servicio que estamos ofreciendo'), 1, 0, 'C');
$y += 5;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 3, 'Favor de evaluar los criterios del 1 al 5 siendo el 5 el valor mas Alto', 1, 0, 'C');
$pdf->Rect($X+140, $y, 49, 3);

$pdf->SetFont('Arial', 'B', 6.96);
$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 3, 'CRITERIOS', 1, 0, 'C');
$pdf->SetX($X+140);
$pdf->Cell(49, 3, 'PUNTOS', 1, 0, 'C');

$pdf->SetFont('Arial', '', 5.52);

$y += 3;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 6, utf8_decode('1.- ¿Cuándo usted realiza sus llamadas para pedir servicio le atienden con la prontitud y la cortesía esperada?'), 1, 0, 'C');
$pdf->Cell(49, 6, '', 1, 0, 'C');

$y += 6;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 6, utf8_decode('2.- ¿El tiempo de respuesta para atenderle su solicitud de servicio, es el adecuado?'), 1, 0, 'C');
$pdf->Cell(49, 6, '', 1, 0, 'C');

$y += 6;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 6, utf8_decode('3.- ¿Acerca de nuestro Representante de Servicio, es su conducta, presentación e interés por resolver su problema la esperada por ustedes?'), 1, 0, 'C');
$pdf->Cell(49, 6, '', 1, 0, 'C');

$y += 6;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 6, utf8_decode('4.- ¿Cuando el Representante de Servicio se retira de sus oficinas queda el equipo funcionando a su entera satisfacción?'), 1, 0, 'C');
$pdf->Cell(49, 6, '', 1, 0, 'C');

$y += 6;
$pdf->SetXY($X, $y);
$pdf->Cell(140, 6, utf8_decode('5.- ¿En general y tomando en cuenta los criterios anteriores como evalúa usted el servicio de Kyocera?'), 1, 0, 'C');
$pdf->Cell(49, 6, '', 1, 0, 'C');
$y += 6;

$pdf->SetFont('Arial', 'B', 5.52);
$pdf->SetXY($X, $y);
$pdf->Cell(27, 3, 'Comentarios Adicionales :', 0, 0, 'L');
$pdf->Rect($X, $y, $W, 6);
$y += 6;

$pdf->Rect($X, $y, $W, 3);
$y += 3;
$pdf->Rect($X, $y, $W, 3);
$y += 3;
$pdf->Rect($X, $y, $W, 3);
$y += 3;
$pdf->Rect($X, $y, $W, 3);
$y += 12;

$pdf->SetFont('Arial', '', 5.52);
$pdf->SetXY($X, $y);
$pdf->Cell(90, 3, 'NOMBRE Y FIRMA DEL TECNICO', 0, 0, 'C', 1);
$pdf->Line($X, $y, $X+90, $y);
$pdf->SetX($X+115);
$pdf->Cell(74, 3, 'NOMBRE Y FIRMA DEL CLIENTE', 0, 0, 'C', 1);
$pdf->Line($X+115, $y, $X+189, $y);

if (empty($adjuntar)) {
    $pdf->Output('I', 'Informe' . date('Y') . date('m') . '.pdf', true);
} else {
    $attach = $pdf->Output('S');
}

function regkey($row, $key) {
    if( isset($row->$key ) ) {
        return utf8_decode($row->$key);
    } else {
        return '';
    }

}

function get_format_time($fecha_hora = '', $fragmento = '') {
    if( $fragmento == 'date' ) {
        $fecha_hora = substr($fecha_hora, 0, 10);
        if( strlen($fecha_hora) == 10) { // Cambiamos el formato de la fecha de Y-m-d a d-m-Y
            $fecha_hora = substr($fecha_hora, 8, 2).'-'.substr($fecha_hora, 5, 2).'-'.substr($fecha_hora, 0, 4);
        }
    } else if( $fragmento == 'time' ) {
        $fecha_hora = substr($fecha_hora, 11, 5);
    } 

    return $fecha_hora;
}
?>
