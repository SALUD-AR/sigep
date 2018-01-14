<?php
require_once("gacl_admin.inc.php");

switch ($_GET['action']) {
    case 'Enviar':
        $gacl_api->debug_text("Submit!!");
		//$result = $gacl_api->acl_query('system', 'email_pw', 'users', '1', NULL, NULL, NULL, NULL, TRUE);
/*
		$result = $gacl_api->acl_query(	$_GET['aco_section_value'],
															$_GET['aco_value'],
															$_GET['aro_section_value'],
															$_GET['aro_value'],
															$_GET['axo_section_value'],
															$_GET['axo_value'],
															$_GET['root_aro_group_id'],
															$_GET['root_axo_group_id'],
															TRUE);
*/
		$result = $gacl_api->acl_query(	$_GET['aco_section_value'],
															$_GET['aco_value'],
															$_GET['aro_section_value'],
															$_GET['aro_value'],
															NULL,
															NULL,
															$_GET['root_aro_group_id'],
															NULL,
															TRUE);

		//Grab all relavent columns
		$result['query'] = str_replace(	"a.id,a.allow,a.return_value",
														"	a.id,
															a.allow,
															a.return_value,
															a.note,
															a.updated_date,
															b.section_value as aco_section_value,
															b.value as aco_value,
															c.section_value as aro_section_value,
															c.value as aro_value,
															h.section_value as axo_section_value,
															h.value as axo_value
															",$result['query']);
//															d.group_id aro_group_id,
//															e.tree_level aro_tree_level
//															f.group_id axo_group_id,
//															g.tree_level axo_tree_level

		$rs = $gacl_api->db->Execute($result['query']);
		$rows = $rs->GetRows();

		while (list(,$row) = @each(&$rows)) {
			list(
					$id,
					$allow,
					$return_value,
					$note,
					$updated_date,

					$aco_section_value,
					$aco_value,

					$aro_section_value,
					$aro_value,

					$axo_section_value,
					$axo_value,

					$aro_group_id,
					$aro_tree_level
				) = $row;

			$acls[] = array(
								'id' => $id,
								'allow' => $allow,
								'return_value' => $return_value,
								'note' => $note,
								'updated_date' => date("d/m/Y H:m:i",$updated_date),

								'aco_section_value' => $aco_section_value,
								'aco_value' => $aco_value,

								'aro_section_value' => $aro_section_value,
								'aro_value' => $aro_value,

								'axo_section_value' => $axo_section_value,
								'axo_value' => $axo_value,

								'aro_group_id' => $aro_group_id,
								'aro_tree_level' =>$aro_tree_level
							);
		}

		//echo "<br><br>$x ACL_CHECK()'s<br>\n";

		$smarty->assign("acls", $acls);

		$smarty->assign("aco_section_value", $_GET['aco_section_value']);
		$smarty->assign("aco_value", $_GET['aco_value']);
		$smarty->assign("aro_section_value", $_GET['aro_section_value']);
		$smarty->assign("aro_value", $_GET['aro_value']);
//		$smarty->assign("axo_section_value", $_GET['axo_section_value']);
//		$smarty->assign("axo_value", $_GET['axo_value']);
		$smarty->assign("root_aro_group_id", $_GET['root_aro_group_id']);
//		$smarty->assign("root_axo_group_id", $_GET['root_axo_group_id']);

        break;

    default:
		break;
}

$smarty->assign("return_page", $_SERVER['PHP_SELF'] );

$smarty->display('phpgacl/acl_debug.tpl');
?>
