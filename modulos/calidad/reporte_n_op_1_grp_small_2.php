<?php // content="text/plain; charset=utf-8"
require_once ("../../config.php");
require_once ('../../lib/imagenes_stat_2011/jpgraph.php');
require_once ('../../lib/imagenes_stat_2011/jpgraph_pie.php');

$desc_completa = $parametros['desc_completa'];
$val_pres=$parametros['val_pres'];

// A new pie graph
$graph = new PieGraph(800,500);
$graph->SetShadow();

// Title setup
// Set title and subtitle y titulos de ejes
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->title->Set("Agrupadas por Prestacion");
$graph->subtitle->Set("Prestaciones del Efector en el Periodo y Diagnostico Seleccionado Agrupadas por Prestacion");

// Setup the pie plot
$p1 = new PiePlot($val_pres);

$graph->legend->Pos(0.5,0.9,"center","center");
$p1->SetLegends($desc_completa);

// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.5,0.82);

// Setup slice labels and move them into the plot
$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("darkred");
$p1->SetLabelPos(0.65);

// Explode all slices
$p1->ExplodeAll(10);

// Add drop shadow
$p1->SetShadow();

// Finally add the plot
$graph->Add($p1);

// ... and stroke it
$graph->Stroke();

?>
