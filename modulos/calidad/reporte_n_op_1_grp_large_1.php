<?php // content="text/plain; charset=utf-8"
require_once ("../../config.php");
require_once ('../../lib/imagenes_stat_2011/jpgraph.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_bar.php');

$desc = $parametros['desc'];
$desc1 = $parametros['desc1'];
$val=$parametros['val'];

$graph = new Graph(1200,850,'auto');
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($desc1);
$graph->xaxis->SetLabelAngle(50);

// Set title and subtitle y titulos de ejes
$graph->title->Set("Agrupadas por Objeto de la Prestacion");
$graph->subtitle->Set("Prestaciones del Efector en el Periodo y Diagnostico Seleccionado Agrupadas por Objeto de la Prestacion");
$graph->yaxis->title->Set("Cantidad de Prestaciones");

$graph->img->SetMargin(95,25,40,190);

// Adjust the legend position

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Create the bar plot
$b1 = new BarPlot($val);

// The order the plots are added determines who's ontop
$graph->Add($b1);

// Finally output the  image
$graph->Stroke();

?>