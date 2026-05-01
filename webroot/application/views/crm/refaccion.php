<?php

require_once(APPPATH . "views/reportes/fpdf.php");

class PDF extends FPDF
{
    function __construct() {
        parent::__construct();
    }

    function Footer() {
        $this->SetY(-28);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF('L','mm','Letter');
if( isset($reg) ) { //Se agrega en forma de array de un solo elemento, esto para mantener la compatibilidad en caso de que se generen reportes por rango de fecha como los demas 
    $tickets[] = $reg;
}
foreach($tickets as $item) {
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFillColor(220,220,220);

    $Y = 20;//Controla el punto de inicio de cada contenedor de datos
    $X = 18; //Controla el margen izquierdo del documento
    $W = 180;
    $M = $W / 2;//Mitad
    $B = 0;

    $pdf->SetFont('Arial','B',12);
    $pdf->SetxY($X,$Y);
    $pdf->cell(54,10,'PEDIDO DE',1,1,'C',true);

    $pdf->SetFont('Arial','B',11);
    $pdf->SetxY($X+54,$Y);
    $pdf->cell(126,10,'CONSUMIBLES USO INTERNO (IOU)',1,1,'C');

    $Y += 12;
    $pdf->SetFont('Arial','',9);
    $pdf->SetxY($X,$Y);
    $pdf->cell(12,7,'Fecha',1,1,'L');
    $pdf->SetxY($X+54,$Y);
    $pdf->cell(26,7,'Responsable',1,1,'L');
    $pdf->SetxY($X+80,$Y);
    $pdf->cell(100,7,'',1,1,'L');
    //VALORES
    $pdf->SetFont('Arial','',8);
    $pdf->SetxY($X+12,$Y);
    $pdf->cell(42,7,substr($item->fecha_pedido, 0, 10),1,1,'L');

    $Y += 15;
    $X = 53;
    $pdf->SetFont('Arial','B',9);
    $pdf->SetxY($X,$Y);
    $pdf->cell(33,5,'Equipo',$B,1,'L');
    
    $pdf->SetFont('Arial','',9);
    $pdf->SetxY($X,$Y+5);
    $pdf->cell(33,4,'Modelo',1,1,'L');
    $pdf->SetxY($X,$Y+9);
    $pdf->cell(33,4,utf8_decode('Número de Serie'),1,1,'L');
    $pdf->SetxY($X,$Y+13);
    $pdf->cell(33,4,'Nombre del cliente',1,1,'L');
    $pdf->SetxY($X,$Y+17);
    $pdf->cell(33,4,'Nombre de Usuario',1,1,'L');
    //VALORES
    $pdf->SetFont('Arial','',8);
    $pdf->SetxY($X+33,$Y+5);
    $pdf->cell(70,4,utf8_decode($item->modelo),1,1,'L');
    $pdf->SetxY($X+33,$Y+9);
    $pdf->cell(70,4,utf8_decode($item->serie),1,1,'L');
    $pdf->SetxY($X+33,$Y+13);
    $pdf->cell(70,4,utf8_decode($item->cliente),1,1,'L');
    $pdf->SetxY($X+33,$Y+17);
    $pdf->cell(70,4,utf8_decode($item->contacto),1,1,'L');

    $Y += 26;
    $X=33;
    $pdf->SetFont('Arial','',9);
    $pdf->SetxY($X,$Y+5);
    $pdf->cell(51,5,'Nombre del distribuidor',1,1,'L');
    $pdf->SetxY($X,$Y+10);
    $pdf->cell(51,5,'Responsable',1,1,'L');
    $pdf->SetxY($X,$Y+15);
    $pdf->cell(51,5,'Modelo',1,1,'L');
    $pdf->SetxY($X,$Y+20);
    $pdf->cell(51,8,utf8_decode('Dirección Entrega'),1,1,'L');
    $pdf->SetxY($X,$Y+28);
    $pdf->cell(51,8,'Motivo',1,1,'L');
    //VALORES
    $pdf->SetFont('Arial','',8);
    $pdf->SetxY($X+51,$Y+5);
    $pdf->cell(114,5,utf8_decode($item->asignacion),1,1,'L');
    $pdf->SetxY($X+51,$Y+10);
    $pdf->cell(114,5,'',1,1,'L');
    $pdf->SetxY($X+51,$Y+15);
    $pdf->cell(114,5,utf8_decode($item->modelo),1,1,'L');
    $pdf->SetxY($X+51,$Y+20);
    $pdf->MultiCell(114,2.7,utf8_decode($item->ubicacion.' '.$item->ciudad.' '.$item->estado),0,'L');
    $pdf->SetxY($X+51,$Y+20);
    $pdf->cell(114,8,'',1);
    $pdf->SetxY($X+51,$Y+28);
    $pdf->cell(114,8,'',1,1,'L');

    $Y += 38;
    $X = 18;
    $pdf->SetFillColor(192,192,192);
    $pdf->SetFont('Arial','',9);
    $pdf->SetxY($X,$Y);
    $pdf->cell(8,5,'No.',1,1,'C', true);
    $pdf->SetxY($X+8,$Y);
    $pdf->cell(17,5,'Cantidad',1,1,'C', true);
    $pdf->SetxY($X+25,$Y);
    $pdf->cell(34,5,'No. de Parte',1,1,'C', true);
    $pdf->SetxY($X+59,$Y);
    $pdf->cell(73,5,utf8_decode('Descripción'),1,1,'C', true);
    $pdf->SetxY($X+132,$Y);
    $pdf->cell(18,5,'# ticket',1,1,'C', true);
    $pdf->SetxY($X+150,$Y);
    $pdf->cell(30,5,'# Proforma',1,1,'C', true);

    $registros = 20;
    $Y += 1.4;
    $y = $Y;
    $pdf->SetFont('Arial','',9);
    for( $i=0; $i<=19; $i++ ) {
        $y += 3.6;
        $pdf->SetxY($X,$y);
        $pdf->cell(8,3.6,$i+1,1,1,'C');
        $pdf->SetxY($X+8,$y);
        if (empty($item->busqueda_de_articulo[$i])) {
            $pdf->cell(17,3.6,'',1,1,'C');
            $pdf->SetxY($X+25,$y);
            $pdf->cell(34,3.6,'',1,1,'C');
            $pdf->SetxY($X+59,$y);
            $pdf->cell(73,3.6,'',1,1,'C');
            $pdf->SetxY($X+132,$y);
            $pdf->cell(18,3.6,'',1,1,'C');
            $pdf->SetxY($X+150,$y);
            $pdf->cell(30,3.6,'',1,1,'C');
        } else {
            $pdf->cell(17,3.6,$item->busqueda_de_articulo[$i]->cantidad,1,1,'C');
            $pdf->SetxY($X+25,$y);
            $pdf->cell(34,3.6,utf8_decode($item->busqueda_de_articulo[$i]->no_parte),1,1,'C');
            $pdf->SetxY($X+59,$y);
            $pdf->cell(73,3.6,utf8_decode($item->busqueda_de_articulo[$i]->descripcion),1,1,'C');
            $pdf->SetxY($X+132,$y);
            $pdf->cell(18,3.6,$item->id,1,1,'C');
            $pdf->SetxY($X+150,$y);
            $pdf->cell(30,3.6,utf8_decode($item->busqueda_de_articulo[$i]->proforma),1,1,'C');
        }
    }
    $Y = 190;
    $X=18;
    $pdf->SetFont('Arial','',9);
    $pdf->SetxY($X,$Y+5);
    $pdf->cell($W,12,'',1,1,'L');
    $pdf->SetxY($X,$Y+17);
    $pdf->cell($W-33,4,'TOTAL USD',1,1,'C', true);
    $pdf->SetxY($X +$W-33,$Y+17);
    $pdf->cell(33,4,'',1,1,'C');

    $Y += 23;
    $pdf->SetxY($X,$Y);
    $pdf->cell(13,10,'Firma',1,1,'L');
    $pdf->SetxY($X+13,$Y);
    $pdf->cell(29,10,'',1,1,'L');
    $pdf->SetxY($X+43,$Y);
    $pdf->cell(13,10,'Firma',1,1,'L');
    $pdf->SetxY($X+56,$Y);
    $pdf->cell(28,10,'',1,1,'L');
    $pdf->SetxY($X+85,$Y);
    $pdf->cell(13,10,'Firma',1,1,'L');
    $pdf->SetxY($X+98,$Y);
    $pdf->cell(43,10,'',1,1,'L');
    $pdf->SetxY($X+142,$Y);
    $pdf->cell(13,10,'Firma',1,1,'L');
    $pdf->SetxY($X+155,$Y);
    $pdf->cell(25,10,'',1,1,'L');
    ////////////////////////////////////////
    $Y +=10;
    $pdf->SetxY($X,$Y);
    $pdf->cell(13,7,'Nombre',1,1,'L');

    $pdf->SetxY($X+13,$Y);
    $pdf->cell(29,7,$item->ejecutivo_ccc,1,1,'L');

    $pdf->SetxY($X+43,$Y);
    $pdf->cell(13,7,'Nombre',1,1,'L');
    $pdf->SetxY($X+56,$Y);
    $pdf->cell(28,7,'Hiroyuki Maebeya',1,1,'L');

    $pdf->SetxY($X+85,$Y);
    $pdf->cell(13,7,'Nombre',1,1,'L');
    $pdf->SetxY($X+98,$Y);
    $pdf->cell(43,7,'Sr. Hirokazu Ueta',1,1,'L');

    $pdf->SetxY($X+142,$Y);
    $pdf->cell(13,7,'Nombre',1,1,'L');
    $pdf->SetxY($X+155,$Y);
    $pdf->cell(25,7,'',1,1,'L');
    ///////////////////////////////////////
    $Y +=7;
    $pdf->SetxY($X,$Y);
    $pdf->cell(13,7,'Fecha',1,1,'L');

    $pdf->SetxY($X+13,$Y);
    $pdf->cell(29,7,substr($item->fecha_pedido,0,10),1,1,'L');

    $pdf->SetxY($X+43,$Y);
    $pdf->cell(13,7,'Fecha',1,1,'L');
    $pdf->SetxY($X+56,$Y);
    $pdf->cell(28,7,substr($item->fecha_pedido,0,10),1,1,'L');

    $pdf->SetxY($X+85,$Y);
    $pdf->cell(13,7,'Fecha',1,1,'L');
    $pdf->SetxY($X+98,$Y);
    $pdf->cell(43,7,substr($item->fecha_pedido,0,10),1,1,'L');

    $pdf->SetxY($X+142,$Y);
    $pdf->cell(13,7,'Fecha',1,1,'L');
    $pdf->SetxY($X+155,$Y);
    $pdf->cell(25,7,'',1,1,'L');

    $pdf->Ln(10);
}

if (empty($adjuntar)) {
    $pdf->Output('I', 'Informe' . date('Y') . date('m') . '.pdf', true);
} else {
    $attach = $pdf->Output('S');
}
?>
