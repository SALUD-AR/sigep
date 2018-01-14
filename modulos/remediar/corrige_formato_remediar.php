<?php

$archivo='br_txt.txt';

$row = 1;   
$handle = fopen("$archivo", "r");
$cont='"H";17/06/2011;"PC_RENUEV";20;701;00701;"";';
$cont.="
";
while (($data = fgetcsv($handle, 10000,";")) !== FALSE) {
    $num = count($data);

$cont.='"'.$data[0].'";';
$cont.='"'.$data[1].'";';
$cont.='"'.$data[2].'";';
$cont.='"'.$data[3].'";';
$cont.='"'.$data[4].'";';
$cont.='"'.$data[5].'";';
$cont.=''.$data[6].';';
$cont.='"'.$data[7].'";';
$cont.='"'.$data[8].'";';
$cont.=''.$data[9].';';
$cont.='"'.$data[10].'";';
$cont.='"'.$data[11].'";';
$cont.='"'.$data[12].'";';
$cont.='"'.$data[13].'";';
$cont.=''.$data[14].';';
$cont.='"'.$data[15].'";';
$cont.='"'.$data[16].'";';
$cont.='"'.$data[17].'";';
$cont.=''.$data[18].';';
$cont.='"'.$data[19].'";';
$cont.='"'.$data[20].'";';
$cont.='"'.$data[21].'";';
$cont.=''.$data[22].';';
$cont.='"'.$data[23].'";';
$cont.='"'.$data[24].'";';
$cont.=''.$data[25].';';
$cont.=''.$data[26].';';
$cont.=''.$data[27].';';
$cont.=''.$data[28].';';
$cont.=''.$data[29].';';
$cont.=''.$data[30].';';
$cont.='"'.$data[31].'";';
$cont.='"'.$data[32].'";';
$cont.='"'.$data[33].'";';
$cont.='"'.$data[34].'";';
$cont.='"'.$data[35].'";';
$cont.='"'.$data[36].'";';
$cont.='"'.$data[37].'";';
$cont.='"'.$data[38].'";';
$cont.='"'.$data[39].'";';
$cont.='"'.$data[40].'";';
$cont.='"'.$data[41].'";';
$cont.='"'.$data[42].'";';
$cont.='"'.$data[43].'";';
$cont.='"'.$data[44].'";';
$cont.='"'.$data[45].'";';
$cont.='"'.$data[46].'";';
$cont.=''.$data[47].';';
$cont.=''.$data[48].';';
$cont.='"'.$data[49].'";';
$cont.='"'.$data[50].'";';
$cont.='"'.$data[51].'";';
$cont.='"'.$data[52].'";';
$cont.=''.$data[53].';';
$cont.='"'.$data[54].'";';
$cont.='"'.$data[55].'";';
$cont.='"'.$data[56].'";';
$cont.='"'.$data[57].'";';
$cont.=''.$data[58].';';
$cont.='"'.$data[59].'";';
$cont.=''.$data[60].';';
$cont.='"'.$data[61].'";';
$cont.=''.$data[62].';';
$cont.='"'.$data[63].'";';
$cont.=''.$data[64].';';
$cont.='"'.$data[65].'";';
$cont.=''.$data[66].';';
//$cont.=str_replace('"','',$data[67]);
$cont.="
";

$row++; 

}
$cont.='"T";7555;';

//echo $cont;

$filename_remediar= 'BR20201105.txt';
$handle2 = fopen($filename_remediar, "w");


     fwrite($handle2,$cont);
	fclose($handle2);
 
		fclose($handle);
?>