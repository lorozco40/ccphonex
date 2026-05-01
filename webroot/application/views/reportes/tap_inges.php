<?php

require_once("fpdf.php");

class PDF extends FPDF
{

    private $meses = array(
        "01"=>"Enero",
        "02"=>"Febrero",
        "03"=>"Marzo",
        "04"=>"Abril",
        "05"=>"Mayo",
        "06"=>"Junio",
        "07"=>"Julio",
        "08"=>"Agosto",
        "09"=>"Septiembre",
        "10"=>"Octubre",
        "11"=>"Noviembre",
        "12"=>"Diciembre"
    );
    private $mes;
    private $ano;
    private $logo = APPPATH.'../assets/img/logo.png';

    function __construct($mes, $ano) {
        parent::__construct();
        $this->mes = $this->meses[$mes];
        $this->ano = $ano;
        if (file_exists(APPPATH.'../assets/img/logo-tap-inges.png')) {
            $this->logo = APPPATH.'../assets/img/logo-tap-inges.png';
        }
    }

    function Header() {
        $this->Image($this->logo,20,8,33);
        $this->SetFont('Arial','B',12);
        $this->SetXY(90,10);
        $this->SetXY(90,15);
        $this->Line(20,25,190,25);
        $this->Ln(15);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,utf8_decode('REPORTE MENSUAL DE MESA DE AYUDA'),0,1,'C');
        $this->Cell(0,10,$this->mes.' de '.$this->ano,0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF($mes, $ano);
$pdf->SetTextColor(60,60,60);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',9);
foreach($tickets as $ticket) {
    $pdf->cell(0,62,'',1,1);
    $actualY = $pdf->getY();

    $pdf->SetxY(12,$actualY-59);
    $pdf->Cell(21,4,utf8_decode("No. de ticket: "),0,0);
    $pdf->Cell(19,4,utf8_decode($ticket->id),1,0,'C');
    $pdf->Cell(24,4,utf8_decode("Ing. de servicio: "),0,0);
    $pdf->Cell(55,4,utf8_decode($ticket->ing_resp),1,0);
    $pdf->Cell(17,4,utf8_decode("Ubicación: "),0,0);
    $pdf->SetFont('Times','',7);
    $pdf->Cell(48,4,utf8_decode($ticket->ubicacion),1,1);
    $pdf->SetFont('Times','',9);

    $pdf->Ln(3);
    $pdf->SetX(12);
    $pdf->Cell(12,4,utf8_decode("Marca: "),0,0);
    $pdf->Cell(70,4,utf8_decode($ticket->marca),1,0);
    $pdf->Cell(14,4,utf8_decode("Modelo: "),0,0);
    $pdf->Cell(37,4,utf8_decode($ticket->modelo),1,0);
    $pdf->Cell(19,4,utf8_decode("No. de serie: "),0,0);
    $pdf->Cell(32,4,utf8_decode($ticket->no_serie),1,1);

    $pdf->Ln(3);
    $pdf->SetX(12);
    $pdf->Cell(104,4,utf8_decode("Descripción del reporte: "),0,0);
    $pdf->Cell(40,4,utf8_decode("Fecha: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,$ticket->fecha,0,0);
    $pdf->Cell(40,4,utf8_decode("Hora: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,utf8_decode($ticket->hora),0,1);
    $pdf->SetX(12);
    $pdf->MultiCell(184,8,strip_tags(utf8_decode($ticket->detalle)),1,1);

    $pdf->Ln(3);
    $pdf->SetX(12);
    $pdf->Cell(104,4,utf8_decode("Estado final: "),0,0);
    $pdf->Cell(40,4,utf8_decode("Fecha: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,$ticket->fecha_llegada,0,0);
    $pdf->Cell(40,4,utf8_decode("Hora: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,utf8_decode($ticket->hora_llegada),0,1);
    $pdf->SetX(12);
    $pdf->MultiCell(184,8,strip_tags(utf8_decode($ticket->estado_final)),1,1);

    $pdf->Ln(3);
    $pdf->SetX(12);
    $pdf->Cell(104,4,utf8_decode("Acciones Realizadas y Conclusión: "),0,0);
    $pdf->Cell(40,4,utf8_decode("Fecha: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,$ticket->fecha_cierre,0,0);
    $pdf->Cell(40,4,utf8_decode("Hora: "),'LTR',0);
    $actualx = $pdf->GetX();
    $pdf->SetX($actualx - 25);
    $pdf->Cell(25,4,utf8_decode($ticket->hora_cierre),0,1);
    $pdf->SetX(12);
    $pdf->MultiCell(184,8,strip_tags(utf8_decode($ticket->acciones)),1,1);

    $pdf->Ln(10);
}

$pdf->Output('I', 'Informe'.$ano.$mes.'.pdf', true);
?>
