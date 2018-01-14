<?php
require_once("gacl_admin.inc.php");

switch ($_GET['action']) {
    case 'Search':
        $gacl_api->debug_text("Submit!!");
		
		$value_search_str = addslashes(trim($_GET['value_search_str']));
		$name_search_str = addslashes(trim($_GET['name_search_str']));
	
		if ($value_search_str != '') {
			$value_search_str .= '%';
		}
		
		if ($name_search_str != '') {
			$name_search_str .= '%';
		}

		//Search
        $query = "select 	section_value,
									value,
									name
							from ".$_GET['object_type']."
							where section_value = '".$_GET['section_value']."'
								AND 	(
											value LIKE '".$value_search_str."'
												OR name LIKE '".$name_search_str."'
											)
							order by section_value, order_value
							limit $gacl_api->_max_search_return_items";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

		$total_rows = $rs->RecordCount();
		
        while (list(,$row) = @each($rows)) {
            list($section_value, $value, $name) = $row;
            $options_objects[$value] = $name;
			
        }

		$smarty->assign("options_objects", $options_objects);
		
		$smarty->assign("total_rows", $total_rows);

        $smarty->assign("value_search_str", $_GET['value_search_str']);
        $smarty->assign("name_search_str", $_GET['name_search_str']);
		
        //break;    
    default:
		
        $smarty->assign("src_form", $_GET['src_form']);
        $smarty->assign("section_value", $_GET['section_value']);
        $smarty->assign("section_value_name", ucfirst($_GET['section_value']));
        $smarty->assign("object_type", $_GET['object_type']);
        $smarty->assign("object_type_name", strtoupper($_GET['object_type']));

        break;
}

$smarty->display('phpgacl/object_search.tpl');
?>
