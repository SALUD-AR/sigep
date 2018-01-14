<?php 
require_once("../../config.php");
require_once ('../../lib/imagenes_stat_2011/jpgraph.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_line.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_bar.php');

$periodos_label_array = $parametros['periodos_label_array'];
$periodos_array = $parametros['periodos_array'];
$periodos_array_ant = $parametros['periodos_array_ant'];
$meta_men = $parametros['meta_men'];
$meta_acu = $parametros['meta_acu'];
$meta_porcent = $parametros['meta_porcent'];

// New graph with a drop shadow
$graph = new Graph(1024,400,'auto');
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($periodos_label_array);

// Set title and subtitle y titulos de ejes
$graph->title->Set("Avance de Metas Anual Por Vacunas y Dosis");
$graph->xaxis->title->Set("Meses");
$graph->yaxis->title->Set("Cantidad de Aplicaciones");

// Adjust the legend position

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$p1 = new LinePlot($meta_men);
$p1->SetLegend("META MENSUAL");
$p1->SetColor("#08298A");
$p1->SetWeight(20);

$b1 = new BarPlot($periodos_array);
$b1->SetLegend("TOTAL MENSUAL");
$b1->SetFillColor("#0040FF");
$b1->SetAbsWidth(30);

$c1 = new BarPlot($periodos_array_ant);
$c1->SetLegend("TOTAL ACUMULADO");
$c1->SetFillColor("#479CC9");
$c1->SetAbsWidth(15);

$a1 = new BarPlot($meta_acu);
$a1->SetLegend("META ACUMULADA");
$a1->SetFillColor("#F6CEF5");
$a1->SetAbsWidth(30);


// The order the plots are added determines who's ontop
$graph->Add($p1);
$graph->Add($a1);
$graph->Add($b1);
$graph->Add($c1);


// Finally output the  image
$graph->Stroke();
?>