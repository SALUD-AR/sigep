<?php
require_once("gacl_admin.inc.php");

if (!isset($_POST['action']) ) {
	$_POST['action'] = FALSE;
}

if (!isset($_GET['action']) ) {
	$_GET['action'] = FALSE;
}

switch ($_POST['action']) {
    case 'Delete':
        break;
    case 'Enviar':
        $gacl_api->debug_text("Submit!!");
		//showarray($_POST['selected_aco']);
		//showarray($_POST['selected_aro']);

		//Parse the form values
		//foreach ($_POST['selected_aco'] as $aco_value) {
		while (list(,$aco_value) = @each($_POST['selected_aco'])) {
				$split_aco_value = explode("^", $aco_value);
				$selected_aco_array[$split_aco_value[0]][] = $split_aco_value[1];
		}
		//showarray($selected_aco_array);

		//Parse the form values
		//foreach ($_POST['selected_aro'] as $aro_value) {
		while (list(,$aro_value) = @each($_POST['selected_aro'])) {
				$split_aro_value = explode("^", $aro_value);
				$selected_aro_array[$split_aro_value[0]][] = $split_aro_value[1];
		}
		//showarray($selected_aro_array);

		while (list(,$axo_value) = @each($_POST['selected_axo'])) {
				$split_axo_value = explode("^", $axo_value);
				$selected_axo_array[$split_axo_value[0]][] = $split_axo_value[1];
		}
		//showarray($selected_axo_array);

		//Some sanity checks.
		if (count($selected_aco_array) == 0) {
			echo "Must select at least one Access Control Object<br>\n";
			exit;
		}

		if (count($selected_aro_array) == 0 AND count($_POST['aro_groups']) == 0) {
			echo "Must select at least one Access Request Object or Group<br>\n";
			exit;
		}

		$enabled = $_POST['enabled'];
		if (empty($enabled)) {
			$enabled=0;
		}

		//function add_acl($aco_array, $aro_array, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $allow=1, $enabled=1, $acl_id=FALSE ) {
		if (!empty($_POST['acl_id']) ) {
			//Update existing ACL
			$acl_id = $_POST['acl_id'];
			if ($gacl_api->edit_acl($acl_id, $selected_aco_array, $selected_aro_array, $_POST['aro_groups'], $selected_axo_array, $_POST['axo_groups'], $_POST['allow'], $enabled, $_POST['return_value'], $_POST['note']) == FALSE) {
				echo "ERROR editing ACL, possible conflict or error found...<br>\n";
				exit;
			}
		} else {
			//Insert new ACL.
			if ($gacl_api->add_acl($selected_aco_array, $selected_aro_array, $_POST['aro_groups'], $selected_axo_array, $_POST['axo_groups'], $_POST['allow'], $enabled, $_POST['return_value'], $_POST['note']) == FALSE) {
				echo "ERROR adding ACL, possible conflict or error found...<br>\n";
				exit;
			}
		}

        $gacl_api->return_page($_POST['return_page']);

        break;
    default:
		//showarray($_GET);
		if ($_GET['action'] == 'edit' AND !empty($_GET['acl_id']) ) {
			$gacl_api->debug_text("EDITING ACL");

			//Grab ACL information
			$query = "select id, allow, enabled, return_value, note from acl where id = ".$_GET['acl_id']."";
			$acl_row = $db->GetRow($query);
			list($acl_id, $allow, $enabled, $return_value, $note) = $acl_row;

			//Grab selected ACO's
			$query = "select a.section_value, a.value, c.name, b.name from aco_map a, aco b, aco_sections c
					where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id
			        ";
			$rs = $db->Execute($query);
			$rows = $rs->GetRows();

			while (list(,$row) = @each($rows)) {
				list($section_value, $value, $section, $aco) = $row;
				$gacl_api->debug_text("Section Value: $section_value Value: $value Section: $section ACO: $aco");

				$options_selected_aco[$section_value.'^'.$value] = "$section > $aco";

			}
			//showarray($options_aco);

			//Grab selected ARO's
			$query = "select a.section_value, a.value, c.name, b.name from aro_map a, aro b, aro_sections c
					  where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id
			          ";
			$rs = $db->Execute($query);
			$rows = $rs->GetRows();

			while (list(,$row) = @each($rows)) {
				list($section_value, $value, $section, $aro) = $row;
				$gacl_api->debug_text("Section Value: $section_value Value: $value Section: $section ARO: $aro");

				$options_selected_aro[$section_value.'^'.$value] = "$section > $aro";

			}
			//showarray($options_aro);

			//Grab selected AXO's
			$query = "select a.section_value, a.value, c.name, b.name from axo_map a, axo b, axo_sections c
					 where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id";
			$rs = $db->Execute($query);
			$rows = $rs->GetRows();

			while (list(,$row) = @each($rows)) {
				list($section_value, $value, $section, $axo) = $row;
				$gacl_api->debug_text("Section Value: $section_value Value: $value Section: $section AXO: $axo");

				$options_selected_axo[$section_value.'^'.$value] = "$section > $axo";

			}
			//showarray($options_aro);

			//Grab selected ARO groups.
			$query = "select group_id from aro_groups_map where  acl_id = $acl_id";
			$selected_aro_groups = $db->GetCol($query);
			//showarray($selected_groups);

			//Grab selected AXO groups.
			$query = "select group_id from axo_groups_map where  acl_id = $acl_id";
			$selected_axo_groups = $db->GetCol($query);
			//showarray($selected_groups);

		} else {
			$gacl_api->debug_text("NOT EDITING ACL");
			$allow=1;
			$enabled=1;
		}


        //
        //Grab all ACO sections for select box
        //
        $query = "select value, name from aco_sections where hidden = 0 order by order_value";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        $i=0;
        while (list(,$row) = @each($rows)) {
            list($id, $value) = $row;

            if ($i==0) {
                $aco_section_id=$id;
            }
            $options_aco_sections[$id] = $value;

            $i++;
        }

        //
        //Grab all ARO sections for select box
        //
        // ahora ordena x nombres 
        $query = "select value, name from aro_sections where hidden = 0 order by name ";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        $i=0;
        while (list(,$row) = @each($rows)) {
            list($id, $value) = $row;

            if ($i==0) {
                $aro_section_id=$id;
            }

            $options_aro_sections[$id] = $value;

            $i++;
        }

        //
        //Grab all AXO sections for select box
        //
        $query = "select value, name from axo_sections where hidden = 0 order by order_value";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        $i=0;
        while (list(,$row) = @each($rows)) {
            list($id, $value) = $row;

            if ($i==0) {
                $axo_section_id=$id;
            }

            $options_axo_sections[$id] = $value;

            $i++;
        }

        //
        //Grab all ACO's for select box
        //
        //$query = "select section_value, value, name from aco where hidden = 0 order by section_value, order_value";
        // ahora ordena x nombres
        $query = "select section_value, value, name from aco where hidden = 0 order by section_value, name";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        //Init the main js array
        $js_aco_array = "var options = new Array();\n";

        $js_aco_array_name = "aco";

        //Init the main aco js array.
        $js_aco_array .= "options['$js_aco_array_name'] = new Array();\n";
        $gacl_api->debug_text("Blah1");
        while (list(,$row) = @each($rows)) {
            list($section_value, $value, $name) = $row;

            //Prepare javascript code for dynamic select box.
            //Init the javascript sub-array.
            if (!isset($tmp_section_value) OR $section_value != $tmp_section_value) {
                $i=0;

                $js_aco_array .= "options['$js_aco_array_name']['$section_value'] = new Array();\n";
            }

            //Add each select option for the section
            $js_aco_array .= "options['$js_aco_array_name']['$section_value'][$i] = new Array('$value', '$name');\n";

            $tmp_section_value = $section_value;
            $i++;
        }
        unset($section_value);
        unset($tmp_section_value);

        //
        //Grab all ARO's for select box
        //
//        $query = "select section_value, value, name from aro  where hidden = 0 order by section_value, order_value limit $gacl_api->_max_select_box_items";
        // $query = "select section_value, value, name from aro  where hidden = 0 order by section_value, order_value";
        // ordena x nombres en cada uno d los grupos
        $query = "select section_value, value, name from aro  where hidden = 0 order by section_value, name";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        $js_aro_array_name = "aro";
        //Init the main aro js array.
        $js_aro_array = "options['$js_aro_array_name'] = new Array();\n";
        while (list(,$row) = @each($rows)) {
            list($section_value, $value, $name) = $row;

            //Prepare javascript code for dynamic select box.
            //Init the javascript sub-array.
            if (!isset($tmp_section_value) OR $section_value != $tmp_section_value) {
                $i=0;

                $js_aro_array .= "options['$js_aro_array_name']['$section_value'] = new Array();\n";
            }

            //Add each select option for the section
            $js_aro_array .= "options['$js_aro_array_name']['$section_value'][$i] = new Array('$value', '$name');\n";

            $tmp_section_value = $section_value;
            $i++;
        }
        unset($section_value);
        unset($tmp_section_value);

        //
        //Grab all AXO's for select box
        //
//        $query = "select section_value, value, name from axo  where hidden = 0 order by section_value, order_value limit $gacl_api->_max_select_box_items";
        $query = "select section_value, value, name from axo  where hidden = 0 order by section_value, order_value";
        $rs = $db->Execute($query);
        $rows = $rs->GetRows();

        $js_axo_array_name = "axo";
        //Init the main aro js array.
        $js_axo_array = "options['$js_axo_array_name'] = new Array();\n";
        while (list(,$row) = @each($rows)) {
            list($section_value, $value, $name) = $row;

            //Prepare javascript code for dynamic select box.
            //Init the javascript sub-array.
            if (!isset($tmp_section_value) OR $section_value != $tmp_section_value) {
                $i=0;

                $js_axo_array .= "options['$js_axo_array_name']['$section_value'] = new Array();\n";
            }

            //Add each select option for the section
            $js_axo_array .= "options['$js_axo_array_name']['$section_value'][$i] = new Array('$value', '$name');\n";

            $tmp_section_value = $section_value;
            $i++;
        }
        unset($section_value);
        unset($tmp_section_value);

        $smarty->assign("options_axo_sections", $options_axo_sections);
        $smarty->assign("axo_section_value", $axo_section_value);

        $smarty->assign("options_aro_sections", $options_aro_sections);
        $smarty->assign("aro_section_value", $aro_section_value);

        $smarty->assign("options_aco_sections", $options_aco_sections);
        $smarty->assign("aco_section_value", $aco_section_value);

        $smarty->assign("js_aro_array", $js_aro_array);
        $smarty->assign("js_aro_array_name", $js_aro_array_name);

        $smarty->assign("js_aco_array", $js_aco_array);
        $smarty->assign("js_aco_array_name", $js_aco_array_name);

        $smarty->assign("js_axo_array", $js_axo_array);
        $smarty->assign("js_axo_array_name", $js_axo_array_name);

        //Grab formatted ARO Groups for select box
        $smarty->assign("options_aro_groups", $gacl_api->format_groups($gacl_api->sort_groups('ARO')) );
		$smarty->assign("selected_aro_groups", $selected_aro_groups);

		unset($formatted_groups);
        //Grab formatted AXO Groups for select box
        $smarty->assign("options_axo_groups", $gacl_api->format_groups($gacl_api->sort_groups('AXO')) );
		$smarty->assign("selected_axo_groups", $selected_axo_groups);

		$smarty->assign("allow", $allow);
		$smarty->assign("enabled", $enabled);
		$smarty->assign("return_value", $return_value);
		$smarty->assign("note", $note);

		if (isset($options_selected_aco)) {
			$smarty->assign("options_selected_aco", $options_selected_aco);
		}
		$smarty->assign("selected_aco", @array_keys($options_selected_aco));

		if (isset($options_selected_aro)) {
			$smarty->assign("options_selected_aro", $options_selected_aro);
		}
		$smarty->assign("selected_aro", @array_keys($options_selected_aro));

		if (isset($options_selected_axo)) {
			$smarty->assign("options_selected_axo", $options_selected_axo);
		}
		$selected_axo = @array_keys($options_selected_axo);

		$smarty->assign("selected_axo", $selected_axo);
		//Show AXO layer if AXO's are selected.
		if (count($selected_axo) > 0) {
			$smarty->assign("show_axo", TRUE);
		}

		if (isset($_GET['acl_id'])) {
			$smarty->assign("acl_id", $_GET['acl_id'] );
		}

        break;
}

//$smarty->assign("return_page", urlencode($_SERVER[REQUEST_URI]) );
if (isset($_GET['return_page'])) {
	$smarty->assign("return_page", $_GET['return_page']);
}
if (isset($_GET['action'])) {
	$smarty->assign("action", $_GET['action']);
}

$smarty->display('phpgacl/acl_admin.tpl');
?>