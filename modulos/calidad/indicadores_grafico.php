<?php
require_once ("../../config.php");
include ("../../lib/imagenes_stat_2011/jpgraph.php");
include ("../../lib/imagenes_stat_2011/jpgraph_bar.php");

$anio = $parametros['anio'];
$id_indi=$parametros['id_indi'];
$tamaño=$parametros['tamaño'];


$sql="SELECT indicadores_ins.id_desc_indicador_ins, indicadores_ins.mes, indicadores_ins.anio, indicadores_ins.valor, desc_indicador_ins.descripcion 
FROM calidad.desc_indicador_ins 
INNER JOIN calidad.indicadores_ins ON desc_indicador_ins.id_desc_indicador_ins = indicadores_ins.id_desc_indicador_ins 
WHERE ( ((indicadores_ins.id_desc_indicador_ins)=$id_indi) AND ((indicadores_ins.anio)=$anio))
Order By indicadores_ins.mes ASC";
$sat_cliente= sql($sql) or fin_pagina();

$titulo=$sat_cliente->fields["descripcion"];

$datax=array("Ene","Feb","Mar","Abr","Mayo","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$datay=array("","","","","","","","","","","","");

$i=0;
while (!$sat_cliente->EOF){

$datay[($sat_cliente->fields['mes']-1)]=$sat_cliente->fields['valor'];	
$sat_cliente->MoveNext();
$i++;

}

switch ($tamaño) {
    case "small":
                $graph = new Graph(400,210,"auto");
                $graph->img->SetMargin(50,10,15,60);
                $tamaño_font=10;
                $tamaño_eje=8;
                break;
    case "large":
                $graph = new Graph(520,320,"auto");
                $graph->img->SetMargin(50,10,15,50);
                $tamaño_font=12;
                $tamaño_eje=9;
                break;

}

$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");
$graph->SetColor($bgcolor_out);


//Seteo el titulo para el grafico
$graph->title->Set($titulo);
$graph->title->SetFont(FF_COURIER,FS_BOLD,$tamaño_font);
$graph->title->SetColor($bgcolor1);

// Seteo fuente para los ejes x e y
$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,$tamaño_eje);
$graph->yaxis->SetFont(FF_COURIER,FS_NORMAL,$tamaño_eje);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(20);

// Se creo la barra de plot
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.6);

// Seteo color
$bplot->SetFillGradient("navy","#EEEEEE",GRAD_LEFT_REFLECTION);

// Set color for the frame of each bar
$bplot->SetColor("white");
$graph->Add($bplot);

// Se envia el grafico al navegador
$graph->Stroke();

?>

