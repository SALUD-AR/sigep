<?php
/*
$Author: nazabal $
$Revision: 1.9 $
$Date: 2004/08/30 19:39:38 $
*/

/*
 * phpGACL - Generic Access Control List
 * Copyright (C) 2002,2003 Mike Benoit
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please join the
 * phpGACL mailing list. http://sourceforge.net/mail/?group_id=57103
 *
 * You may contact the author of phpGACL by e-mail at:
 * ipso@snappymail.ca
 *
 * The latest version of phpGACL can be obtained from:
 * http://phpgacl.sourceforge.net/
 *
 */

/*
 * Path to ADODB.
 */
/*if ( !defined('ADODB_DIR') ) {
	define('ADODB_DIR', dirname(__FILE__).'/adodb');
}
*/
class gacl {
	// --- Private properties ---

	/*
	 * Enable Debug output.
	 */
	var $_debug = FALSE;

	/*
	 * Database configuration.
	 */
	var $_db_table_prefix = '';
	var $_db_type = ''; //mysql, postgres7, sybase, oci8po See here for more: http://php.weblogs.com/adodb_manual#driverguide
	var $_db_host = '';
	var $_db_user = '';
	var $_db_password = '';
	var $_db_name = '';
	var $_db_link = '';
   var $db;

	/*
	 * NOTE: 	This cache must be manually cleaned each time ACL's are modified.
	 * 			Alternatively you could wait for the cache to expire.
	 */
	var $_caching = FALSE;
	var $_force_cache_expire = TRUE;
	var $_cache_dir = '/tmp/phpgacl_cache'; // NO trailing slash
	var $_cache_expire_time=600; //600 == Ten Minutes

	/*
	 * Constructor
	 */
	function gacl($options = NULL) {
   	global $db;
       $this->db = $db;
		if ($this->_debug OR isset($_GET['debug'])) {
			$this->_debug=TRUE;
		}

		$available_options = array('debug','items_per_page','max_select_box_items','max_search_return_items','db_table_prefix','db_type','db_host','db_user','db_password','db_name','caching','force_cache_expire','cache_dir','cache_expire_time');
		if (is_array($options)) {
			foreach ($options as $key => $value) {
					$this->debug_text("Option: $key - $value");

					if (in_array($key, $available_options) ) {
						$this->debug_text("Valid Config options: $key - $value");
						$property = '_'.$key;
						$this->$property = $value;
					} else {
						$this->debug_text("ERROR: Config option: $key is not a valid option");
					}
			}
		}
/*
		require_once( ADODB_DIR .'/adodb.inc.php');
		require_once( ADODB_DIR .'/adodb-pager.inc.php');
*/
		//Use NUM for slight performance/memory reasons.
		//Leave this in for backwards compatibility with older ADODB installations.
		//If your using ADODB v3.5+ feel free to comment out the following line if its giving you problems.
//		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
/*
		$this->db = ADONewConnection($this->_db_type);
		$this->db->SetFetchMode(ADODB_FETCH_NUM);
		$this->db->Connect($this->_db_host, $this->_db_user, $this->_db_password, $this->_db_name);
*/
//		$this->db->debug = $this->_debug;

		if (!class_exists('Hashed_Cache_Lite')) {
			require_once(dirname(__FILE__) .'/Cache_Lite/Hashed_Cache_Lite.php');
		}

		/*
		 * Cache options. We default to the highest performance. If you run in to cache corruption problems,
		 * Change all the 'false' to 'true', this will slow things down slightly however.
		 */
		$cache_options = array(
			'caching' => $this->_caching,
			'cacheDir' => $this->_cache_dir.'/',
			'lifeTime' => $this->_cache_expire_time,
			'fileLocking' => true,
			'writeControl' => false,
			'readControl' => false,
		);
		$this->Cache_Lite = new Hashed_Cache_Lite($cache_options);

		return true;
	}

	/*======================================================================*\
		Function:   $gacl_api->debug_text()
		Purpose:    Prints debug text if debug is enabled.
	\*======================================================================*/
	function debug_text($text) {

		if ($this->_debug) {
			echo "$text<br>\n";
		}

		return true;
	}

	/*======================================================================*\
		Function:   acl_check()
		Purpose:	Function that wraps the actual acl_query() function.
						It is simply here to return TRUE/FALSE accordingly.
	\*======================================================================*/
	function acl_check($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL, $root_aro_group_id=NULL, $root_axo_group_id=NULL) {
		$acl_result = $this->acl_query($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value, $axo_value, $root_aro_group_id, $root_axo_group_id);

		return $acl_result['allow'];
	}

	/*======================================================================*\
		Function:   acl_return_value()
		Purpose:	Function that wraps the actual acl_query() function.
						Quick access to the return value of an ACL.
	\*======================================================================*/
	function acl_return_value($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL, $root_aro_group_id=NULL, $root_axo_group_id=NULL) {
		$acl_result = $this->acl_query($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value, $axo_value, $root_aro_group_id, $root_axo_group_id);

		return $acl_result['return_value'];
	}

	/*======================================================================*\
		Function:   acl_query()
		Purpose:	Main function that does the actual ACL lookup.
						Returns as much information as possible about the ACL, so this is the function
						that should be used if you want to "hook" in other applications. Such as pricing
						etc...
	\*======================================================================*/
	function acl_query($aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL, $root_aro_group_id=NULL, $root_axo_group_id=NULL, $debug=NULL) {

		$cache_id = $aco_section_value.'-'.$aco_value.'-'.$aro_section_value.'-'.$aro_value.'-'.$axo_section_value.'-'.$axo_value.'-'.$root_aro_group_id.'-'.$root_axo_group_id.'-'.$debug;

		$retarr = $this->get_cache($cache_id);

		if (!$retarr) {
			/*
			 * Grab all groups mapped to this ARO/AXO
			 */
			$aro_group_ids = $this->acl_get_groups($aro_section_value, $aro_value, $root_aro_group_id,'ARO');
			if ($axo_section_value != '' AND $axo_value != '') {
				$axo_group_ids = $this->acl_get_groups($axo_section_value, $axo_value, $root_axo_group_id,'AXO');
			}

			/*
			 * Grab the path_to_root for all the above group parents.
			 * This is so we can perform the group inheritance.
			 */
			$aro_path_ids = $this->acl_get_group_path($aro_group_ids['parent_ids'], 'ARO');
			if ($axo_section_value != '' AND $axo_value != '') {
				$axo_path_ids = $this->acl_get_group_path($axo_group_ids['parent_ids'], 'AXO');
			}

			/*
			 * Generate SQL text for SQL's in () statements
			 */
			if ( isset($aro_group_ids['group_ids']) AND is_array($aro_group_ids['group_ids']) ) {
				$sql_aro_group_ids = implode(",", $aro_group_ids['group_ids']);
			}

			if ( isset($aro_path_ids) AND is_array($aro_path_ids) ) {
				$sql_aro_path_ids = implode(",", $aro_path_ids);
			}

			if ( isset($axo_group_ids['group_ids']) AND is_array($axo_group_ids['group_ids']) ) {
				$sql_axo_group_ids = implode(",", $axo_group_ids['group_ids']);
			}

			if ( isset($axo_path_ids) AND is_array($axo_path_ids) ) {
				$sql_axo_path_ids = implode(",", $axo_path_ids);
			}

			/*
			 * This query is where all the magic happens.
			 * The ordering is very important here, as well very tricky to get correct.
			 * Currently there can be  duplicate ACLs, or ones that step on each other toes. In this case, the ACL that was last updated/created
			 * is used.
			 *
			 * This is probably where the most optimizations can be made.
			 */

			/*
			These are currently useless to us.
												a.updated_date,
												b.aco_id,
												c.aro_id,
												d.group_id
			*/
			$query ="
								select a.id,a.allow,a.return_value
									from    acl a
										LEFT JOIN aco_map b ON a.id=b.acl_id
										LEFT JOIN aro_map c ON a.id=c.acl_id
										LEFT JOIN axo_map h ON a.id=h.acl_id";

			//If there are no groups, don't bother doing the join.
			if (isset($sql_aro_group_ids)) {
				$query .= "        LEFT JOIN aro_groups_map d ON a.id=d.acl_id";
			}

			if (isset($sql_aro_path_ids)) {
				$query .= "		LEFT JOIN aro_groups_path e ON d.group_id=e.group_id";
			}

			if (isset($sql_axo_group_ids)) {
				$query .= "        LEFT JOIN axo_groups_map f ON a.id=f.acl_id";
			}

			if (isset($sql_axo_path_ids)) {
				$query .= "		LEFT JOIN axo_groups_path g ON f.group_id=g.group_id";
			}

			$query .= "       where   a.enabled = 1
												AND ( b.section_value = '$aco_section_value' AND b.value = '$aco_value' )
												AND	( (c.section_value= '$aro_section_value' AND c.value = '$aro_value') ";

			if (isset($sql_aro_group_ids)) {
				$query .= "								OR d.group_id in ($sql_aro_group_ids)";
			}

			if (isset($sql_aro_path_ids)) {
				$query .= "								OR e.id in ($sql_aro_path_ids) ";
			}

			if ($axo_section_value == '' AND $axo_value == '') {
				$query .= "					)
													AND ( ( h.section_value is NULL AND h.value is NULL ) ";

			} else {
				$query .= "					)
													AND ( ( h.section_value = '$axo_section_value' AND h.value = '$axo_value' ) ";
			}

			if (isset($sql_axo_group_ids)) {
				$query .= "								OR f.group_id in ($sql_axo_group_ids)";
			}

			if (isset($sql_axo_path_ids)) {
				$query .= "								OR g.id in ($sql_axo_path_ids) ";
			}


			/*
			 * The ordering is always very tricky and makes all the difference in the world.
			 * Order c.aro_value is not null desc should put ACL's given to specific ARO's
			 * ahead of any ACLs given to groups. This works well for exceptions to groups.
			 */
			$query .=                           "	)
									order by c.value is not null desc,";

			/*
			 * Tree levels are 0 furthest from root. The highest value is always the root, and this
			 * can of course differ depending on the depth of the tree. Because of this, we want to
			 * prefer (order) permissions by level asc, putting the deepest groups first.
			 * However due to this method, the deepest group does not have a level at all, so we need
			 * need an exception for it, which is the "null" part of the ordering.
			 */
			if (isset($sql_aro_path_ids)) {
				$query .= "										e.tree_level is null desc, e.tree_level asc, ";
			}
			if (isset($sql_axo_path_ids)) {
				$query .= "										g.tree_level is null desc, g.tree_level asc, ";
			}

			$query .= "											a.updated_date desc";

			if ($debug != TRUE) {
				$query .= "		limit 1";
			}

			//$rs = &$this->db->CacheExecute($query);
			$rs = &$this->db->Execute($query);
			$row = &$rs->GetRows();

			/*
			 * Permission granted?
			 * Now why did I not choose to use TRUE/FALSE in the first place?
			 */
			if ( isset($row[0][1]) AND $row[0][1] == 1 ) {
				$allow = TRUE;
			} else {
				$allow = FALSE;
			}

			/*
			 * Return ACL ID. This is the key to "hooking" extras like pricing assigned to ACLs etc... Very useful.
			 */
			if (is_array($row)) {
				$retarr = array('acl_id' => &$row[0][0], 'return_value' => &$row[0][2], 'allow' => &$allow);
			} else {
				$retarr = array('acl_id' => NULL, 'return_value' => NULL, 'allow' => &$allow);
			}
			/*
			 * Return the query that we ran if in debug mode.
			 */
			if ($debug == TRUE) {
				$retarr['query'] = &$query;
			}

			//Cache data.
			$this->put_cache($retarr, $cache_id);
		}

		$this->debug_text("<b>acl_query():</b> ACO Section: $aco_section_value ACO Value: $aco_value ARO Section: $aro_section_value ARO Value $aro_value ACL ID: ". $retarr['acl_id'] ." Result: ". $row[0][1]."");
		return $retarr;
	}

	/*======================================================================*\
		Function:   acl_get_groups()
		Purpose:	Grabs all groups mapped to an ARO. You can also specify a root_group_id for subtree'ing.
	\*======================================================================*/
	function acl_get_groups($section_value, $value, $root_group_id=NULL, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$group_table = 'axo_groups';
				$group_map_table = 'groups_axo_map';
				$group_path_table = 'axo_groups_path';
				break;
			default:
				$group_table = 'aro_groups';
				$group_map_table = 'groups_aro_map';
				$group_path_table = 'aro_groups_path';
				break;
		}

		//$profiler->startTimer( "acl_get_groups()");

		/*
		* Dirty way to default to false.
		*/
		$retarr['group_ids'] = FALSE;
		$retarr['parent_ids'] = FALSE;

		//Generate unique cache id.
		$cache_id = $section_value.'-'.$value.'-'.$root_group_id.'-'.$group_type;

		$retarr = $this->get_cache($cache_id);

		if (!$retarr) {

			/*
			* Lookup data needed for subtree'ing
			*/
			if (!empty($root_group_id)) {
				/*
				* Find the path_to_root given for the root_group_id argument. This will help give us the parent_ids of all the groups
				* in the subtree.
				*/
				$query = "select id, tree_level from $group_path_table where group_id = $root_group_id order by tree_level desc limit 1";
				$row = &$this->db->GetRow($query);

				$path_id = $row[0];
				$tree_level = $row[1];
				$this->debug_text("acl_get_groups(): Path ID: $path_id Starting Level: $tree_level");

				if (!empty($path_id)) {
					/*
					 * Using the path_id, grab all group parent_id's. This simple flat query replaces the more complex
					 * recursive query.
					 */
					$query = "select group_id from $group_path_table where id = $path_id AND tree_level <= $tree_level";
					$parent_ids_sql = @implode($this->db->GetCol($query),',');
				}
				//else {
					/*
					 * Subtree is too deep, or invalid, so don't return any parent IDs
					 * This is irrelevant.
					 */
					//$parent_ids_sql = array(-1);

				//}
			}

			/*
			 * Make sure we get the groups and their parents
			 */
			$query = "select a.id, a.parent_id from $group_table a, $group_map_table b where a.id=b.group_id AND ( b.section_value = '$section_value' AND b.value = '$value' )";

			/*
			 * If root_group_id is specified, we have to narrow this query down
			 * to just groups deeper in the tree then what is specified.
			 * This essentially creates a virtual "subtree" and ignores all outside groups.
			 * Useful for sites like sourceforge where you may seperate groups by "project".
			 */
			if (isset($parent_ids_sql)) {
				//$query .= " AND (a.id = $root_group_id OR a.parent_id = $root_group_id)";
				$query .= " AND ( a.parent_id in ($parent_ids_sql) )";
			}
			//$rs = &$this->db->CacheExecute($query);
			$rs = &$this->db->Execute($query);
			$rows = &$rs->GetRows();

			/*
			 * Seperate the group_ids from the parent_ids so we can work with them easy later on.
			 */
			foreach ($rows as $row) {
				$this->debug_text("Group ID: $row[0] Parent ID: $row[1]");
				$retarr['group_ids'][] = &$row[0];
				$retarr['parent_ids'][] = &$row[1];
			}

			//Cache data.
			$this->put_cache($retarr, $cache_id);

		}
		//$profiler->stopTimer( "acl_get_groups()");

		return $retarr;
	}

	/*======================================================================*\
		Function:   acl_get_group_path()
		Purpose:	Grabs the path to root for given groups. This is important for group inheritance.
	\*======================================================================*/
	function acl_get_group_path(&$groups_array, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$group_path_table = 'axo_groups_path';
				break;
			default:
				$group_path_table = 'aro_groups_path';
				break;
		}

		//$profiler->startTimer( "acl_get_group_path()");

		if ($groups_array) {

			//Generate unique cache ID.
			//crc32() is faster then md5() but is there a better way perhaps?
			$cache_id = 'acl_get_group_path_'.crc32(serialize($groups_array)).'-'.$group_type;

			$path_ids = $this->get_cache($cache_id);

			if (!$path_ids) {
				$groups_array = array_unique($groups_array);

				/*
				 * This is a topic for debate. How to figure out proper group inheritance.
				 * I opted for a method that requires the least amount of work for doing inheritance lookups.
				 * Most of the work is done when inserting/updating a group.
				 * This makes it so we don't need any recursive functions when speed is required.
				 */
				$query = "select id from $group_path_table where group_id in (". implode(",",$groups_array) .") and tree_level=0";
				$path_ids = &$this->db->GetCol($query);

				//Cache data.
				$this->put_cache($path_ids, $cache_id);

				//$profiler->stopTimer( "acl_get_group_path()");
			}

			return $path_ids;
		}

		return false;
	}

	/*======================================================================*\
		Function:   get_cache()
		Purpose:	Uses PEAR's Cache_Lite package to grab cached arrays, objects, variables etc...
						using unserialize() so it can handle more then just text string.
	\*======================================================================*/
	function get_cache($cache_id) {

		$this->debug_text("get_cache(): on ID: $cache_id");

		if ( is_string($this->Cache_Lite->get($cache_id) ) ) {
			return unserialize($this->Cache_Lite->get($cache_id) );
		}

		return false;
	}

	/*======================================================================*\
		Function:   put_cache()
		Purpose:	Uses PEAR's Cache_Lite package to write cached arrays, objects, variables etc...
						using serialize() so it can handle more then just text string.
	\*======================================================================*/
	function put_cache($data, $cache_id) {

		$this->debug_text("put_cache(): Cache MISS on ID: $cache_id");

		return $this->Cache_Lite->save(serialize($data), $cache_id);
	}
}
?>