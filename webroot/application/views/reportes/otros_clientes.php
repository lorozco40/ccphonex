<?php

require_once("fpdf.php");

class PDF extends FPDF
{
    private $logo = APPPATH.'../assets/img/logo.png';

    function __construct() {
        parent::__construct();
        if (file_exists(APPPATH.'../assets/img/logo-tap-inges.png')) {
            $this->logo = APPPATH.'../assets/img/logo-tap-inges.png';
        }
    }

    function Header() {
        $this->Image($this->logo,12,4,55, 20);
    }

    function Footer() {
        $this->SetY(-28);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF('P','mm', 'Letter');
foreach($tickets as $reporte) {
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFillColor(221,221,221);

    $Y = 20;//Controla el punto de inicio de cada contenedor de datos
    $X = 12; //Controla el margen izquierdo del documento
    $W = 184;
    $M = $W / 2;//Mitad
    $B = 0;

    $pdf->SetTextColor(60,60,60);
    $pdf->SetFont('Arial','B',11);
    $pdf->SetxY($X+0,25);
    $pdf->MultiCell(100,6,utf8_decode('REPORTE DE ATENCIÓN Y SOLUCIÓN A FALLAS, PROBLEMAS O DAÑOS.'),$B,'L');

    //========================================================================================================

    //DATOS DEL REPORTE
    $pdf->SetFont('Arial','B',8);
    $pdf->SetxY($X+122,$Y);
    $pdf->MultiCell(62,4,utf8_decode('Datos del Reporte'),1,'C', 1);
    
    $pdf->SetFont('Arial','',8);
    $pdf->SetxY($X+122,$Y+4);
    $pdf->MultiCell(30,4,utf8_decode('Fecha:'),1,'L');

    $pdf->SetxY($X+122,$Y+8);
    $pdf->MultiCell(30,4,utf8_decode('Hora:'),1,'L');

    $pdf->SetxY($X+122,$Y+12);
    $pdf->MultiCell(30,4,utf8_decode('Estatus del reporte:'),1,'L');

    $pdf->SetxY($X+122,$Y+16);
    $pdf->MultiCell(30,4,utf8_decode('Número de reporte:'),1,'L');

    //VALORES
    $pdf->SetxY($X+152,$Y+4);
    $pdf->MultiCell(32,4, $reporte->creacion_f,1,'L');

    $pdf->SetxY($X+152,$Y+8);
    $pdf->MultiCell(32,4, $reporte->creacion_h,1,'L');

    $pdf->SetxY($X+152,$Y+12);
    $pdf->MultiCell(32,4, utf8_decode($reporte->estatus),1,'L');

    $pdf->SetxY($X+152,$Y+16);
    $pdf->MultiCell(32,4, $reporte->id,1,'L');



    $Y = $Y + 22;
    //DATOS DEL EQUIPO
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('Datos del equipo.'),1,'C', 1);

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(30,5,utf8_decode('Cliente:'),1,'L');

    $pdf->SetxY($X+86,$Y+5);
    $pdf->MultiCell(28,5,utf8_decode('Sitio/Ubicación:'),1,'L');

    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(30,10,utf8_decode('Dirección:'),1,'L');

    $pdf->SetxY($X,$Y+20);
    $pdf->MultiCell(30,5,utf8_decode('Marca:'),1,'L');

    $pdf->SetxY($X+68,$Y+20);
    $pdf->MultiCell(18,5,utf8_decode('Modelo:'),1,'L');

    $pdf->SetxY($X+127,$Y+20);
    $pdf->MultiCell(14,5,utf8_decode('Serie:'),1,'L');

    $pdf->SetxY($X,$Y+25);
    $pdf->MultiCell(30,5,utf8_decode('No. de inventario:'),1,'L');

    $pdf->SetxY($X+86,$Y+25);
    $pdf->MultiCell(28,5,utf8_decode('Versión:'),1,'L');

    //VALORES
    $pdf->SetxY($X+30,$Y+5);
    $pdf->MultiCell(56,5, utf8_decode( $reporte->cliente ),1,'L');

    $pdf->SetxY($X+114,$Y+5);
    $pdf->MultiCell(70,5, utf8_decode( $reporte->ubicacion ),1,'L');

    $pdf->SetxY($X+30,$Y+10);
    $pdf->Cell(154,10,'',1);
    $pdf->SetxY($X+30,$Y+10);
    $pdf->MultiCell(154,5, utf8_decode( $reporte->direccion ),0,'L');

    $pdf->SetxY($X+30,$Y+20);
    $pdf->MultiCell(38,5, utf8_decode( $reporte->marca ),1,'L');

    $pdf->SetxY($X+86,$Y+20);
    $pdf->MultiCell(41,5, utf8_decode( $reporte->modelo ),1,'L');

    $pdf->SetxY($X+141,$Y+20);
    $pdf->MultiCell(43,5, utf8_decode( $reporte->no_serie ),1,'L');

    $pdf->SetxY($X+30,$Y+25);
    $pdf->MultiCell(56,5, utf8_decode( $reporte->no_inventario ),1,'L');

    $pdf->SetxY($X+114,$Y+25);
    $pdf->MultiCell(70,5, utf8_decode( $reporte->version ),1,'L');


    $Y = $Y + 36;
    //DATOS DE LA FALLA
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('Datos de la falla.'),1,'C', 1);

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(37,5,utf8_decode('Falla reportada:'),1,'L');

    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(37,5,utf8_decode('Quién reporta la falla:'),1,'L');

    $pdf->SetxY($X+100,$Y+10);
    $pdf->MultiCell(15,5,utf8_decode('Cargo:'),1,'L');

    $pdf->SetxY($X+100,$Y+15);
    $pdf->MultiCell(15,5,utf8_decode('Correo:'),1,'L');

    $pdf->SetxY($X,$Y+15);
    $pdf->MultiCell(37,5,utf8_decode('Tel. celular:'),1,'L');

    //VALORES
    $pdf->SetxY($X+37,$Y+5);
    $pdf->MultiCell(147,5,utf8_decode($reporte->detalle),1,'L');

    $pdf->SetxY($X+37,$Y+10);
    $pdf->MultiCell(63,5,utf8_decode($reporte->nombre_contacto),1,'L');

    $pdf->SetxY($X+115,$Y+10);
    $pdf->MultiCell(69,5,utf8_decode($reporte->cargo),1,'L');

    $pdf->SetxY($X+37,$Y+15);
    $pdf->MultiCell(63,5,utf8_decode( $reporte->telefono ),1,'L');

    $pdf->SetxY($X+115,$Y+15);
    $pdf->MultiCell(69,5,utf8_decode( $reporte->informar ),1,'L');


    $Y = $Y + 26;
    //1ER NIVEL DE SERVICIO
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('1er Nivel de servicio.'),1,'C', 1);

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(45,5,utf8_decode('Fecha/hora de llamada:'),1,'L');

    $pdf->SetxY($X+80,$Y+5);
    $pdf->MultiCell(35,5,utf8_decode('Nombre del técnico:'),1,'L');

    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(45,5,utf8_decode('Se resolvió vía telefónica:'),1,'L');

    $pdf->SetxY($X+45,$Y+10);
    $pdf->MultiCell(9,5,utf8_decode('No'),1,'C');
    $pdf->SetxY($X+54,$Y+10);
    $pdf->MultiCell(8,5,utf8_decode(''),1,'L');
    $pdf->SetxY($X+62,$Y+10);
    $pdf->MultiCell(10,5,utf8_decode('Si'),1,'C');
    $pdf->SetxY($X+72,$Y+10);
    $pdf->MultiCell(8,5,utf8_decode(''),1,'L');

    $pdf->SetxY($X+80,$Y+10);
    $pdf->MultiCell(65,5,utf8_decode('Se atendió en menos de 2 horas:'),1,'L');

    $pdf->SetxY($X+145,$Y+10);
    $pdf->MultiCell(8,5,utf8_decode('No'),1,'C');
    $pdf->SetxY($X+153,$Y+10);
    $pdf->MultiCell(8,5,'',1,'C');
    $pdf->SetxY($X+161,$Y+10);
    $pdf->MultiCell(8,5,utf8_decode('Si'),1,'C');
    $pdf->SetxY($X+169,$Y+10);
    $pdf->MultiCell(15,5,'',1,'C');

    //VALORES
    $pdf->SetxY($X+45,$Y+5);
    $pdf->MultiCell(35,5,$reporte->f_hora_llamada,1,'L');

    $pdf->SetxY($X+115,$Y+5);
    $pdf->MultiCell(69,5,utf8_decode( $reporte->asignar_a ),1,'L');

    if( strtoupper($reporte->resuelto_por_tel) == 'SI') {
        $pdf->Line($X+62,$Y+10,$X+72,$Y+15);
        $pdf->Line($X+62,$Y+15,$X+72,$Y+10);
    }
    if( strtoupper($reporte->resuelto_por_tel) == 'NO') {
        $pdf->Line($X+45,$Y+10,$X+54,$Y+15);
        $pdf->Line($X+45,$Y+15,$X+54,$Y+10);
    }
    if( strtoupper($reporte->atte_menos_2_hrs) == 'SI') {
        $pdf->Line($X+161,$Y+10,$X+169,$Y+15);
        $pdf->Line($X+161,$Y+15,$X+169,$Y+10);
    }
    if( strtoupper($reporte->atte_menos_2_hrs) == 'NO') {
        $pdf->Line($X+145,$Y+10,$X+153,$Y+15);
        $pdf->Line($X+145,$Y+15,$X+153,$Y+10);
    }


    $Y = $Y + 21;
    //2DO NIVEL DE SERVICIO
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('2do Nivel de servicio.'),1,'C', 1);

    $pdf->SetxY($X,$Y+40);
    $pdf->MultiCell($M,5,utf8_decode('Refacciones instaladas:'),1,'C');

    $pdf->SetxY($X+$M,$Y+40);
    $pdf->MultiCell($M,5,utf8_decode('Refacciones retiradas:'),1,'C');

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(38,5,utf8_decode('Fecha/hora de llegada:'),1,'L');

    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(38,5,utf8_decode('Situación en la que se encontró el equipo:'),1,'L');

    $pdf->SetxY($X,$Y+20);
    $pdf->MultiCell(38,5,utf8_decode('Diagnóstico del equipo:'),1,'L');

    $pdf->SetxY($X,$Y+25);
    $pdf->MultiCell(38,15,utf8_decode('Acciones correctivas:'),1,'L');

    //VALORES
    $pdf->SetxY($X+38,$Y+5);
    $pdf->MultiCell(146,5,$reporte->llegada_f.' '.$reporte->llegada_h,1,'L');

    $pdf->SetxY($X+38,$Y+10);
    $pdf->Cell(146,10, '',1);
    $pdf->SetxY($X+38,$Y+10);
    $pdf->MultiCell(146,3.33, utf8_decode( $reporte->como_se_encontro ),0,'L');

    $pdf->SetxY($X+38,$Y+20);
    $pdf->MultiCell(146,5, utf8_decode($reporte->diagnostico),1,'L');

    $pdf->SetxY($X+38,$Y+25);
    $pdf->Cell(146,15, '',1);
    $pdf->SetxY($X+38,$Y+25);
    $pdf->MultiCell(146,5, utf8_decode($reporte->accion_realizada),0,'L');

    $pdf->SetxY($X,$Y+45);
    $pdf->Cell($M,15, '',1);
    $pdf->SetxY($X,$Y+45);
    $pdf->MultiCell($M,5, utf8_decode($reporte->refaccion_instalada),0,'L');

    $pdf->SetxY($X+$M,$Y+45);
    $pdf->Cell($M,15, '',1);
    $pdf->SetxY($X+$M,$Y+45);
    $pdf->MultiCell($M,5, utf8_decode($reporte->refaccion_retirada),0,'L');


    $Y = $Y + 65;
    //2DO NIVEL DE SERVICIO
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('Cierre de reporte.'),1,'C', 1);

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(43,5,utf8_decode('Fecha/hora de reparación:'),1,'L');

    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(43,5,utf8_decode('Tiempo de reparación:'),1,'L');

    $pdf->SetxY($X+99,$Y+10);
    $pdf->MultiCell(43,5,utf8_decode('Cantidad de muestreos:'),1,'L');

    $pdf->SetxY($X,$Y+15);
    $pdf->MultiCell(43,5,utf8_decode('Situación en la que se entrega el equipo:'),1,'L');

    $pdf->SetxY($X,$Y+25);
    $pdf->MultiCell(43,10,utf8_decode('Observaciones:'),1,'L');

    //VALORES
    $pdf->SetxY($X+43,$Y+5);
    $pdf->MultiCell(141,5,$reporte->termino_f.' '.$reporte->termino_h,1,'L');

    $pdf->SetxY($X+43,$Y+10);
    $pdf->MultiCell(56,5, utf8_decode( $reporte->tiempo_reparacion ),1,'L');

    $pdf->SetxY($X+142,$Y+10);
    $pdf->MultiCell(42,5, utf8_decode( $reporte->cantidad_muestreos ),1,'L');

    $pdf->SetxY($X+43,$Y+15);
    $pdf->Cell(141,10, '',1);
    $pdf->SetxY($X+43,$Y+15);
    $pdf->MultiCell(141,5,utf8_decode( $reporte->estatus_final ),0,'L');

    $pdf->SetxY($X+43,$Y+25);
    $pdf->Cell(141,10, '',1);
    $pdf->SetxY($X+43,$Y+25);
    $pdf->MultiCell(141,5,utf8_decode( $reporte->observaciones ),0,'L');


    $Y = $Y + 35;
    //2DO NIVEL DE SERVICIO
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('Conformidad.'),1,'C', 1);

    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell($M,5,utf8_decode('Provetecnia S.A. de C.V.'),1,'C');

    $pdf->SetxY($X+$M,$Y+5);
    $pdf->MultiCell($M,5,utf8_decode('Por parte de la Aduana.'),1,'C');

    $pdf->SetFont('Arial','', 9);
    $pdf->SetxY($X,$Y+10);
    $pdf->MultiCell(18,5,utf8_decode('Nombre:'),1,'L');
    $pdf->SetxY($X+18,$Y+10);
    $pdf->MultiCell($M-18,5,'',1,'L');

    $pdf->SetxY($X,$Y+15);
    $pdf->MultiCell(18,5,utf8_decode('Cargo:'),1,'L');
    $pdf->SetxY($X+18,$Y+15);
    $pdf->MultiCell($M-18,5,'',1,'L');

    $pdf->SetxY($X,$Y+20);
    $pdf->MultiCell(18,5,utf8_decode('Firma:'),1,'L');
    $pdf->SetxY($X+18,$Y+20);
    $pdf->MultiCell($M-18,5,'',1,'L');

    //LADO DERECHO
    $pdf->SetxY($X+$M,$Y+10);
    $pdf->MultiCell(18,5,utf8_decode('Nombre:'),1,'L');
    $pdf->SetxY($X+$M+18,$Y+10);
    $pdf->MultiCell($M-18,5,'',1,'L');

    $pdf->SetxY($X+$M,$Y+15);
    $pdf->MultiCell(18,5,utf8_decode('Cargo:'),1,'L');
    $pdf->SetxY($X+$M+18,$Y+15);
    $pdf->MultiCell($M-18,5,'',1,'L');

    $pdf->SetxY($X+$M,$Y+20);
    $pdf->MultiCell(18,5,utf8_decode('Firma:'),1,'L');
    $pdf->SetxY($X+$M+18,$Y+20);
    $pdf->MultiCell($M-18,5,'',1,'L');


    $pdf->SetTextColor(0,0,255);
    $pdf->SetFont('Arial','', 8);
    $Y = $Y + 35;
    $pdf->SetxY($X,$Y);
    $pdf->MultiCell($W,5,utf8_decode('Av. Antonio No. 200, 5° Piso Col. Ciudad de los Deportes. alcaldía Benito Juárez. CP 03710, CDMX, Tel: 55 56152130, www.provetecnia.com.'),0,'J', 0);
    $pdf->SetTextColor(0,0,0);


    $pdf->Ln(10);
}

$pdf->Output('I', 'Informe'.$ano.$mes.'.pdf', true);
?>
