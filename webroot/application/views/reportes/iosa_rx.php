<?php

require_once("fpdf.php");

class PDF extends FPDF
{
    private $logo = APPPATH.'../assets/img/logo.png';

    function __construct() {
        parent::__construct();
        if (file_exists(APPPATH.'../assets/img/logo-iosa-rx.png')) {
            $this->logo = APPPATH.'../assets/img/logo-iosa-rx.png';
        }
    }

    function Header() {
        $this->Image($this->logo,20,8,21);//33
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF('L','mm','Letter');
$k = 0;
foreach($tickets as $reporte) {
    $k++;
    $Y = 30;//Controla el punto de inicio de cada contenedor de datos
    $X = 10; //Controla el margen izquierdo del documento
    $W = 190;
    //$col = $W / 28;//Se crean columnas de
    $cols = [];
    $rows = [];
    $row = 4; 
    for( $i=0; $i<=30; $i++ ){
        $rows[$i] = $row * $i;
        $cols[$i] = $W / 28 * $i;
    }
    
    $B = 1;
    if( $k % 2 == 1 ) {
        $pdf->AliasNbPages();
        $pdf->AddPage();
    }
    else {
        $Y += $rows[30];
    }
    
    $pdf->SetFont('Arial','',7.5);
    $pdf->SetxY($X,$Y);
    $pdf->cell($W,$rows[28],'',1,1);
    
    $pdf->SetxY($X+$cols[1],$Y+$rows[1]);
    $pdf->cell($cols[3],$row,'No de ticket:',$B,1,'L');
    $pdf->SetxY($X+$cols[4],$Y+$rows[1]);
    $pdf->cell($cols[3],$row,utf8_decode($reporte->id),1,1,'L');

    $pdf->SetxY($X+$cols[8],$Y+$rows[1]);
    $pdf->cell($cols[3]+3,$row,'Ingeniero a cargo:',$B,1,'L');
    $pdf->SetxY($X+$cols[11]+3,$Y+$rows[1]);
    $pdf->cell($cols[8],$row,utf8_decode($reporte->ing_a_cargo),1,1,'L');

    $pdf->SetxY($X+$cols[21],$Y+$rows[1]);
    $pdf->cell($cols[3],$row,'Fecha reporte:',$B,1,'L');
    $pdf->SetxY($X+$cols[24],$Y+$rows[1]);
    $pdf->cell($cols[3],$row,utf8_decode($reporte->creacion_f),1,1,'L');

    $pdf->SetxY($X+$cols[8],$Y+$rows[2]);
    $pdf->cell($cols[3]+3,$row,'Coordinador:',$B,1,'L');
    $pdf->SetxY($X+$cols[11]+3,$Y+$rows[2]);
    $pdf->cell($cols[8],$row,utf8_decode($reporte->coordinador),1,1,'L');

    $pdf->SetxY($X+$cols[21],$Y+$rows[2]);
    $pdf->cell($cols[3],$row,'Hora reporte:',$B,1,'L');
    $pdf->SetxY($X+$cols[24],$Y+$rows[2]);
    $pdf->cell($cols[3],$row,utf8_decode($reporte->creacion_h),1,1,'L');
    
    $pdf->SetxY($X+$cols[1],$Y+$rows[4]);
    $pdf->cell($cols[2],$row,'Serie:',$B,1,'L');
    $pdf->SetxY($X+$cols[3],$Y+$rows[4]);
    $pdf->cell($cols[4],$row,utf8_decode($reporte->serie),1,1,'L');

    $pdf->SetxY($X+$cols[8],$Y+$rows[4]);
    $pdf->cell($cols[2],$row,'Modelo:',$B,1,'L');
    $pdf->SetxY($X+$cols[10],$Y+$rows[4]);
    $pdf->MultiCell($cols[4],$row,utf8_decode($reporte->modelo),1,'L');

    $pdf->SetxY($X+$cols[14],$Y+$rows[4]);
    $pdf->cell($cols[2],$row,'Marca:',$B,1,'L');
    $pdf->SetxY($X+$cols[16],$Y+$rows[4]);
    $pdf->MultiCell($cols[3],$row,utf8_decode($reporte->marca),1,'L');

    $pdf->SetxY($X+$cols[20],$Y+$rows[4]);
    $pdf->cell($cols[3],$row,utf8_decode('Ubicación:'),$B,1,'L');
    $pdf->SetxY($X+$cols[23],$Y+$rows[4]);
    $pdf->MultiCell($cols[4],$row,utf8_decode($reporte->ubicacion),1,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[6]);
    $pdf->cell($cols[2],$row,'Contrato:',$B,1,'L');
    $pdf->SetxY($X+$cols[3],$Y+$rows[6]);
    $pdf->cell($cols[6],$row,utf8_decode($reporte->contrato),1,1,'L');

    $pdf->SetxY($X+$cols[11],$Y+$rows[6]);
    $pdf->cell($cols[3],$row,'Mantenimiento:',$B,1,'L');
    $pdf->SetxY($X+$cols[14],$Y+$rows[6]);
    $pdf->cell($cols[8],$row,utf8_decode($reporte->tema_de_ayuda),1,1,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[8]);
    $pdf->cell($cols[5],$row,'Persona que reporta:',$B,1,'L');

    $pdf->SetxY($X+$cols[6],$Y+$rows[8]);
    $pdf->cell($cols[3],$row,'Nombre:',$B,1,'L');
    $pdf->SetxY($X+$cols[9],$Y+$rows[8]);
    $pdf->cell($cols[10],$row,utf8_decode($reporte->quien_reporta),1,1,'L');

    $pdf->SetxY($X+$cols[6],$Y+$rows[9]);
    $pdf->cell($cols[3],$row,utf8_decode('Teléfono:'),$B,1,'L');
    $pdf->SetxY($X+$cols[9],$Y+$rows[9]);
    $pdf->cell($cols[8],$row,utf8_decode($reporte->telefono),1,1,'L');

    $pdf->SetxY($X+$cols[6],$Y+$rows[10]);
    $pdf->cell($cols[3],$row,'Cargo:',$B,1,'L');
    $pdf->SetxY($X+$cols[9],$Y+$rows[10]);
    $pdf->cell($cols[10],$row,utf8_decode($reporte->cargo),1,1,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[12]);
    $pdf->cell($cols[5],$row,utf8_decode('Descripción de la falla:'),$B,1,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[13]);
    $pdf->cell($cols[26],$rows[2],'',1,1,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[13]);
    $pdf->MultiCell($cols[26],$rows[1],utf8_decode($reporte->detalle),0,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[16]);
    $pdf->cell($cols[3],$row,utf8_decode('Resolución:'),$B,1,'C');
    $pdf->SetxY($X+$cols[4],$Y+$rows[16]);
    $pdf->cell($cols[7],$row,utf8_decode($reporte->resolucion),1,1,'L');

    $pdf->SetxY($X+$cols[12],$Y+$rows[16]);
    $pdf->cell($cols[4],$row,'Fecha llegada:',$B,1,'C');
    $pdf->SetxY($X+$cols[16],$Y+$rows[16]);
    $pdf->cell($cols[4],$row,utf8_decode($reporte->llegada_f),1,1,'L');
    
    $pdf->SetxY($X+$cols[20],$Y+$rows[16]);
    $pdf->cell($cols[4],$row,'Hora llegada:',$B,1,'C');
    $pdf->SetxY($X+$cols[24],$Y+$rows[16]);
    $pdf->cell($cols[3],$row,utf8_decode($reporte->llegada_h),1,1,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[18]);
    $pdf->cell($cols[5],$row,'Acciones realizadas:',$B,1,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[19]);
    $pdf->cell($cols[26],$rows[2],'',1,0,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[19]);
    $pdf->MultiCell($cols[26],$rows[1],utf8_decode($reporte->acciones),0,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[22]);
    $pdf->cell($cols[3],$row,'Fecha cierre:',$B,1,'L');
    $pdf->SetxY($X+$cols[4],$Y+$rows[22]);
    $pdf->cell($cols[4],$row,utf8_decode($reporte->termino_f),1,1,'L');

    $pdf->SetxY($X+$cols[10],$Y+$rows[22]);
    $pdf->cell($cols[3],$row,'Hora de cierre:',$B,1,'L');
    $pdf->SetxY($X+$cols[13],$Y+$rows[22]);
    $pdf->cell($cols[3],$row,utf8_decode($reporte->termino_h),1,1,'L');

    $pdf->SetxY($X+$cols[17],$Y+$rows[22]);
    $pdf->cell($cols[3],$row,'Estado final:',$B,1,'L');
    $pdf->SetxY($X+$cols[20],$Y+$rows[22]);
    $pdf->cell($cols[7],$row,utf8_decode($reporte->estado_final),1,1,'L');

    $pdf->SetxY($X+$cols[1],$Y+$rows[24]);
    $pdf->cell($cols[5],$row,'Observaciones:',$B,1,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[25]);
    $pdf->cell($cols[26],$rows[2],'',1,1,'L');
    $pdf->SetxY($X+$cols[1],$Y+$rows[25]);
    $pdf->MultiCell($cols[26],$rows[1],utf8_decode($reporte->observaciones),0,'L');

    $pdf->Ln(10);
}

$pdf->Output('I', 'Informe'.$ano.$mes.'.pdf', true);
?>
