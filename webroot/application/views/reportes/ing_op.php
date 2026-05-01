<?php

require_once("fpdf.php");

class PDF extends FPDF
{
    private $logo = APPPATH.'../assets/img/logo.png';

    function __construct() {
        parent::__construct();
        if (file_exists(APPPATH.'../assets/img/logo-ing-op.png')) {
            $this->logo = APPPATH.'../assets/img/logo-ing-op.png';
        }
    }

    function Header() {
        $this->Image($this->logo,5,10,125, 25);
    }

    function Footer() {
        $this->SetY(-28);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF('L','mm','Letter');
foreach($tickets as $reporte) {
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFillColor(128,128,128);

    $Y = 46;//Controla el punto de inicio de cada contenedor de datos
    $X = 5; //Controla el margen izquierdo del documento
    $W = 195;
    $M = $W / 2;//Mitad
    $B = 0;

    $pdf->SetFont('Arial','',8);
    $pdf->SetTextColor(0,0,255);
    $pdf->SetxY($X+83,36);
    $pdf->cell(42,5,utf8_decode('mantenimiento3@iosamx.com'),$B,1,'L');

    $pdf->SetTextColor(60,60,60);
    //REPORTE
    //id, fecha_contacto, estado_final
    $pdf->SetFont('Arial','B',12);
    $pdf->SetxY($X+125,16);
    $pdf->MultiCell(70,5,'REPORTE DE MANTENIMIENTO CORRECTIVO',$B,'L');
    $pdf->SetFont('Arial','B',11);
    $pdf->SetxY($X+125,26);
    $pdf->cell(38,5,'Fecha del reporte:',$B,1,'L');
    $pdf->SetxY($X+131,31);
    $pdf->cell(14,5,'Hora:',$B,1,'L');
    $pdf->SetxY($X+125,36);
    $pdf->cell(38,5,'Estatus de la falla:',$B,1,'L');
    $pdf->SetxY($X+125,41);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+163,26);
    $pdf->cell(32,5,$reporte->fecha_contacto_ff,$B,1,'L');
    $pdf->SetxY($X+145,31);
    $pdf->cell(50,5,$reporte->fecha_contacto_h,$B,1,'L');
    $pdf->SetxY($X+163,36);
    $pdf->cell(32,5,utf8_decode($reporte->estado_final),$B,1,'L');
    $pdf->SetxY($X+163,41);
    

    
    //DATOS DEL EQUIPO
    $pdf->SetFont('Arial','B',11);
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,25,'',1,1);
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,'Datos del equipo',$B,1,'C', true);
    $pdf->SetxY($X,$Y+6);
    $pdf->cell(50,5,'Aduana:',$B,1,'L');
    $pdf->SetxY($X,$Y+11);
    $pdf->cell(50,5,utf8_decode('Sitio/Ubicación:'),$B,1,'L');
    $pdf->SetxY($X,$Y+16);
    $pdf->cell(50,5,utf8_decode('Equipo:'),$B,1,'L');
    $pdf->SetxY($X+125,$Y+16);
    $pdf->cell(18,5,utf8_decode('N° Serie:'),$B,1,'L');

    $pdf->Line($X+50,$Y+11,$X+$W,$Y+11);
    $pdf->Line($X+50,$Y+16,$X+$W,$Y+16);
    $pdf->Line($X+50,$Y+21,$X+125,$Y+21);
    $pdf->Line($X+143,$Y+21,$X+$W,$Y+21);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+50,$Y+6);
    $pdf->cell(145,5,utf8_decode($reporte->aduana),0,1,'L');
    $pdf->SetxY($X+50,$Y+11);
    $pdf->cell(145,5,utf8_decode($reporte->ubicacion),0,1,'L');
    $pdf->SetxY($X+50,$Y+16);
    $pdf->cell(145,5,utf8_decode($reporte->marca.' '.$reporte->modelo),0,1,'L');
    $pdf->SetxY($X+125+18,$Y+16);
    $pdf->cell(52,5,utf8_decode($reporte->no_serie),0,1,'L');
    


    //DATOS DE LA FALLA
    //detalle, nom_contacto, cargo, telefono, informar,
    $pdf->SetFont('Arial','B',11);
    $Y += 30;//76
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,54,'',1,1);

    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,'Datos de la falla',1,1,'C', true);
    $Y+=6;
    $pdf->SetxY($X,$Y);
    $pdf->cell(50,10,'Falla reportada:',$B,1,'L');
    $pdf->SetxY($X,$Y+10);
    $pdf->cell(50,5,'Quien reporta la falla:',$B,1,'L');
    $pdf->SetxY($X+125,$Y+10);
    $pdf->cell(18,5,utf8_decode('Cargo:'),$B,1,'C');
    $pdf->SetxY($X,$Y+15);
    $pdf->cell(50,5,utf8_decode('Teléfono/Extensión:'),$B,1,'L');
    $pdf->SetxY($X+125,$Y+15);
    $pdf->cell(18,5,utf8_decode('Correo:'),$B,1,'C');
    //LINEAS
    $pdf->Line($X+50,$Y+5,$X+$W,$Y+5);
    $pdf->Line($X+50,$Y+10,$X+$W,$Y+10);
    $pdf->Line($X+50,$Y+15,$X+125,$Y+15);
    $pdf->Line($X+143,$Y+15,$X+$W,$Y+15);
    $pdf->Line($X+50,$Y+20,$X+125,$Y+20);
    $pdf->Line($X+143,$Y+20,$X+$W,$Y+20);
    $pdf->Line($X+143,$Y+20,$X+$W,$Y+20);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+50,$Y);
    $pdf->MultiCell($W-50,5,utf8_decode($reporte->detalle),$B,'L');
    $pdf->SetxY($X+50,$Y+10);
    $pdf->cell(75,5,utf8_decode($reporte->nom_contacto),$B,1,'L');
    $pdf->SetxY($X+125+18,$Y+10);
    $pdf->cell(52,5,utf8_decode($reporte->cargo),$B,1,'L');
    $pdf->SetxY($X+50,$Y+15);
    $pdf->cell(75,5,utf8_decode($reporte->telefono),$B,1,'L');
    $pdf->SetxY($X+125+18,$Y+15);
    $pdf->cell(52,5,utf8_decode($reporte->informar),$B,1,'L');



    //ATENCIÓN TELEFÓNICA
    $pdf->SetFont('Arial','B',11);
    $Y += 22;//131
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,utf8_decode('Atención telefónica'),0,1,'C', true);
    $Y += 6;
    $pdf->SetxY($X,$Y);
    $pdf->cell(50,9,'Fecha/hora de la llamada:',$B,1,'L');
    $pdf->SetxY($X+103,$Y);
    $pdf->cell(40,9,utf8_decode('Nombre del técnico:'),$B,1,'L');
    $pdf->SetxY($X,$Y+9);
    $pdf->cell(50,9, utf8_decode('Se resolvió vía telefónica:'),$B,1,'L');
    $pdf->SetxY($X+103,$Y+9);
    $pdf->MultiCell(40,4.5,utf8_decode('Se atendió dentro de las 2 horas:'),$B,'C');
    $pdf->SetxY($X+55,$Y+11);
    $pdf->cell(11,6,'Si',1,1,'C');
    $pdf->SetxY($X+74,$Y+11);
    $pdf->cell(11,6,'No',1,1,'C');
    $pdf->SetxY($X+148,$Y+11);
    $pdf->cell(11,6,'Si',1,1,'C');
    $pdf->SetxY($X+166,$Y+11);
    $pdf->cell(11,6,'No',1,1,'C');
    //LINEAS
    $pdf->Line($X+50,$Y+10,$X+103,$Y+10);
    $pdf->Line($X+143,$Y+10,$X+$W,$Y+10);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+50,$Y+2);
    $pdf->cell(53,6,$reporte->f_hora_de_llamada_f.' '.$reporte->f_hora_de_llamada_h,$B,1,'C');
    $pdf->SetxY($X+143,$Y);
    $pdf->MultiCell(52,5,utf8_decode($reporte->ing_contactado),$B,'L');
    if( strtoupper($reporte->resuelto_por_tel) == 'SI') {
        $pdf->Line($X+55,$Y+11,$X+66,$Y+17);
        $pdf->Line($X+55,$Y+17,$X+66,$Y+11);
    }
    if( strtoupper($reporte->resuelto_por_tel) == 'NO') {
        $pdf->Line($X+74,$Y+11,$X+85,$Y+17);
        $pdf->Line($X+74,$Y+17,$X+85,$Y+11);
    }
    if( strtoupper($reporte->atte_menos_2_hrs) == 'SI') {
        $pdf->Line($X+148,$Y+11,$X+159,$Y+17);
        $pdf->Line($X+148,$Y+17,$X+159,$Y+11);
    }
    if( strtoupper($reporte->atte_menos_2_hrs) == 'NO') {
        $pdf->Line($X+166,$Y+11,$X+177,$Y+17);
        $pdf->Line($X+166,$Y+17,$X+177,$Y+11);
    }



    //VISITA EN SITIO
    $pdf->SetFont('Arial','B',11);
    $Y += 21;//131
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,73,'',1,1);
    $pdf->SetxY($X+142,$Y+31);
    $pdf->cell(53,42,'',1,1);

    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,'Visita en sitio',1,1,'C', true);
    $Y+=6;
    $pdf->SetxY($X,$Y);
    $pdf->cell(50,5,'Fecha/hora de llegada:',$B,1,'L');
    $pdf->SetxY($X,$Y+5);
    $pdf->MultiCell(50,5,utf8_decode('Diagnóstico
    (Causa del problema):'),$B,'C');
    $pdf->SetxY($X,$Y+20);
    $pdf->cell($W,5,'Acciones Correctivas:',$B,1,'L');
    $pdf->SetxY($X+142,$Y+20);
    $pdf->cell(53,5,'Refacciones:',$B,1,'L');
    $pdf->SetxY($X,$Y+45);
    $pdf->cell(50,5,'Observaciones:',$B,1,'L');
    //LINEAS
    $pdf->Line($X+0,$Y+5,$X+$W,$Y+5);
    $pdf->Line($X+50,$Y+10,$X+$W,$Y+10);
    $pdf->Line($X+50,$Y+15,$X+$W,$Y+15);
    $pdf->Line($X+0,$Y+20,$X+$W,$Y+20);
    $pdf->Line($X+50,$Y+25,$X+$W,$Y+25);
    $pdf->Line($X+50,$Y+30,$X+$W,$Y+30);
    $pdf->Line($X+50,$Y+35,$X+$W,$Y+35);
    $pdf->Line($X+50,$Y+40,$X+$W,$Y+40);
    $pdf->Line($X+50,$Y+45,$X+$W,$Y+45);
    $pdf->Line($X,$Y+45,$X+$W,$Y+45);
    $pdf->Line($X+50,$Y+50,$X+$W,$Y+50);
    $pdf->Line($X+50,$Y+55,$X+$W,$Y+55);
    $pdf->Line($X+50,$Y+60,$X+$W,$Y+60);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+50,$Y);
    $pdf->MultiCell($W-50,5,utf8_decode($reporte->llegada_f.' '.$reporte->llegada_h),$B,'L');
    $pdf->SetxY($X+50,$Y+5);
    $pdf->MultiCell($W-50,5,utf8_decode($reporte->diagnostico),$B,'L');
    $pdf->SetxY($X+50,$Y+20);
    $pdf->MultiCell($W-103,5,utf8_decode($reporte->accion_realizada),$B,'L');
    $pdf->SetxY($X+142,$Y+25);
    $pdf->MultiCell(53,5,utf8_decode($reporte->refacciones),$B,'L');
    $pdf->SetxY($X+50,$Y+45);
    $pdf->MultiCell($W-103,5,utf8_decode($reporte->observaciones),$B,'L');



    //CIERRE DE REPORTE
    $pdf->SetFont('Arial','B',11);
    $Y += 70;//207
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,22,'',1,1);

    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,'Cierre de Reporte',1,1,'C', true);
    $Y += 6;
    $pdf->SetxY($X,$Y);
    $pdf->cell(72,5,utf8_decode('Fecha y hora de reparación:'),$B,1,'L');
    $pdf->SetxY($X,$Y+5);
    $pdf->cell(72,8,utf8_decode('Tiempo de reparación:'),$B,1,'L');
    $pdf->SetxY($X+83,$Y+6);
    $pdf->cell(24,6,'48 horas',1,1,'C');
    $pdf->SetxY($X+127,$Y+6);
    $pdf->cell(24,6,'96 horas',1,1,'C');
    $pdf->SetxY($X+170,$Y+6);
    $pdf->cell(24,6,'+96 horas',1,1,'C');
    //LINEAS
    $pdf->Line($X+72,$Y+5,$X+$W,$Y+5);
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+72,$Y);
    $pdf->cell(45,5,$reporte->termino_f.' '.$reporte->termino_h,$B,1,'C');
    if( strtoupper(trim($reporte->plan_ans)) == '48 HORAS') {
        $pdf->Line($X+83,$Y+6,$X+83+24,$Y+12);
        $pdf->Line($X+83,$Y+12,$X+83+24,$Y+6);
    }
    if( strtoupper(trim($reporte->plan_ans)) == '96 HORAS') {
        $pdf->Line($X+127,$Y+6,$X+127+24,$Y+12);
        $pdf->Line($X+127,$Y+12,$X+127+24,$Y+6);
    }
    if( strtoupper(trim($reporte->plan_ans)) == '+96 HORAS' || strtoupper(trim($reporte->plan_ans)) == '+ 96 HORAS' ) {
        $pdf->Line($X+170,$Y+6,$X+170+24,$Y+12);
        $pdf->Line($X+170,$Y+12,$X+170+24,$Y+6);
    }



    //FIRMAS DE CONFORMIDAD
    $pdf->SetFont('Arial','B',11);
    $Y += 18;
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,35,'',1,1);

    $pdf->SetxY($X,$Y);
    $pdf->cell($W,6,'Firmas de conformidad',1,1,'C', true);
    $Y += 6;
    $pdf->SetxY($X,$Y);
    $pdf->cell($W/2,5,'Por parte de (Nombre de la empresa)',1,1,'C', true);
    $pdf->SetxY($X + $W/2,$Y);
    $pdf->cell($W/2,5,utf8_decode('Aceptación del servicio por parte de la Aduana'),1,1,'C', true);
    $Y += 5;
    $pdf->SetxY($X,$Y);
    $pdf->cell($M,8,'Nombre',1,1,'L');
    $pdf->SetxY($X+$M,$Y);
    $pdf->cell($M,8,'Nombre',1,1,'L');
    $pdf->SetxY($X,$Y+8);
    $pdf->cell($M,8,'Cargo',1,1,'L');
    $pdf->SetxY($X+$M,$Y+8);
    $pdf->cell($M,8,'Cargo',1,1,'L');
    $pdf->SetxY($X,$Y+16);
    $pdf->cell($M,8,'Firma',1,1,'L');
    $pdf->SetxY($X+$M,$Y+16);
    $pdf->cell($M,8,'Firma',1,1,'L');
    //VALUES
    $pdf->SetFont('Arial','',10);
    $pdf->SetxY($X+$M+18,$Y);
    $pdf->cell(80,8,'',$B,1,'L');
    $pdf->SetxY($X+18,$Y);
    $pdf->cell(80,8,'',$B,1,'L');
    $pdf->SetxY($X+$M+18,$Y+8);
    $pdf->cell(80,8,'',$B,1,'L');
    $pdf->SetxY($X+18,$Y+8);
    $pdf->cell(80,8,'',$B,1,'L');
    $pdf->SetxY($X+$M+18,$Y+16);
    $pdf->cell(80,8,'',$B,1,'L');
    $pdf->SetxY($X+18,$Y+16);
    $pdf->cell(80,8,'',$B,1,'L');

    $pdf->Ln(10);
}

$pdf->Output('I', 'Informe'.$ano.$mes.'.pdf', true);
?>
