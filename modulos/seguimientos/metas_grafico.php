<?php
require_once ("../../config.php");
include ("../../lib/imagenes_stat_2011/jpgraph.php");
include ("../../lib/imagenes_stat_2011/jpgraph_bar.php");

$dato = $parametros['dato'];
//$meta=$parametros['meta'];
//$metarrhh=$parametros['metarrhh'];
$metarrhh_s=$parametros['metarrhh_s'];
$tamaño=$parametros['tamaño'];
$nombre=$parametros['nombre'];
$cuie=$parametros['cuie'];


$datax=array("Dato","Meta");
$datay=array("","");

$datay[0]=$dato;
$datay[1]=$metarrhh_s;
//$datay[2]=$metarrhh;
//$datay[3]=$meta;

	
	


switch ($tamaño) {
    case "small":
                $graph = new Graph(300,210,"auto");
                $graph->img->SetMargin(40,10,15,60);
                $tamaño_font=10;
                $tamaño_eje=8;
                break;
    case "large":
                $graph = new Graph(420,320,"auto");
                $graph->img->SetMargin(40,10,15,50);
                $tamaño_font=12;
                $tamaño_eje=9;
                break;

}

$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");
$graph->SetColor($bgcolor_out);


//Seteo el titulo para el grafico
$graph->title->Set("Grafico");
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
//$bplot->SetFillGradient("navy","#EEEEEE",GRAD_LEFT_REFLECTION);
$bplot->SetFillGradient("#4B0082","#EEEEEE",GRAD_LEFT_REFLECTION);

// Set color for the frame of each bar
$bplot->SetColor("white");
$graph->Add($bplot);

// Se envia el grafico al navegador
//$graph->Stroke();

//para grabar los graficos
/*$fileName = "$nombre.png";
$graph->Stroke("$fileName");*/

$graph->Stroke(_IMG_HANDLER);

$fileName = nombre_archivo("$nombre$cuie.png");
$graph->img->Stream("graficos/$fileName");

// Mandarlo al navegador
$graph->img->Headers();
$graph->img->Stream();


?>

