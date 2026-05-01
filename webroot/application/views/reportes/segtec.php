<?php

require_once("fpdf.php");

class PDF extends FPDF
{
    private $logo = APPPATH.'../assets/img/logo.png';

    function __construct() {
        parent::__construct();
        if (file_exists(APPPATH.'../assets/img/logo_segtec.png')) {
            $this->logo = APPPATH.'../assets/img/logo_segtec.png';
        }
    }

    function Header() {
        //$this->Image($this->logo,5,10,125, 25);
        $this->Image($this->logo,10,10,62.4, 12.1);
        $this->SetFont('Arial','B',14);
        $this->SetxY(10,23);
        $this->cell(190,10,'REPORTE MENSUAL DE TICKETS GENERADOS',0,1,'C');
    }

    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF('L','mm','Letter');
$k = 0;
foreach($tickets as $reporte) {
    $k++;
    $Y = 40;//Controla el punto de inicio de cada contenedor de datos
    $X = 10; //Controla el margen izquierdo del documento
    $W = 190;

    $cols = [];
    $rows = [];
    $row = 3.5; 
    for( $i=0; $i<=30; $i++ ){
        $rows[$i] = $row * $i;
        $cols[$i] = $W / 25 * $i;
    }
    
    $B = 0;
    $BB = 1;
    $hCol = $cols[1]/2;
    $hRow = $rows[1]/2;

    //Calculamos las ubicaciones de cada ticket
    $mod = $k%3;
    switch ($mod) {
        case 1: $Y = 40; $pdf->AliasNbPages(); $pdf->AddPage(); break;//primero
        case 2: $Y = 120; break;//segundo
        case 0: $Y = 200; break;//tercero
        default: exit( "asd" ); break;
    }
    
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,$rows[21]+$hRow,'',1,1);
    
    //BOX & LABELS
    $pdf->SetFont('Arial','B',7.5);
    //Ticket
    $pdf->SetxY($X+$cols[1],$Y+$hRow);
    $pdf->cell($cols[2],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$hRow);
    $pdf->cell($cols[2],$row,'Ticket #:',$B,1,'C');
    //Apertura fecha y hora:
    $pdf->SetxY($X+$cols[4],$Y+$hRow);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[4],$Y+$hRow);
    $pdf->cell($cols[5],$row,'Apertura fecha y hora:',$B,1,'C');
    //No de serie:
    $pdf->SetxY($X+$cols[1],$Y+$rows[3]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$rows[3]);
    $pdf->cell($cols[5],$row,'No de serie:',$B,1,'C');
    //Marca:
    $pdf->SetxY($X+$cols[7],$Y+$rows[3]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[7],$Y+$rows[3]);
    $pdf->cell($cols[5],$row,'Marca:',$B,1,'C');
    //Modelo
    $pdf->SetxY($X+$cols[13],$Y+$rows[3]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[13],$Y+$rows[3]);
    $pdf->cell($cols[5],$row,'Modelo:',$B,1,'C');
    //Ubicacion
    $pdf->SetxY($X+$cols[19],$Y+$rows[3]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[19],$Y+$rows[3]);
    $pdf->cell($cols[5],$row,utf8_decode('Ubicación:'),$B,1,'C');
    //Detalle
    $pdf->SetxY($X+$cols[1],$Y+$rows[6]+$hRow);
    $pdf->cell($cols[12],$rows[3],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$rows[5]+$hRow);
    $pdf->cell($cols[12],$row,'Detalle:',$B,1,'L');
    //Reportado por
    $pdf->SetxY($X+$cols[13],$Y+$rows[6]+$hRow);
    $pdf->cell($cols[5],$rows[3],'',$BB);
    $pdf->SetxY($X+$cols[13],$Y+$rows[5]+$hRow);
    $pdf->cell($cols[5],$row,'Reportado por:',$B,1,'L');
    //Técnico de servicio
    $pdf->SetxY($X+$cols[18],$Y+$rows[6]+$hRow);
    $pdf->cell($cols[6],$rows[3],'',$BB);
    $pdf->SetxY($X+$cols[18],$Y+$rows[5]+$hRow);
    $pdf->cell($cols[6],$row,utf8_decode('Técnico de servicio:'),$B,1,'L');
    //Descripción
    $pdf->SetxY($X+$cols[1],$Y+$rows[11]);
    $pdf->cell($cols[23],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$rows[10]);
    $pdf->cell($cols[23],$row,utf8_decode('Descripción'),$B,1,'L');
    //Actividades realizadas
    $pdf->SetxY($X+$cols[1],$Y+$rows[14]+$hRow);
    $pdf->cell($cols[23],$rows[3],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$rows[13]+$hRow);
    $pdf->cell($cols[23],$row,utf8_decode('Actividades realizadas:'),$B,1,'L');
    //Estado final del equipo:
    $pdf->SetxY($X+$cols[1],$Y+$rows[19]);
    $pdf->cell($cols[11],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[1],$Y+$rows[18]);
    $pdf->cell($cols[11],$row,utf8_decode('Estado final del equipo:'),$B,1,'L');
    //LLegada fecha y hora
    $pdf->SetxY($X+$cols[13],$Y+$rows[19]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[13],$Y+$rows[19]);
    $pdf->cell($cols[5],$row,'Llegada fecha y hora:',$B,1,'C');
    //Cierre fecha y hora
    $pdf->SetxY($X+$cols[19],$Y+$rows[19]);
    $pdf->cell($cols[5],$rows[2],'',$BB);
    $pdf->SetxY($X+$cols[19],$Y+$rows[19]);
    $pdf->cell($cols[5],$row,'Termino fecha y hora:',$B,1,'C');


    //VALUES
    $pdf->SetFont('Arial','',7.5);
    //Ticket
    $pdf->SetxY($X+$cols[1],$Y+$rows[1]+$hRow);
    $pdf->cell($cols[2],$row,$reporte->id,$B,1,'C');
    //Apertura fecha y hora:
    $pdf->SetxY($X+$cols[4],$Y+$rows[1]+$hRow);
    $pdf->cell($cols[5],$row,$reporte->apertura_f.' '.$reporte->apertura_h,$B,1,'C');
    //No de serie:
    $pdf->SetxY($X+$cols[1],$Y+$rows[4]);
    $pdf->cell($cols[5],$row,utf8_decode($reporte->no_serie),$B,1,'C');
    //Marca:
    $pdf->SetxY($X+$cols[7],$Y+$rows[4]);
    $pdf->cell($cols[5],$row,utf8_decode($reporte->marca),$B,1,'C');
    //Modelo
    $pdf->SetxY($X+$cols[13],$Y+$rows[4]);
    $pdf->cell($cols[5],$row,utf8_decode($reporte->modelo),$B,1,'C');
    //Ubicación
    $pdf->SetxY($X+$cols[19],$Y+$rows[4]);
    $pdf->cell($cols[5],$row,utf8_decode($reporte->ubicacion),$B,1,'C');
    //Detalle
    $pdf->SetxY($X+$cols[1],$Y+$rows[6]+$hRow);
    $pdf->MultiCell($cols[12],$row,utf8_decode($reporte->detalle),$B);
    //Reportado por
    $pdf->SetxY($X+$cols[13],$Y+$rows[6]+$hRow);
    $pdf->MultiCell($cols[5],$row,utf8_decode($reporte->area_reporta),$B);
    //Técnico de servicio
    $pdf->SetxY($X+$cols[18],$Y+$rows[6]+$hRow);
    $pdf->MultiCell($cols[6],$row,utf8_decode($reporte->asignar_a),$B);
    //Descripción
    $pdf->SetxY($X+$cols[1],$Y+$rows[11]);
    $pdf->MultiCell($cols[23],$row,utf8_decode($reporte->descripcion),$B);
    //Actividades realizadas
    $pdf->SetxY($X+$cols[1],$Y+$rows[14]+$hRow);
    $pdf->MultiCell($cols[23],$row,utf8_decode($reporte->actividad_realizada),$B);
    //Estado final del equipo:
    $pdf->SetxY($X+$cols[1],$Y+$rows[19]);
    $pdf->MultiCell($cols[11],$row,utf8_decode($reporte->estado_final),$B);
    //LLegada fecha y hora
    $pdf->SetxY($X+$cols[13],$Y+$rows[20]);
    $pdf->cell($cols[5],$row,$reporte->fhora_llegada_f.' '.$reporte->fhora_llegada_h,$B,1,'C');
    //Cierre fecha y hora
    $pdf->SetxY($X+$cols[19],$Y+$rows[20]);
    $pdf->cell($cols[5],$row,$reporte->fhora_termino_f.' '.$reporte->fhora_termino_h,$B,1,'C');

    $pdf->Ln(10);
}

$pdf->Output('I', 'Informe'.$ano.$mes.'.pdf', true);
?>
