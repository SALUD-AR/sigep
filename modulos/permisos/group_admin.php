<?php
require_once("gacl_admin.inc.php");

//GET takes precedence.
if ($_GET['group_type'] != '') {
	$group_type = $_GET['group_type'];
} else {
	$group_type = $_POST['group_type'];	
}

switch(strtolower(trim($group_type))) {
    case 'axo':
        $group_type = 'axo';
        $group_table = 'axo_groups';
        $group_map_table = 'groups_axo_map';
        break;
    default:
        $group_type = 'aro';
        $group_table = 'aro_groups';
        $group_map_table = 'groups_aro_map';
        break;
}

switch ($_POST['action']) {
    case 'Delete':
        //See edit_group.php    
        break;
    default:
        $formatted_groups = $gacl_api->format_groups($gacl_api->sort_groups($group_type), HTML);

        $query = "select a.id, count(*) from $group_table as a, $group_map_table as b where a.id=b.group_id group by a.id";
        $rs = $db->Execute($query);

        $rows = $rs->GetRows();
        foreach ($rows as $row) {
            $id = $row[0];
            $count = $row[1];
            
            $object_count[$id] = $count;
        }
        
        //showarray($);
        while (list($id,$name) = @each($formatted_groups)) {
            //list($id, $name) = $row;
            
                $groups[] = array(
                                                'id' => $id,
                                                'parent_id' => $parent_id,
                                                'family_id' => $family_id,
                                                'name' => $name,
                                                'object_count' => $object_count[$id] + 0
                                            );
        }

        $smarty->assign('groups', $groups);

        break;
}

$smarty->assign('group_type', $group_type);
$smarty->assign('return_page', $_SERVER['REQUEST_URI']);

$smarty->display('phpgacl/group_admin.tpl');
?>
