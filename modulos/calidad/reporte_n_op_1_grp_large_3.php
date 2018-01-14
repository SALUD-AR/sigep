<?php 
require_once("../../config.php");
require_once ('../../lib/imagenes_stat_2011/jpgraph.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_line.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_bar.php');

$periodos_label_array = $parametros['periodos_label_array'];
$periodos_array = $parametros['periodos_array'];
$periodos_array_ant = $parametros['periodos_array_ant'];

// New graph with a drop shadow
$graph = new Graph(900,600,'auto');
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($periodos_label_array);

// Set title and subtitle y titulos de ejes
$graph->title->Set("Todos los Pacientes Agrupados por Diagnostico");
$graph->subtitle->Set("Prestaciones del Efector en el Periodo y Diagnostico Seleccionados Agrupados por Diagnostico (Discrimina el mismo periodo del año anterior)");
$graph->xaxis->title->Set("Periodos");
$graph->yaxis->title->Set("Cantidad de Prestaciones");

// Adjust the legend position

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Create the bar plot
$b1 = new BarPlot($periodos_array);
$b1->SetLegend("Periodo Seleccionado");

// Create a red line plot
$p1 = new BarPlot($periodos_array_ant);
$p1->SetLegend("Periodo Año Anterior");
$p1->SetFillColor("orange");
$p1->SetAbsWidth(15);


// The order the plots are added determines who's ontop
$graph->Add($b1);
$graph->Add($p1);

// Finally output the  image
$graph->Stroke();

?>