<?php
/*
if (!empty($_GET['debug'])) {
	$debug = $_GET['debug'];
}
*/
set_time_limit(600);

require_once('profiler.inc');
$profiler = new Profiler(true,true);

require_once("gacl_admin.inc.php");

$query = "select 		a.value,
								a.name,
								b.value,
								b.name,

								c.value,
								c.name,
								d.value,
								d.name
					from 	aco_sections as a
						LEFT JOIN aco as b ON a.value=b.section_value,
						aro_sections as c
						LEFT JOIN aro as d ON c.value=d.section_value
					order by a.value, b.value, c.value, d.value";
//$rs = $db->Execute($query);
$rs = $db->pageexecute($query, $gacl_api->_items_per_page, $_GET['page']);
$rows = $rs->GetRows();

$total_rows = count($rows);

while (list(,$row) = @each(&$rows)) {
    list(	$aco_section_value,
			$aco_section_name,
			$aco_value,
			$aco_name,

			$aro_section_value,
			$aro_section_name,
			$aro_value,
			$aro_name
		) = $row;

	$acl_check_begin_time = $profiler->getMicroTime();
	$acl_result = $gacl->acl_query($aco_section_value, $aco_value, $aro_section_value, $aro_value);
	$acl_check_end_time = $profiler->getMicroTime();

	$access = &$acl_result['allow'];
	$return_value = &$acl_result['return_value'];

	$acl_check_time = ($acl_check_end_time - $acl_check_begin_time) * 100;
	$total_acl_check_time += $acl_check_time;

	if ($aco_section_name != $tmp_aco_section_name OR $aco_name != $tmp_aco_name) {
		$display_aco_name = "$aco_section_name > $aco_name";
	} else {
		$display_aco_name = "<br>";
	}

	$acls[] = array(
						'aco_section_value' => $aco_section_value,
						'aco_section_name' => $aco_section_name,
						'aco_value' => $aco_value,
						'aco_name' => $aco_name,

						'aro_section_value' => $aro_section_value,
						'aro_section_name' => $aro_section_name,
						'aro_value' => $aro_value,
						'aro_name' => $aro_name,

						'access' => $access,
						'return_value' => $return_value,
						'acl_check_time' => number_format($acl_check_time, 2),

						'display_aco_name' => $display_aco_name,
					);

	$tmp_aco_section_name = $aco_section_name;
	$tmp_aco_name = $aco_name;
}

//echo "<br><br>$x ACL_CHECK()'s<br>\n";

$smarty->assign("acls", $acls);

$smarty->assign("total_acl_checks", $total_rows);
$smarty->assign("total_acl_check_time", $total_acl_check_time);

if ($total_rows > 0) {
	$avg_acl_check_time = $total_acl_check_time / $total_rows;
}
$smarty->assign("avg_acl_check_time", number_format( ($avg_acl_check_time + 0) ,2));

$smarty->assign("paging_data", $gacl_api->get_paging_data($rs));

$smarty->assign("return_page", $_SERVER['PHP_SELF'] );

$smarty->display('phpgacl/acl_test.tpl');
?>
