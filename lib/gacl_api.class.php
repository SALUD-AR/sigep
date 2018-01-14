<?php

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
 *
 *
 *  == If you find a feature may be missing from this API, please email me: ipso@snappymail.ca and I will be happy to add it. ==
 *
 *
 * Example:
 *	$gacl_api = new gacl_api;
 *
 *	$section_id = $gacl_api->get_aco_section_id('System');
 *	$aro_id= $gacl_api->add_aro($section_id, 'John Doe', 10);
 *
 * For more examples, see the Administration interface, as it makes use of nearly every API Call.
 *
 */

class gacl_api extends gacl {

	/*
	 * Administration interface settings
	 */
	var $_items_per_page = 100;
	var $_max_select_box_items = 100;
	var $_max_search_return_items = 100;

	/*
	 *
	 * Misc helper functions.
	 *
	 */

	/*======================================================================*\
		Function:   showarray()
		Purpose:    Dump all contents of an array in HTML (kinda).
	\*======================================================================*/
	function showarray($array) {
		echo "<br><pre>\n";
		var_dump($array);
		echo "</pre><br>\n";
	}

	/*======================================================================*\
		Function:   $gacl_api->return_page()
		Purpose:	Sends the user back to a passed URL, unless debug is enabled, then we don't redirect.
						If no URL is passed, try the REFERER
	\*======================================================================*/
	function return_page($url="") {
		global $_SERVER;

		if (empty($url) AND !empty($_SERVER[HTTP_REFERER])) {
			$this->debug_text("return_page(): URL not set, using referer!");
			$url = $_SERVER[HTTP_REFERER];
		}
		if (!$this->_debug OR $this->_debug==0) {
			header("Location: $url\n\n");
		} else {
			$this->debug_text("return_page(): URL: $url -- Referer: $_SERVER[HTTP_REFERRER]");
		}
	}

	/*======================================================================*\
		Function:   get_pading_data()
		Purpose:	Creates a basic array for Smarty to deal with paging large recordsets.
						Pass it the ADODB recordset.
	\*======================================================================*/
	function get_paging_data($rs) {

		return array( 'prevpage' => $rs->absolutepage() - 1,
							'currentpage' => $rs->absolutepage(),
							'nextpage' => $rs->absolutepage() + 1,
							'atfirstpage' => $rs->atfirstpage(),
							'atlastpage' => $rs->atlastpage(),
							'lastpageno' => $rs->lastpageno()
					 );
	}

	/*======================================================================*\
		Function:	count_all()
		Purpose:	Recursively counts elements in an array.
	\*======================================================================*/
	function count_all($arg) {
		unset($count);
		// skip if argument is empty
		if ($arg) {
			// not an array, return 1 (base case)
			if (!is_array($arg)) {
				return 1;
			}
			// else call recursively for all elements $arg
			foreach($arg as $key => $val) {
				$count += $this->count_all($val);
			}
			$this->debug_text("count_all(): Count: $count");
			return $count;
		}

		return false;
	}

	/*
	 *
	 * ACL
	 *
	 */

	/*======================================================================*\
		Function:	consolidated_edit_acl()
		Purpose:	Add's an ACL but checks to see if it can consolidate it with another one first.
						This ONLY works with ACO's and ARO's. Groups, and AXO are excluded.
						As well this function is designed for handling ACLs with return values,
						and consolidating on the return_value, in hopes of keeping the ACL count to a minimum.

						A return value of false must _always_ be handled outside this function.
						As this function will remove AROs from ACLs and return false, in most cases
						you will need to a create a completely new ACL on a false return.
	\*======================================================================*/
	function consolidated_edit_acl($aco_section_value, $aco_value, $aro_section_value, $aro_value, $return_value) {

		$this->debug_text("consolidated_edit_acl(): ACO Section Value: $aco_section_value ACO Value: $aco_value ARO Section Value: $aro_section_value ARO Value: $aro_value Return Value: $return_value");

		if (empty($aco_section_value) ) {
			$this->debug_text("consolidated_edit_acl(): ACO Section Value ($aco_section_value) is empty, this is required!");
			return false;
		}

		if (empty($aco_value) ) {
			$this->debug_text("consolidated_edit_acl(): ACO Value ($aco_value) is empty, this is required!");
			return false;
		}

		if (empty($aro_section_value) ) {
			$this->debug_text("consolidated_edit_acl(): ARO Section Value ($aro_section_value) is empty, this is required!");
			return false;
		}

		if (empty($aro_value) ) {
			$this->debug_text("consolidated_edit_acl(): ARO Value ($aro_value) is empty, this is required!");
			return false;
		}

		if (empty($return_value) ) {
			$this->debug_text("consolidated_edit_acl(): Return Value ($return_value) is empty, this is required!");
			return false;
		}

		//See if a current ACL exists with the current objects, excluding return value
		$current_acl_ids = $this->search_acl($aco_section_value, $aco_value, $aro_section_value, $aro_value, FALSE, FALSE, FALSE, FALSE, FALSE);
		//showarray($current_acl_ids);

		if (is_array($current_acl_ids)) {
			$this->debug_text("add_consolidated_acl(): Found current ACL_IDs, counting ACOs");

			foreach ($current_acl_ids as $current_acl_id) {
				//Check to make sure these ACLs only have a single ACO mapped to them.
				$current_acl_array = &$this->get_acl($current_acl_id);

				//showarray($current_acl_array);
				$this->debug_text("add_consolidated_acl(): Current Count: ".$this->count_all($current_acl_array['aco'])."");

				if ( $this->count_all($current_acl_array['aco']) == 1) {
					$this->debug_text("add_consolidated_acl(): ACL ID: $current_acl_id has 1 ACO.");

					//Test to see if the return values match, if they do, no need removing or appending ARO. Just return true.
					if ($current_acl_array['return_value'] == $return_value) {
						$this->debug_text("add_consolidated_acl(): ACL ID: $current_acl_id has 1 ACO, and the same return value. No need to modify.");
						return true;
					}

					$acl_ids[] = $current_acl_id;
				}

			}
		}

		//showarray($acl_ids);
		$acl_ids_count = count($acl_ids);

		//If acl_id's turns up more then one ACL, lets remove the ARO from all of them in hopes to
		//eliminate any conflicts.
		if (is_array($acl_ids) AND $acl_ids_count > 0) {
			$this->debug_text("add_consolidated_acl(): Removing specified ARO from existing ACL.");

			foreach ($acl_ids as $acl_id) {
				//Remove ARO from current ACLs, so we don't create conflicting ACLs later on.
				if (!$this->shift_acl($acl_id, array($aro_section_value => array($aro_value)) ) ) {
					$this->debug_text("add_consolidated_acl(): Error removing specified ARO from ACL ID: $acl_id");
					return false;
				}
			}
		} else {
			$this->debug_text("add_consolidated_acl(): Didn't find any current ACLs with a single ACO. ");
		}
		unset($acl_ids);
		unset($acl_ids_count);

		//At this point there should be no conflicting ACLs, searching for an existing ACL with the new values.
		$new_acl_ids = $this->search_acl($aco_section_value, $aco_value, FALSE, FALSE, NULL, NULL, NULL, NULL, $return_value);
		$new_acl_count = count($new_acl_ids);
		//showarray($new_acl_ids);

		if (is_array($new_acl_ids)) {
			$this->debug_text("add_consolidated_acl(): Found new ACL_IDs, counting ACOs");

			foreach ($new_acl_ids as $new_acl_id) {
				//Check to make sure these ACLs only have a single ACO mapped to them.
				$new_acl_array = &$this->get_acl($new_acl_id);
				//showarray($new_acl_array);
				$this->debug_text("add_consolidated_acl(): New Count: ".$this->count_all($new_acl_array['aco'])."");
				if ( $this->count_all($new_acl_array['aco']) == 1) {

					$this->debug_text("add_consolidated_acl(): ACL ID: $new_acl_id has 1 ACO, append should be able to take place.");
					$acl_ids[] = $new_acl_id;
				}

			}
		}

		//showarray($acl_ids);
		$acl_ids_count = count($acl_ids);

		if (is_array($acl_ids) AND $acl_ids_count == 1) {
			$this->debug_text("add_consolidated_acl(): Appending specified ARO to existing ACL.");

			$acl_id=$acl_ids[0];

			if (!$this->append_acl($acl_id, array($aro_section_value => array($aro_value)) ) ) {
				$this->debug_text("add_consolidated_acl(): Error appending specified ARO to ACL ID: $acl_id");
				return false;
			}

			$this->debug_text("add_consolidated_acl(): Hot damn, ACL consolidated!");
			return true;
		} elseif($acl_ids_count > 1) {
			$this->debug_text("add_consolidated_acl(): Found more then one ACL with a single ACO. Possible conflicting ACLs.");
			return false;
		} elseif ($acl_ids_count == 0) {
			$this->debug_text("add_consolidated_acl(): No existing ACLs found, create a new one.");

			if (!$this->add_acl(	array( $aco_section_value => array($aco_value) ),
									array( $aro_section_value => array($aro_value) ),
									NULL,
									NULL,
									NULL,
									TRUE,
									TRUE,
									$return_value,
									NULL)
								) {
				$this->debug_text("add_consolidated_acl(): Error adding new ACL for ACO Section: $aco_section_value ACO Value: $aco_value Return Value: $return_value");
				return false;
			}

			$this->debug_text("add_consolidated_acl(): ADD_ACL() successfull, returning True.");
			return true;
		}

		$this->debug_text("add_consolidated_acl(): Returning false.");
		return false;
	}

	/*======================================================================*\
		Function:	search_acl()
		Purpose:	Searches for ACL's with specified objects mapped to them.
					NULL values are included in the search, if you want to ignore
					for instance aro_groups use FALSE instead of NULL.
	\*======================================================================*/
	function search_acl($aco_section_value=NULL, $aco_value=NULL, $aro_section_value=NULL, $aro_value=NULL, $aro_group_name=NULL, $axo_section_value=NULL, $axo_value=NULL, $axo_group_name=NULL, $return_value=NULL) {
		$this->debug_text("search_acl(): aco_section_value: $aco_section_value aco_value: $aco_value, aro_section_value: $aro_section_value, aro_value: $aro_value, aro_group_name: $aro_group_name, axo_section_value: $axo_section_value, axo_value: $axo_value, axo_group_name: $axo_group_name, return_value: $return_value");

		$query = "select 	a.id
							from acl a";
		if ($aco_section_value !== FALSE AND $aco_value !== FALSE) {
			$query .= " LEFT JOIN aco_map b ON a.id=b.acl_id";
		}
		if ($aro_section_value !== FALSE AND $aro_value !== FALSE) {
			$query .= " LEFT JOIN aro_map c ON a.id=c.acl_id";
		}
		if ($aro_group_name !== FALSE) {
			$query .= " LEFT JOIN aro_groups_map d ON a.id=d.acl_id
							LEFT JOIN aro_groups dd ON d.group_id=dd.id";
		}
		if ($axo_section_value !== FALSE AND $axo_value !== FALSE) {
			$query .= " LEFT JOIN axo_map e ON a.id=e.acl_id";
		}
		if ($axo_group_name !== FALSE) {
			$query .= " LEFT JOIN axo_groups_map f ON a.id=f.acl_id
							LEFT JOIN axo_groups ff ON f.group_id=ff.id";
		}

		$query .= " WHERE ";

		//Where clauses
		if ($aco_section_value !== FALSE AND $aco_value !== FALSE) {
			if ($aco_section_value == NULL AND $aco_value == NULL) {
				$where_query[] = " ( b.section_value is NULL AND b.value is NULL )";
			} else {
				$where_query[] = " ( b.section_value='$aco_section_value' AND b.value='$aco_value' )";
			}
		}
		if ($aro_section_value !== FALSE AND $aro_value !== FALSE) {
			if ($aro_section_value == NULL AND $aro_value == NULL) {
				$where_query[] = " ( c.section_value is NULL AND c.value is NULL )";
			} else {
				$where_query[] = " ( c.section_value='$aro_section_value' AND c.value='$aro_value' )";
			}
		}
		if ($aro_group_name !== FALSE) {
			if ($aro_group_name == NULL) {
				$where_query[] = " ( dd.name is NULL ) ";
			} else {
				$where_query[] = " ( dd.name='$aro_group_name' ) ";
			}
		}
		if ($axo_section_value !== FALSE AND $axo_value !== FALSE) {
			if ($axo_section_value == NULL AND $axo_value == NULL) {
				$where_query[] = " ( e.section_value is NULL AND e.value is NULL )";
			} else {
				$where_query[] = " ( e.section_value='$axo_section_value' AND e.value='$axo_value' )";
			}
		}
		if ($axo_group_name !== FALSE) {
			if ($axo_group_name == NULL) {
				$where_query[] = " ( ff.name is NULL ) ";
			} else {
				$where_query[] = " ( ff.name='$axo_group_name' ) ";
			}
		}
		if ($return_value != FALSE) {
			if ($return_value == NULL) {
				$where_query[] = " ( a.return_value is NULL ) ";
			} else {
				$where_query[] = " ( a.return_value='$return_value' ) ";
			}
		}

		$query .= implode($where_query, " AND ");

		return $this->db->GetCol($query);
	}

	/*======================================================================*\
		Function:	append_acl()
		Purpose:	Appends objects on to a specific ACL.
	\*======================================================================*/
	function append_acl($acl_id, $aro_array=NULL, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $aco_array=NULL) {
		$this->debug_text("append_acl(): ACL_ID: $acl_id");

		if (empty($acl_id)) {
			$this->debug_text("append_acl(): No ACL_ID specified! ACL_ID: $acl_id");
			return false;
		}

		//Grab ACL data.
		$acl_array = &$this->get_acl($acl_id);

		//Append each object type seperately.
		if (is_array($aro_array) AND count($aro_array) > 0) {
			$this->debug_text("append_acl(): Appending ARO's");

			while (list($aro_section_value,$aro_value_array) = @each($aro_array)) {
				foreach ($aro_value_array as $aro_value) {

					if (!in_array($aro_value, $acl_array['aro'][$aro_section_value])) {
						$this->debug_text("append_acl(): ARO Section Value: $aro_section_value ARO VALUE: $aro_value");
						$acl_array['aro'][$aro_section_value][] = $aro_value;
						$update=1;
					} else {
						$this->debug_text("append_acl(): Duplicate ARO, ignoring... ");
					}

				}
			}
		}

		if (is_array($aro_group_ids) AND count($aro_group_ids) > 0) {
			$this->debug_text("append_acl(): Appending ARO_GROUP_ID's");

			while (list(,$aro_group_id) = @each($aro_group_ids)) {
				if (!is_array($acl_array['aro_groups']) OR !in_array($aro_group_id, $acl_array['aro_groups'])) {
					$this->debug_text("append_acl(): ARO Group ID: $aro_group_id");
					$acl_array['aro_groups'][] = $aro_group_id;
					$update=1;
				} else {
					$this->debug_text("append_acl(): Duplicate ARO_Group_ID, ignoring... ");
				}
			}
		}

		if (is_array($axo_array) AND count($axo_array) > 0) {
			$this->debug_text("append_acl(): Appending AXO's");

			while (list($axo_section_value,$axo_value_array) = @each($axo_array)) {
				foreach ($axo_value_array as $axo_value) {
					if (!in_array($axo_value, $acl_array['axo'][$axo_section_value])) {
						$this->debug_text("append_acl(): AXO Section Value: $axo_section_value AXO VALUE: $axo_value");
						$acl_array['axo'][$axo_section_value][] = $axo_value;
						$update=1;
					} else {
						$this->debug_text("append_acl(): Duplicate AXO, ignoring... ");
					}

				}
			}
		}

		if (is_array($axo_group_ids) AND count($axo_group_ids) > 0) {
			$this->debug_text("append_acl(): Appending AXO_GROUP_ID's");
			while (list(,$axo_group_id) = @each($axo_group_ids)) {
				if (!is_array($acl_array['axo_groups']) OR !in_array($axo_group_id, $acl_array['axo_groups'])) {
					$this->debug_text("append_acl(): AXO Group ID: $axo_group_id");
					$acl_array['axo_groups'][] = $axo_group_id;
					$update=1;
				} else {
					$this->debug_text("append_acl(): Duplicate ARO_Group_ID, ignoring... ");
				}
			}
		}

		if (is_array($aco_array) AND count($aco_array) > 0) {
			$this->debug_text("append_acl(): Appending ACO's");

			while (list($aco_section_value,$aco_value_array) = @each($aco_array)) {
				foreach ($aco_value_array as $aco_value) {
					if (!in_array($aco_value, $acl_array['aco'][$aco_section_value])) {
						$this->debug_text("append_acl(): ACO Section Value: $aco_section_value ACO VALUE: $aco_value");
						$acl_array['aco'][$aco_section_value][] = $aco_value;
						$update=1;
					} else {
						$this->debug_text("append_acl(): Duplicate ACO, ignoring... ");
					}
				}
			}
		}

		if ($update == 1) {
			$this->debug_text("append_acl(): Update flag set, updating ACL.");
			//function edit_acl($acl_id, $aco_array, $aro_array, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $allow=1, $enabled=1, $return_value=NULL, $note=NULL) {
			return $this->edit_acl($acl_id, $acl_array['aco'], $acl_array['aro'], $acl_array['aro_groups'], $acl_array['axo'], $acl_array['axo_groups'], $acl_array['allow'], $acl_array['enabled'], $acl_array['return_value'], $acl_array['note']);
		}


		//Return true if everything is duplicate and no ACL id updated.
		$this->debug_text("append_acl(): Update flag not set, NOT updating ACL.");
		return true;
	}

	/*======================================================================*\
		Function:	shift_acl()
		Purpose:	Opposite of append_acl(). Removes objects from a specific ACL. (named after PHP's array_shift())
	\*======================================================================*/
	function shift_acl($acl_id, $aro_array=NULL, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $aco_array=NULL) {
		$this->debug_text("shift_acl(): ACL_ID: $acl_id");

		if (empty($acl_id)) {
			$this->debug_text("shift_acl(): No ACL_ID specified! ACL_ID: $acl_id");
			return false;
		}

		//Grab ACL data.
		$acl_array = &$this->get_acl($acl_id);

		//showarray($acl_array);
		//Remove each object type seperately.
		if (is_array($aro_array) AND count($aro_array) > 0) {
			$this->debug_text("shift_acl(): Removing ARO's");

			while (list($aro_section_value,$aro_value_array) = @each($aro_array)) {
				foreach ($aro_value_array as $aro_value) {
					$this->debug_text("shift_acl(): ARO Section Value: $aro_section_value ARO VALUE: $aro_value");
					$aro_key = array_search($aro_value, $acl_array['aro'][$aro_section_value]);

					if ($aro_key !== FALSE) {
						$this->debug_text("shift_acl(): Removing ARO. ($aro_key)");
						unset($acl_array['aro'][$aro_section_value][$aro_key]);
						$update=1;
					} else {
						$this->debug_text("shift_acl(): ARO doesn't exist, can't remove it.");
					}

				}
			}
		}

		if (is_array($aro_group_ids) AND count($aro_group_ids) > 0) {
			$this->debug_text("shift_acl(): Removing ARO_GROUP_ID's");

			while (list(,$aro_group_id) = @each($aro_group_ids)) {
				$this->debug_text("shift_acl(): ARO Group ID: $aro_group_id");
				$aro_group_key = array_search($aro_group_id, $acl_array['aro_groups']);

				if ($aro_group_key !== FALSE) {
					$this->debug_text("shift_acl(): Removing ARO Group. ($aro_group_key)");
					unset($acl_array['aro_groups'][$aro_group_key]);
					$update=1;
				} else {
					$this->debug_text("shift_acl(): ARO Group doesn't exist, can't remove it.");
				}
			}
		}

		if (is_array($axo_array) AND count($axo_array) > 0) {
			$this->debug_text("shift_acl(): Removing AXO's");

			while (list($axo_section_value,$axo_value_array) = @each($axo_array)) {
				foreach ($axo_value_array as $axo_value) {
					$this->debug_text("shift_acl(): AXO Section Value: $axo_section_value AXO VALUE: $axo_value");
					$axo_key = array_search($axo_value, $acl_array['axo'][$axo_section_value]);

					if ($axo_key !== FALSE) {
						$this->debug_text("shift_acl(): Removing AXO. ($axo_key)");
						unset($acl_array['axo'][$axo_section_value][$axo_key]);
						$update=1;
					} else {
						$this->debug_text("shift_acl(): AXO doesn't exist, can't remove it.");
					}
				}
			}
		}

		if (is_array($axo_group_ids) AND count($axo_group_ids) > 0) {
			$this->debug_text("shift_acl(): Removing AXO_GROUP_ID's");

			while (list(,$axo_group_id) = @each($axo_group_ids)) {
				$this->debug_text("shift_acl(): AXO Group ID: $axo_group_id");
				$axo_group_key = array_search($axo_group_id, $acl_array['axo_groups']);

				if ($axo_group_key !== FALSE) {
					$this->debug_text("shift_acl(): Removing AXO Group. ($axo_group_key)");
					unset($acl_array['axo_groups'][$axo_group_key]);
					$update=1;
				} else {
					$this->debug_text("shift_acl(): AXO Group doesn't exist, can't remove it.");
				}
			}
		}

		if (is_array($aco_array) AND count($aco_array) > 0) {
			$this->debug_text("shift_acl(): Removing ACO's");

			while (list($aco_section_value,$aco_value_array) = @each($aco_array)) {
				foreach ($aco_value_array as $aco_value) {
					$this->debug_text("shift_acl(): ACO Section Value: $aco_section_value ACO VALUE: $aco_value");
					$aco_key = array_search($aco_value, $acl_array['aco'][$aco_section_value]);

					if ($aco_key !== FALSE) {
						$this->debug_text("shift_acl(): Removing ACO. ($aco_key)");
						unset($acl_array['aco'][$aco_section_value][$aco_key]);
						$update=1;
					} else {
						$this->debug_text("shift_acl(): ACO doesn't exist, can't remove it.");
					}
				}
			}
		}

		if ($update == 1) {
			//We know something was changed, so lets see if no ACO's or no ARO's are left assigned to this ACL, if so, delete the ACL completely.
			$this->showarray($acl_array);
			$this->debug_text("shift_acl(): ACOs: ". $this->count_all($acl_array['aco']) ." AROs: ".$this->count_all($acl_array['aro'])."");

			if ( $this->count_all($acl_array['aco']) == 0
					OR ( $this->count_all($acl_array['aro']) == 0
						AND ( $this->count_all($acl_array['axo']) == 0 OR $acl_array['axo'] == FALSE)
						AND (count($acl_array['aro_groups']) == 0 OR $acl_array['aro_groups'] == FALSE)
						AND (count($acl_array['axo_groups']) == 0 OR $acl_array['axo_groups'] == FALSE)
						) ) {
				$this->debug_text("shift_acl(): No ACOs or ( AROs AND AXOs AND ARO Groups AND AXO Groups) left assigned to this ACL (ID: $acl_id), deleting ACL.");

				return $this->del_acl($acl_id);
			}

			$this->debug_text("shift_acl(): Update flag set, updating ACL.");

			return $this->edit_acl($acl_id, $acl_array['aco'], $acl_array['aro'], $acl_array['aro_groups'], $acl_array['axo'], $acl_array['axo_groups'], $acl_array['allow'], $acl_array['enabled'], $acl_array['return_value'], $acl_array['note']);
		}

		//Return true if everything is duplicate and no ACL id updated.
		$this->debug_text("shift_acl(): Update flag not set, NOT updating ACL.");
		return true;
	}

	/*======================================================================*\
		Function:	get_acl()
		Purpose:	Grabs ACL data.
	\*======================================================================*/
	function get_acl($acl_id) {

		$this->debug_text("get_acl(): ACL_ID: $acl_id");

		if (empty($acl_id)) {
			$this->debug_text("get_acl(): No ACL_ID specified! ACL_ID: $acl_id");
			return false;
		}

		//Grab ACL information
		$query = "select id, allow, enabled, return_value, note from acl where id = ".$acl_id."";
		$acl_row = $this->db->GetRow($query);
		list($retarr['acl_id'], $retarr['allow'], $retarr['enabled'], $retarr['return_value'], $retarr['note']) = $acl_row;

		//Grab selected ACO's
		$query = "select distinct a.section_value, a.value, c.name, b.name from aco_map a, aco b, aco_sections c
							where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id";
		$rs = $this->db->Execute($query);
		$rows = $rs->GetRows();

		while (list(,$row) = @each($rows)) {
			list($section_value, $value, $section, $aco) = $row;
			$this->debug_text("Section Value: $section_value Value: $value Section: $section ACO: $aco");

			$retarr['aco'][$section_value][] = $value;

		}
		//showarray($aco);

		//Grab selected ARO's
		$query = "select distinct a.section_value, a.value, c.name, b.name from aro_map a, aro b, aro_sections c
							where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id";
		$rs = $this->db->Execute($query);
		$rows = $rs->GetRows();

		while (list(,$row) = @each($rows)) {
			list($section_value, $value, $section, $aro) = $row;
			$this->debug_text("Section Value: $section_value Value: $value Section: $section ARO: $aro");

			$retarr['aro'][$section_value][] = $value;

		}
		//showarray($options_aro);

		//Grab selected AXO's
		$query = "select distinct a.section_value, a.value, c.name, b.name from axo_map a, axo b, axo_sections c
							where ( a.section_value=b.section_value AND a.value = b.value) AND b.section_value=c.value AND a.acl_id = $acl_id";
		$rs = $this->db->Execute($query);
		$rows = $rs->GetRows();

		while (list(,$row) = @each($rows)) {
			list($section_value, $value, $section, $axo) = $row;
			$this->debug_text("Section Value: $section_value Value: $value Section: $section AXO: $axo");

			$retarr['axo'][$section_value][] = $value;

		}
		//showarray($options_aro);

		//Grab selected ARO groups.
		$query = "select distinct group_id from aro_groups_map where  acl_id = $acl_id";
		$retarr['aro_groups'] = $this->db->GetCol($query);
		//showarray($selected_groups);

		//Grab selected AXO groups.
		$query = "select distinct group_id from axo_groups_map where  acl_id = $acl_id";
		$retarr['axo_groups'] = $this->db->GetCol($query);
		//showarray($selected_groups);

		return $retarr;
	}

	/*======================================================================*\
		Function:	is_conflicting_acl()
		Purpose:	Checks for conflicts when adding a specific ACL.
	\*======================================================================*/
	function is_conflicting_acl($aco_array, $aro_array, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $ignore_acl_ids=NULL) {
		//Check for potential conflicts. Ignore groups, as groups will almost always have "conflicting" ACLs.
		//Thats part of inheritance.

		//ACO
		while (list($aco_section_value,$aco_value_array) = @each($aco_array)) {
			$this->debug_text("is_conflicting_acl(): ACO Section Value: $aco_section_value ACO VALUE: $aco_value_array");
			//showarray($aco_array);

			foreach ($aco_value_array as $aco_value) {
				$aco_object_id = &$this->get_object_id($aco_section_value, $aco_value, 'ACO');

				if (!empty($aco_object_id)) {
					//ARO
					while (list($aro_section_value,$aro_value_array) = @each($aro_array)) {
						$this->debug_text("is_conflicting_acl():  ARO Section Value: $aro_section_value ARO VALUE: $aro_value_array");

						foreach ($aro_value_array as $aro_value) {
							$aro_object_id = &$this->get_object_id($aro_section_value, $aro_value, 'ARO');

							if (!empty($aro_object_id)) {
								$this->debug_text("is_conflicting_acl():  ARO Object ID: $aro_object_id");
								$this->debug_text("is_conflicting_acl(): Search: ACO Section: $aco_section_value ACO Value: $aco_value ARO Section: $aro_section_value ARO Value: $aro_value");

								$conflict_result = &$this->search_acl($aco_section_value, $aco_value, $aro_section_value, $aro_value);
								if ($conflict_result != FALSE) {
									//showarray($conflict_result);

									$conflicting_acls = array_diff($conflict_result, $ignore_acl_ids);

									if (count($conflicting_acls) > 0) {
										$conflicting_acls_str = implode($conflicting_acls,",");
										$this->debug_text("is_conflicting_acl(): Conflict FOUND!!! ACL_IDS: ($conflicting_acls_str)");
										return true;
									}

								}

							} else {
								$this->debug_text("is_conflicting_acl():  ARO Object Section Value: $aro_section_value Value: $aro_value DOES NOT exist in the database. Skipping...");
								return true;
							}
						}
					}

				} else {
					$this->debug_text("is_conflicting_acl(): ACO Object Section Value: $aco_section_value Value: $aco_value DOES NOT exist in the database. Skipping...");
					return true;
				}
			}
		}

		$this->debug_text("is_conflicting_acl(): No conflicting ACL found.");
		return false;

	}
	/*======================================================================*\
		Function:	add_acl()
		Purpose:	Add's an ACL. ACO_IDS, ARO_IDS, GROUP_IDS must all be arrays.
	\*======================================================================*/
	function add_acl($aco_array, $aro_array, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $allow=1, $enabled=1, $return_value=NULL, $note=NULL, $acl_id=FALSE ) {

		$this->debug_text("add_acl():");

		if (count($aco_array) == 0) {
			$this->debug_text("Must select at least one Access Control Object");
			return false;
		}

		if (count($aro_array) == 0 AND count($aro_group_ids) == 0) {
			$this->debug_text("Must select at least one Access Request Object or Group");
			return false;
		}

		if (empty($allow)) {
			$allow=0;
		}

		if (empty($enabled)) {
			$enabled=0;
		}

		//Unique the group arrays. Later one we unique ACO/ARO/AXO arrays.
		if (is_array($aro_group_ids)) {
			$aro_group_ids = array_unique($aro_group_ids);
		}
		if (is_array($axo_group_ids)) {
			$axo_group_ids = array_unique($axo_group_ids);
		}

		//Check for conflicting ACLs.
		if ($this->is_conflicting_acl($aco_array,$aro_array, NULL, NULL, NULL, array($acl_id)) ) {
			$this->debug_text("add_acl(): Detected possible ACL conflict, not adding ACL!");
			return false;
		}

		//Edit ACL if acl_id is set. This is simply if we're being called by edit_acl().
		if (empty($acl_id)) {
			//Create ACL row first, so we have the acl_id
			$acl_id = $this->db->GenID('acl_seq',10);

			$this->db->BeginTrans();
			$query = "insert into acl (id,allow,enabled,return_value, note, updated_date) VALUES($acl_id, $allow, $enabled, ".$this->db->quote($return_value).", ".$this->db->quote($note).", ".time().")";
			$result = $this->db->Execute($query);
		} else {
			$this->db->BeginTrans();

			//Update ACL row, and remove all mappings so they can be re-inserted.
			$query = "update acl set allow=$allow,enabled=$enabled,return_value=".$this->db->quote($return_value).", note=".$this->db->quote($note).",updated_date=".time()." where id=$acl_id";
			$result = $this->db->Execute($query);

			if ($result) {
				$this->debug_text("Update completed without error, delete mappings...");
				//Delete all mappings so they can be re-inserted.
				$query = "delete from aco_map where acl_id=$acl_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}

				$query = "delete from aro_map where acl_id=$acl_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}

				$query = "delete from axo_map where acl_id=$acl_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}

				$query = "delete from aro_groups_map where acl_id=$acl_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}

				$query = "delete from axo_groups_map where acl_id=$acl_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}
			}
		}

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		if ($result) {
			$this->debug_text("Insert or Update completed without error, insert new mappings.");
			//Insert ACO mappings
			while (list($aco_section_value,$aco_value_array) = @each($aco_array)) {
				$this->debug_text("Insert: ACO Section Value: $aco_section_value ACO VALUE: $aco_value_array");
				//$this->showarray($aco_value_array);
				$aco_value_array = array_unique($aco_value_array);

				foreach ($aco_value_array as $aco_value) {
					$aco_object_id = &$this->get_object_id($aco_section_value, $aco_value, 'ACO');

					if (!empty($aco_object_id)) {

						$query = "insert into aco_map (acl_id,section_value,value) VALUES($acl_id, '$aco_section_value', '$aco_value')";
						$rs = $this->db->Execute($query);

						if ( is_string( $this->db->ErrorNo() ) ) {
							$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
							$this->db->RollBackTrans();
							return false;
						}
					} else {
						$this->debug_text("add_acl(): ACO Object Section Value: $aco_section_value Value: $aco_value DOES NOT exist in the database. Skipping...");
						$this->db->RollBackTrans();
						return false;
					}
				}
			}

			//Insert ARO mappings
			while (list($aro_section_value,$aro_value_array) = @each($aro_array)) {
				$this->debug_text("Insert: ARO Section Value: $aro_section_value ARO VALUE: $aro_value_array");

				$aro_value_array = array_unique($aro_value_array);
				foreach ($aro_value_array as $aro_value) {
					$aro_object_id = &$this->get_object_id($aro_section_value, $aro_value, 'ARO');

					if (!empty($aro_object_id)) {
						$this->debug_text("add_acl(): ARO Object ID: $aro_object_id");

						$query = "insert into aro_map (acl_id,section_value, value) VALUES($acl_id, '$aro_section_value', '$aro_value')";
						$rs = $this->db->Execute($query);

						if ( is_string( $this->db->ErrorNo() ) ) {
							$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
							$this->db->RollBackTrans();
							return false;
						}
					} else {
						$this->debug_text("add_acl(): ARO Object Section Value: $aro_section_value Value: $aro_value DOES NOT exist in the database. Skipping...");
						$this->db->RollBackTrans();
						return false;
					}
				}
			}

			//Insert AXO mappings
			while (list($axo_section_value,$axo_value_array) = @each($axo_array)) {
				$this->debug_text("Insert: AXO Section Value: $axo_section_value AXO VALUE: $axo_value_array");

				$axo_value_array = array_unique($axo_value_array);
				foreach ($axo_value_array as $axo_value) {
					$axo_object_id = &$this->get_object_id($axo_section_value, $axo_value, 'AXO');

					if (!empty($axo_object_id)) {
						$query = "insert into axo_map (acl_id,section_value, value) VALUES($acl_id, '$axo_section_value', '$axo_value')";
						$rs = $this->db->Execute($query);

						if ( is_string( $this->db->ErrorNo() ) ) {
							$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
							$this->db->RollBackTrans();
							return false;
						}
					} else {
						$this->debug_text("add_acl(): AXO Object Section Value: $axo_section_value Value: $axo_value DOES NOT exist in the database. Skipping...");
						$this->db->RollBackTrans();
						return false;
					}
				}
			}

			//Insert ARO GROUP mappings
			while (list(,$aro_group_id) = @each($aro_group_ids)) {
				$this->debug_text("Insert: ARO GROUP ID: $aro_group_id");

				$aro_group_data = &$this->get_group_data($aro_group_id, 'ARO');

				if (!empty($aro_group_data)) {

					$query = "insert into aro_groups_map (acl_id,group_id) VALUES($acl_id, $aro_group_id)";
					$rs = $this->db->Execute($query);

					if ( is_string( $this->db->ErrorNo() ) ) {
						$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
						$this->db->RollBackTrans();
						return false;
					}
				} else {
					$this->debug_text("add_acl(): ARO Group: $aro_group_id DOES NOT exist in the database. Skipping...");
					$this->db->RollBackTrans();
					return false;
				}
			}

			//Insert AXO GROUP mappings
			while (list(,$axo_group_id) = @each($axo_group_ids)) {
				$this->debug_text("Insert: AXO GROUP ID: $axo_group_id");

				$axo_group_data = &$this->get_group_data($axo_group_id, 'AXO');

				if (!empty($axo_group_data)) {

					$query = "insert into axo_groups_map (acl_id,group_id) VALUES($acl_id, $axo_group_id)";
					$rs = $this->db->Execute($query);

					if ( is_string( $this->db->ErrorNo() ) ) {
						$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
						$this->db->RollBackTrans();
						return false;
					}
				} else {
					$this->debug_text("add_acl(): AXO Group: $axo_group_id DOES NOT exist in the database. Skipping...");
					$this->db->RollBackTrans();
					return false;
				}
			}
		}

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		} else {
			$this->db->CommitTrans();

			if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
				//Expire all cache.
				$this->Cache_Lite->clean('default');
			}

			//Return only the ID in the first row.
			return $acl_id;
		}
	}

	/*======================================================================*\
		Function:	edit_acl()
		Purpose:	Edit's an ACL, ACO_IDS, ARO_IDS, GROUP_IDS must all be arrays.
	\*======================================================================*/
	function edit_acl($acl_id, $aco_array, $aro_array, $aro_group_ids=NULL, $axo_array=NULL, $axo_group_ids=NULL, $allow=1, $enabled=1, $return_value=NULL, $note=NULL) {

		$this->debug_text("edit_acl():");

		if (empty($acl_id) ) {
			$this->debug_text("edit_acl(): Must specify a single ACL_ID to edit");
			return false;
		}
		if (count($aco_array) == 0) {
			$this->debug_text("edit_acl(): Must select at least one Access Control Object");
			return false;
		}

		if (count($aro_array) == 0 AND count($aro_group_ids) == 0) {
			$this->debug_text("edit_acl(): Must select at least one Access Request Object or Group");
			return false;
		}

		if (empty($allow)) {
			$allow=0;
		}

		if (empty($enabled)) {
			$enabled=0;
		}

		//if ($this->add_acl($aco_array, $aro_array, $group_ids, $allow, $enabled, $acl_id)) {
		if ($this->add_acl($aco_array, $aro_array, $aro_group_ids, $axo_array, $axo_group_ids, $allow, $enabled, $return_value, $note, $acl_id)) {
			return true;
		} else {
			$this->debug_text("edit_acl(): error in add_acl()");
			return false;
		}
	}

	/*======================================================================*\
		Function:	del_acl()
		Purpose:	Deletes a given ACL
	\*======================================================================*/
	function del_acl($acl_id) {

		$this->debug_text("del_acl(): ID: $acl_id");

		if (empty($acl_id) ) {
			$this->debug_text("del_acl(): ACL_ID ($acl_id) is empty, this is required");
			return false;
		}

		$this->db->BeginTrans();

		$query = "delete from acl where id = $acl_id";
		$this->debug_text("delete query: $query");
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from aco_map where acl_id= $acl_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from aro_map where acl_id = $acl_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from axo_map where acl_id = $acl_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from aro_groups_map where acl_id = $acl_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from axo_groups_map where acl_id = $acl_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_acl(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		} else {
			$this->debug_text("del_acl(): deleted ACL ID: $acl_id");
			$this->db->CommitTrans();

			if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
				//Expire all cache.
				$this->Cache_Lite->clean('default');
			}

			return true;
		}

	}


	/*
	 *
	 * Groups
	 *
	 */

	/*======================================================================*\
		Function:	sort_groups()
		Purpose:	Grabs all the groups from the database doing preliminary grouping by parent
	\*======================================================================*/
	function sort_groups($group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		//Grab all groups from the database.
		$query = "select
									id,
									parent_id,
									name
						from    $table
						order by parent_id, name asc";
		$rs = $this->db->Execute($query);
		$rows = $rs->GetRows();

		/*
		 * Save groups in an array sorted by parent. Should be make it easier for later on.
		 */
		if ($rows != FALSE) {
			foreach ($rows as $row) {
				$id = &$row[0];
				$parent_id = &$row[1];
				$name = &$row[2];

				$sorted_groups[$parent_id][$id] = $name;
			}
		}

		return $sorted_groups;
	}

	/*======================================================================*\
		Function:	format_groups()
		Purpose:	Takes the array returned by sort_groups() and formats for human consumption.
	\*======================================================================*/
	function format_groups($sorted_groups, $type='TEXT', $root_id=0, $level=0, $formatted_groups=NULL) {
		/*
		 * Recursing with a global array, not the most effecient or safe way to do it, but it will work for now.
		 */
		//$this->showarray($formatted_groups);

		//while (list($id,$name) = @each($sorted_groups[$root_id])) {
		if (isset($sorted_groups[$root_id])) {
			foreach ($sorted_groups[$root_id] as $id => $name) {
				switch ($type) {
					case 'TEXT':
						/*
						 * Formatting optimized for TEXT (combo box) output.
						 */
						//$spacing = str_repeat("|&nbsp;&nbsp;", $level * 1);
						$spacing = str_repeat("|  &nbsp;", $level * 1);
						$text = $spacing.$name;
						break;
					case 'HTML':
						/*
						 * Formatting optimized for HTML (tables) output.
						 */
						$width= $level * 20;
						$spacing = "<img src=\"s.gif\" width=\"$width\">";
						$text = $spacing." ".$name;
						break;
					case "ARRAY":
						break;
				}

				$formatted_groups[$id] = $text;
				/*
				 * Recurse if we can.
				 */

				//if (isset($sorted_groups[$id]) AND count($sorted_groups[$id]) > 0) {
				if (isset($sorted_groups[$id]) ) {
					//$this->debug_text("format_groups(): Recursing! Level: $level");
					$formatted_groups = $this->format_groups($sorted_groups, $type, $id, $level + 1, $formatted_groups);
				} else {
					//$this->debug_text("format_groups(): Found last branch!");
				}
			}
		}

		//$this->debug_text("format_groups(): Returning final array.");

		return $formatted_groups;
	}

	/*======================================================================*\
		Function:	get_group_id()
		Purpose:	Gets the group_id given the name.
						Will only return one group id, so if there are duplicate names, it will return false.
	\*======================================================================*/
	function get_group_id($name = null, $group_type = 'ARO') {

		$this->debug_text("get_group_id(): Name: $name");

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		$name = trim($name);

		if (empty($name) ) {
			$this->debug_text("get_group_id(): name ($name) is empty, this is required");
			return false;
		}

		$query = "select id from $table where name='$name'";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_group_id(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$row_count = $rs->RecordCount();

			if ($row_count > 1) {
				$this->debug_text("get_group_id(): Returned $row_count rows, can only return one. Please make your names unique.");
				return false;
			} elseif($row_count == 0) {
				$this->debug_text("get_group_id(): Returned $row_count rows");
				return false;
			} else {
				$rows = $rs->GetRows();

				//Return only the ID in the first row.
				return $rows[0][0];
			}
		}
	}

	/*======================================================================*\
		Function:	get_group_data()
		Purpose:	Gets the group data given the GROUP_ID.
	\*======================================================================*/
	function get_group_data($group_id, $group_type = 'ARO') {

		$this->debug_text("get_group_data(): Group_ID: $group_id Group Type: $group_type");

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		if (empty($group_id) ) {
			$this->debug_text("get_group_data(): ID ($group_id) is empty, this is required");
			return false;
		}

		$query = "select id, parent_id, name from $table where id=$group_id";
		//$rs = $this->db->Execute($query);
		$row = $this->db->GetRow($query);

		if ($row) {
			return $row;

		}

		$this->debug_text("get_object_data(): Goup does not exist.");
		return false;
/*
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_group_data(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			if ($rs->RecordCount() > 0) {
				$this->debug_text("get_group_data(): Returned Rows: ". $rs->RecordCount() ."");
				$row = $rs->GetRows();

				//Return just first row. As there should never be more then that.
				return $row[0];
			} else {
				$this->debug_text("get_object_data(): Returned $row_count rows");
				return false;
			}
		}
*/
	}

	/*======================================================================*\
		Function:	get_group_parent_id()
		Purpose:	Grabs the parent_id of a given group
	\*======================================================================*/
	function get_group_parent_id($id, $group_type='ARO') {

		$this->debug_text("get_group_parent_id(): ID: $id Group Type: $group_type");

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		if (empty($id) ) {
			$this->debug_text("get_group_parent_id(): ID ($id) is empty, this is required");
			return false;
		}

		$query = "select parent_id from $table where id=$id";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_group_parent_id(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$row_count = $rs->RecordCount();

			if ($row_count > 1) {
				$this->debug_text("get_group_parent_id(): Returned $row_count rows, can only return one. Please make your names unique.");
				return false;
			} elseif($row_count == 0) {
				$this->debug_text("get_group_parent_id(): Returned $row_count rows");
				return false;
			} else {
				$rows = $rs->GetRows();

				//Return only the ID in the first row.
				return $rows[0][0];
			}
		}
	}

	/*======================================================================*\
		Function:	map_path_to_root()
		Purpose:	Maps a unique path to root to a specific group. Each group can only have
						one path to root.
	\*======================================================================*/
	function map_group_path_to_root($group_id, $path_id, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups_path_map';
				break;
			default:
				$table = 'aro_groups_path_map';
				break;
		}

		if (empty($group_id) OR empty($path_id) ) {
			$this->debug_text("map_group_path_to_root(): group id ($group_id) OR path id ($path_id) is empty, this is required");
			return false;
		}

		$this->db->BeginTrans();

		$query = "delete from $table where group_id=$group_id";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("map_group_path_to_root(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "insert into $table (path_id, group_id) VALUES($path_id, $group_id)";
		$this->db->Execute($query);
		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("map_group_path_to_root(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$this->db->CommitTrans();
		return true;
	}

	/*======================================================================*\
		Function:	put_path_to_root()
		Purpose:	Writes the unique path to root to the database. There should really only be
						one path to root for each level "deep" the groups go. If the groups are branched
						10 levels deep, there should only be 10 unique path to roots. These of course
						overlap each other more and more the closer to the root/trunk they get.
	\*======================================================================*/
	function put_group_path_to_root($path_to_root, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups_path';
				break;
			default:
				$table = 'aro_groups_path';
				break;
		}

		if (count($path_to_root) > 0) {
			/*
			 * See if the path has already been created.
			 */

			$query = "select
										id
							from    $table
							where group_id = $path_to_root[0]
									AND tree_level = 0";
			$path_id = $this->db->GetOne($query);
			$this->debug_text("put_group_path_to_root(): Path ID: $path_id");

			if (!empty($path_id) ) {
				$this->debug_text("put_group_path_to_root(): Checking all steps in path ID: $path_id");

				//Possible path found, make sure all steps are correct.
				$query= "select group_id from $table where id = $path_id";
				$path_group_ids = $this->db->GetCol($query);

				//$this->showarray($path_group_ids);
				//$this->showarray($path_to_root);

				$path_diff = array_diff($path_to_root, $path_group_ids);
				if (count($path_diff) > 0) {
					$this->debug_text("put_group_path_to_root(): Paths differ by ".count($path_diff) .", bogus path. Insert new one.");
					unset($path_id);
				} else {
					$this->debug_text("put_group_path_to_root(): Path matches perfect, unique path FOUND, returning ID: $path_id");
					$retval = $path_id;
				}
			}

			if (empty($path_id)) {
				$this->debug_text("put_group_path_to_root(): Unique path not found, inserting...");
				$insert_id = $this->db->GenID($table.'_id_seq',10);

				$this->db->BeginTrans();
				$i=0;
				foreach ($path_to_root as $group_id) {

					$query = "insert into $table (id, group_id, tree_level) VALUES($insert_id, $group_id, $i)";
					$this->db->Execute($query);
					if ( is_string( $this->db->ErrorNo() ) ) {
						$this->debug_text("put_group_path_to_root(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
						$this->db->RollBackTrans();
						return false;
					}

					$i++;
				}
				$this->db->CommitTrans();

				$retval = $insert_id;
			}

			/*
			 * Return path to root ID.
			 */
			return $retval;
		}

		return false;
	}

	/*======================================================================*\
		Function:	clean_path_to_root()
		Purpose:	Cleans up any paths that are not being used.
	\*======================================================================*/
	function clean_path_to_root($group_type = 'ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups_path';
				$group_path_map_table = 'axo_groups_path_map';
				break;
			default:
				$table = 'aro_groups_path';
				$group_path_map_table = 'aro_groups_path_map';
				break;
		}

		$this->debug_text("clean_path_to_root(): Cleaning any orphaned path_to_root's.");

		$query = "select distinct path_id from $group_path_map_table";
		$valid_path_ids = $this->db->GetCol($query);

		//$this->showarray($valid_path_ids);
		if ($valid_path_ids == FALSE) {
			//Delete all paths, as none should be in use.
			$query = "delete from $table";
			$this->db->Execute($query);
			if ( is_string( $this->db->ErrorNo() ) ) {
				$this->debug_text("clean_path_to_root(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
				return false;
			}

		} else {
			//Format path_id's for a SQL query.
			$sql_valid_path_ids = implode($valid_path_ids, ",");
			unset($valid_path_ids);

			$query = "delete from $table where id not in ($sql_valid_path_ids)";
			$this->db->Execute($query);
			if ( is_string( $this->db->ErrorNo() ) ) {
				$this->debug_text("clean_path_to_root(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
				return false;
			}
		}

		return true;
	}

	/*======================================================================*\
		Function:	get_path_to_root()
		Purpose:	Generates the path to root for a given group.
	\*======================================================================*/
	function gen_group_path_to_root($group_id, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		$this->debug_text("gen_group_path_to_root():");
		$parent_id = $group_id;

		if (empty($parent_id)) {
			$this->debug_text("gen_group_path_to_root(): group id ($group_id) is empty, this is required");
			return false;
		}

		/*
		 * Simply repeat the SQL query until we reach the root (0). Obviously this won't scale that well, but it should do the trick
		 * up to about 100 levels deep if it needs too. This way will use less memory too.
		 * It's only run during group administration so speed is not much of a concern. Its all for a better cause. ;)
		 */
		while ($parent_id > 0) {
			$query = "select
										parent_id
							from    $table
							where id = $parent_id";
			$parent_id = $this->db->GetOne($query);

			$path[] = $parent_id;
		}

		return $path;
	}

	/*======================================================================*\
		Function:	add_group()
		Purpose:	Inserts a group, defaults to be on the "root" branch.
	\*======================================================================*/
	function add_group($name, $parent_id=0, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		$this->debug_text("add_group(): Name: $name Parent ID: $parent_id Group Type: $group_type");

		$name = trim($name);

		if (empty($name)) {
			$this->debug_text("add_group(): name ($name) OR parent id ($parent_id) is empty, this is required");
			return false;
		}

		$insert_id = $this->db->GenID('groups_id_seq',10);
		$query = "insert into $table (id, parent_id,name) VALUES($insert_id, $parent_id, '$name')";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("add_group(): Added group as ID: $insert_id");

			$this->map_group_path_to_root($insert_id, $this->put_group_path_to_root( $this->gen_group_path_to_root($insert_id, $group_type), $group_type ), $group_type );

			return $insert_id;
		}
	}

	/*======================================================================*\
		Function:	get_group_objects()
		Purpose:	Gets all objects assigned to a group.
	\*======================================================================*/
	function get_group_objects($group_id, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'groups_axo_map';
				break;
			default:
				$table = 'groups_aro_map';
				break;
		}

		$this->debug_text("get_group_objects(): Group ID: $group_id");

		if (empty($group_id)) {
			$this->debug_text("get_group_objects(): Group ID:  ($group_id) is empty, this is required");
			return false;
		}

        $query = "select section_value, value from $table where group_id = $group_id";
		$rs = $this->db->Execute($query);
		$rows = $rs->GetRows();

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_group_objects(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("get_group_objects(): Got group objects");
			return $rows;
		}
	}

	/*======================================================================*\
		Function:	add_group_object()
		Purpose:	Assigns an ARO to a group
	\*======================================================================*/
	function add_group_object($group_id, $object_section_value, $object_value, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'groups_axo_map';
				break;
			default:
				$table = 'groups_aro_map';
				break;
		}

		$this->debug_text("add_group_object(): Group ID: $group_id Section Value: $object_section_value Value: $object_value Group Type: $group_type");

		$object_section_value = trim($object_section_value);
		$object_value = trim($object_value);

		if (empty($group_id) OR empty($object_value) OR empty($object_section_value)) {
			$this->debug_text("add_group_object(): Group ID:  ($group_id) OR Value ($object_value) OR Section value ($object_section_value) is empty, this is required");
			return false;
		}

		if(!$this->get_object_id($object_section_value, $object_value, $group_type)) {
			$this->debug_text("add_group_object(): Group ID:  ($group_id) OR Value ($object_value) OR Section value ($object_section_value) is invalid. Does this object exist?");
			return false;
		}

        $query = "insert into $table (group_id,section_value, value) VALUES($group_id, '$object_section_value', '$object_value')";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_group_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("add_group_object(): Added Value: $object_value to Group ID: $group_id");

			if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
				//Expire all cache.
				$this->Cache_Lite->clean('default');
			}

			return true;
		}
	}

	/*======================================================================*\
		Function:	del_group_object()
		Purpose:	Removes an Object to group assignment
	\*======================================================================*/
	function del_group_object($group_id, $object_section_value, $object_value, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'groups_axo_map';
				break;
			default:
				$table = 'groups_aro_map';
				break;
		}

		$this->debug_text("del_group_object(): Group ID: $group_id Section value: $object_section_value Value: $object_value");

		$object_section_value = trim($object_section_value);
		$object_value = trim($object_value);

		if (empty($group_id) OR empty($object_value) OR empty($object_section_value)) {
			$this->debug_text("del_group_object(): Group ID:  ($group_id) OR Section value: $object_section_value OR Value ($object_value) is empty, this is required");
			return false;
		}

        $query = "delete from $table where group_id=$group_id AND ( section_value='$object_section_value' AND value='$object_value')";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("del_group_object(): Deleted Value: $object_value to Group ID: $group_id assignment");

			if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
				//Expire all cache.
				$this->Cache_Lite->clean('default');
			}

			return true;
		}
	}

	/*======================================================================*\
		Function:	edit_group()
		Purpose:	Edits a group
	\*======================================================================*/
	function edit_group($group_id, $name=NULL, $parent_id=0, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				break;
			default:
				$table = 'aro_groups';
				break;
		}

		$this->debug_text("edit_group(): ID: $group_id Name: $name Parent ID: $parent_id Group Type: $group_type");

		$name = trim($name);

		if (empty($group_id) ) {
			$this->debug_text("edit_group(): Group ID ($group_id) is empty, this is required");
			return false;
		}

		if ($group_id == $parent_id) {
			$this->debug_text("edit_group(): Groups can't be a parent to themselves. Incest is bad. ;)");
			return false;
		}

		//Make sure we don't re-parent to our own children.
		//Grab all children of this group_id.
		$children_ids = @array_keys( $this->format_groups($this->sort_groups($group_type), 'ARRAY', $group_id) );
		if (@in_array($parent_id, $children_ids) ) {
			$this->debug_text("edit_group(): Groups can not be re-parented to there own children, this would be incestuous!");
			return false;
		}
		unset($children_ids);

		$query = "update $table set ";
		//Don't update name if it is not specified.
		if ($name != NULL) {
			$query .= "									name = '$name', ";
		}
		$query .= "										parent_id = $parent_id
													where   id=$group_id";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("edit_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("edit_group(): Modified group ID: $group_id");

			//Get all new child IDs.
			$children_ids = @array_keys( $this->format_groups($this->sort_groups($group_type), 'ARRAY', $group_id) );
			//$this->showarray($children_ids);

			if (count($children_ids) > 0) {
				$this->debug_text("Found child_ids to re-parent.");

				foreach($children_ids as $child_id) {
					$this->debug_text("Re-Calculating child group path to root: $child_id");

					$this->map_group_path_to_root($child_id, $this->put_group_path_to_root( $this->gen_group_path_to_root($child_id, $group_type), $group_type ), $group_type );
				}
			}

			$retval = &$this->map_group_path_to_root($group_id, $this->put_group_path_to_root( $this->gen_group_path_to_root($group_id, $group_type), $group_type ), $group_type );

			//Clean orphaned paths
			$this->clean_path_to_root($group_type);

			if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
				//Expire all cache.
				$this->Cache_Lite->clean('default');
			}

			return $retval;
		}
	}

	/*======================================================================*\
		Function:	del_group()
		Purpose:	deletes a given group
	\*======================================================================*/
	function del_group($group_id, $reparent_children=TRUE, $group_type='ARO') {

		switch(strtolower($group_type)) {
			case 'axo':
				$table = 'axo_groups';
				$groups_map_table = 'axo_groups_map';
				$groups_object_map_table = 'groups_axo_map';
				$groups_path_table = 'axo_groups_path';
				$groups_path_map_table = 'axo_groups_path_map';
				break;
			default:
				$table = 'aro_groups';
				$groups_map_table = 'aro_groups_map';
				$groups_object_map_table = 'groups_aro_map';
				$groups_path_table = 'aro_groups_path';
				$groups_path_map_table = 'aro_groups_path_map';
				break;
		}

		$this->debug_text("del_group(): ID: $group_id Reparent Children: $reparent_children Group Type: $group_type");

		if (empty($group_id) ) {
			$this->debug_text("del_group(): Group ID ($group_id) is empty, this is required");
			return false;
		}

		/*
		 * Find this groups parent. Which we use to reparent children.
		 */
		$query = "select parent_id from $table where id=$group_id";
		$parent_id = $this->db->GetOne($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		}

		$this->db->BeginTrans();

		//Grab all current children and edit_group() each of them to the proper parent
		$query = "select id from $table where parent_id=$group_id";
		$children_ids = $this->db->GetCol($query);

		/*
		 * Handle children here.
		 */
		if ($reparent_children==TRUE) {
			//Reparent children if any.

			if ($children_ids != FALSE) {
				foreach ($children_ids as $child_id) {
					$this->edit_group($child_id, NULL, $parent_id, $group_type);
				}
			}
		} else {
			//Delete all children
			if ($children_ids != FALSE) {
				foreach ($children_ids as $child_id) {
					$this->del_group($child_id, FALSE, $group_type);
				}
			}
		}

		//Clean up paths.
		//Find which path this group uses.
		$query = "select path_id from $groups_path_map_table where group_id=$group_id";
		$path_id = $this->db->GetOne($query);

		if (!empty($path_id) ) {
			//Do other groups use the same path?
			$query = "select count(*) from $groups_path_map_table where path_id=$path_id";
			$groups_using_path = $this->db->GetOne($query);

			$this->debug_text("Found $groups_using_path groups using this path.");

			if ($groups_using_path == 1) {
				//Delete path
				$this->debug_text("Only one group using path, deleting path: $path_id");

				$query = "delete from $groups_path_table where id=$path_id";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}
			} else {
				$this->debug_text("More then one ($groups_using_path) group using path (ID: $path_id), not deleting it.");
			}
		}

		$query = "delete from $table where id=$group_id";
		$this->debug_text("delete query: $query");
		$this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from $groups_map_table where group_id=$group_id";
		$this->debug_text("delete query: $query");
		$this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$query = "delete from $groups_path_map_table where group_id=$group_id";
		$this->debug_text("delete query: $query");
		$this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		//Delete objects mapped to this group.
		$query = "delete from $groups_object_map_table where group_id=$group_id";
		$this->debug_text("delete query: $query");
		$this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("del_group(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			$this->db->RollBackTrans();
			return false;
		}

		$this->debug_text("del_group(): deleted group ID: $group_id");
		$this->db->CommitTrans();

		if ($this->_caching == TRUE AND $this->_force_cache_expire == TRUE) {
			//Expire all cache.
			$this->Cache_Lite->clean('default');
		}

		return true;

	}


	/*
	 *
	 * Objects (ACO/ARO/AXO)
	 *
	 */

	/*======================================================================*\
		Function:	get_object()
		Purpose:	Grabs all Objects's in the database, or specific to a section_value
	\*======================================================================*/
	function get_object($section_value = null, $return_hidden=1, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				break;
			case 'aro':
				$object_type = 'aro';
				break;
			case 'axo':
				$object_type = 'axo';
				break;
		}

		$this->debug_text("get_object(): Section Value: $section_value Object Type: $object_type");

		$query = "select id from $object_type ";
		if (!empty($section_value) ) {
			$query .= " where section_value = '$section_value'";
		}

		if ($return_hidden==0) {
			$query .= "	and hidden=0";
		}

		$rs = $this->db->GetCol($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			//Return all Object id's
			return $rs;
		}
	}

	/*======================================================================*\
		Function:	get_object_data()
		Purpose:	Gets all data pertaining to a specific Object.
	\*======================================================================*/
	function get_object_data($object_id, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				break;
			case 'aro':
				$object_type = 'aro';
				break;
			case 'axo':
				$object_type = 'axo';
				break;
		}

		$this->debug_text("get_aco_data(): Object ID: $object_id Object Type: $object_type");

		if (empty($object_id) ) {
			$this->debug_text("get_object_data(): Object ID ($object_id) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("get_object_data(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$query = "select section_value, value, order_value, name, hidden from $object_type where id = $object_id";

		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_object_data(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			if ($rs->RecordCount() > 0) {
				$rows = $rs->GetRows();

				//Return all ACO id's
				return $rows;
			} else {
				$this->debug_text("get_object_data(): Returned $row_count rows");
				return false;
			}
		}
	}

	/*======================================================================*\
		Function:	get_object_id()
		Purpose:	Gets the object_id given the section_value AND value of the object.
	\*======================================================================*/
	function get_object_id($section_value, $value, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				break;
			case 'aro':
				$object_type = 'aro';
				break;
			case 'axo':
				$object_type = 'axo';
				break;
		}

		$this->debug_text("get_object_id(): Section Value: $section_value Value: $value Object Type: $object_type");

		$section_value = trim($section_value);
		$value = trim($value);

		if (empty($section_value) AND empty($value) ) {
			$this->debug_text("get_object_id(): Section Value ($value) AND value ($value) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("get_object_id(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$query = "select id from $object_type where section_value='$section_value' AND value='$value'";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_object_id(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$row_count = $rs->RecordCount();

			if ($row_count > 1) {
				$this->debug_text("get_object_id(): Returned $row_count rows, can only return one. This should never happen, the database may be missing a unique key.");
				return false;
			} elseif($row_count == 0) {
				$this->debug_text("get_object_id(): Returned $row_count rows");
				return false;
			} else {
				$rows = $rs->GetRows();

				//Return only the ID in the first row.
				return $rows[0][0];
			}
		}
	}

	/*======================================================================*\
		Function:	get_object_section_value()
		Purpose:	Gets the object_section_value given object id
	\*======================================================================*/
	function get_object_section_value($object_id, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				break;
			case 'aro':
				$object_type = 'aro';
				break;
			case 'axo':
				$object_type = 'axo';
				break;
		}

		$this->debug_text("get_object_section_value(): Object ID: $object_id Object Type: $object_type");

		if (empty($object_id) ) {
			$this->debug_text("get_object_section_value(): Object ID ($object_id) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("get_object_section_value(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$query = "select section_value from $object_type where id=$object_id";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_object_section_value(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$row_count = $rs->RecordCount();

			if ($row_count > 1) {
				$this->debug_text("get_object_section_value(): Returned $row_count rows, can only return one.");
				return false;
			} elseif($row_count == 0) {
				$this->debug_text("get_object_section_value(): Returned $row_count rows");
				return false;
			} else {
				$rows = $rs->GetRows();

				//Return only the ID in the first row.
				return $rows[0][0];
			}
		}
	}

	/*======================================================================*\
		Function:	add_object()
		Purpose:	Inserts a new object
	\*======================================================================*/
	function add_object($section_value, $name, $value=0, $order=0, $hidden=0, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_sections_table = 'aco_sections';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_sections_table = 'aro_sections';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_sections_table = 'axo_sections';
				break;
		}

		$this->debug_text("add_object(): Section Value: $section_value Value: $value Order: $order Name: $name Object Type: $object_type");

		$section_value = trim($section_value);
		$name = trim($name);
		$value = trim($value);
		$order = trim($order);

		if ($order == NULL OR $order == '') {
			$order = 0;
		}

		if (empty($name) OR empty($section_value) ) {
			$this->debug_text("add_object(): name ($name) OR section value ($section_value) is empty, this is required");
			return false;
		}

		if (strlen($name) >= 255 OR strlen($value) >= 230 ) {
			$this->debug_text("add_object(): name ($name) OR value ($value) is too long.");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("add_object(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		if ($this->get_object_section_section_id(NULL, $section_value, $object_type) === FALSE) {
			$this->debug_text("add_object(): Section Value: $section_value Object Type ($object_type) does not exist, this is required");
			return false;
		}

		$insert_id = $this->db->GenID($object_type.'_seq',10);
		$query = "insert into $object_type (id,section_value, value,order_value,name,hidden) VALUES($insert_id, '$section_value', '$value', '$order', '$name', $hidden)";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("add_object(): Added object as ID: $insert_id");
			return $insert_id;
		}
	}

	/*======================================================================*\
		Function:	edit_object()
		Purpose:	Edits a given Object
	\*======================================================================*/
	function edit_object($object_id, $section_value, $name, $value=0, $order=0, $hidden=0, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_map_table = 'aco_map';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_map_table = 'aro_map';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_map_table = 'axo_map';
				break;
		}

		$this->debug_text("edit_object(): ID: $object_id Section Value: $section_value Value: $value Order: $order Name: $name Object Type: $object_type");

		$section_value = trim($section_value);
		$name = trim($name);
		$value = trim($value);
		$order = trim($order);

		if (empty($object_id) OR empty($section_value) ) {
			$this->debug_text("edit_object(): Object ID ($object_id) OR Section Value ($section_value) is empty, this is required");
			return false;
		}

		if (empty($name) ) {
			$this->debug_text("edit_object(): name ($name) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("edit_object(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		//Get old value incase it changed, before we do the update.
		$query = "select value from $object_type where id=$object_id";
		$old_value = $this->db->GetOne($query);

		$query = "update $object_type set
																section_value='$section_value',
																value='$value',
																order_value='$order',
																name='$name',
																hidden=$hidden
													where   id=$object_id";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("edit_object(): Modified aco ID: $aco_id");

			if ($old_value != $value) {
				$this->debug_text("edit_object(): Value Changed, update other tables.");

				$query = "update $object_map_table set
																value='$value'
													where section_value = '$section_value'
														AND value = '$old_value'";
				$rs = $this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					return false;
				} else {
					$this->debug_text("edit_object(): Modified aco_map value: $value");
					return true;
				}

			}

			return true;
		}
	}

	/*======================================================================*\
		Function:	del_object()
		Purpose:	Deletes a given Object and, if instructed to do so,
						erase all referencing objects
						ERASE feature by: Martino Piccinato
	\*======================================================================*/
	function del_object($object_id, $object_type=NULL, $erase=FALSE) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_map_table = 'aco_map';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_map_table = 'aro_map';
				$groups_map_table = 'aro_groups_map';
				$object_group_table = 'groups_aro_map';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_map_table = 'axo_map';
				$groups_map_table = 'axo_groups_map';
				$object_group_table = 'groups_axo_map';
				break;
		}

		$this->debug_text("del_object(): ID: $object_id Object Type: $object_type, Erase all referencing objects: $erase");

		if (empty($object_id) ) {
			$this->debug_text("del_object(): Object ID ($object_id) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("del_object(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$this->db->BeginTrans();

		// Get Object section_value/value (needed to look for referencing objects)
		$query = "SELECT section_value, value FROM $object_type WHERE id = '$object_id'";
		$object = $this->db->GetRow($query);
		$section_value = $object[0];
		$value = $object[1];

		// Get ids of acl referencing the Object (if any)
		$query = "SELECT acl_id FROM $object_map_table WHERE value = '$value' AND  section_value = '$section_value'";
		$acl_ids = $this->db->GetCol($query);

		if ($erase) {
			// We were asked to erase all acl referencing it

			$this->debug_text("del_object(): Erase was set to TRUE, delete all referencing objects");

			if ($object_type == "aro" OR $object_type == "axo") {
				// The object can be referenced in groups_X_map tables
				// in the future this branching may become useless because
				// ACO might me "groupable" too

				// Get rid of groups_map referencing the Object
				$query = "DELETE FROM $object_group_table WHERE section_value = '$section_value' AND value = '$value'";
				$this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					$this->db->RollBackTrans();
					return false;
				}

			}

			if ($acl_ids) {
				//There are acls actually referencing the object

				if ($object_type == 'aco') {
					// I know it's extremely dangerous but
					// if asked to really erase an ACO
					// we should delete all acl referencing it
					// (and relative maps)

					// Do this below this branching
					// where it uses $orphan_acl_ids as
					// the array of the "orphaned" acl
					// in this case all referenced acl are
					// orhpaned acl

					$orphan_acl_ids = $acl_ids;
				} else {
					// The object is not an ACO and might be referenced
					// in still valid acls regarding also other object.
					// In these cases the acl MUST NOT be deleted

					// Get rid of $object_id map referencing erased objects
					$query = "DELETE FROM $object_map_table WHERE section_value = '$section_value' AND value = '$value'";
					$this->db->Execute($query);

					if ( is_string( $this->db->ErrorNo() ) ) {
						$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
						$this->db->RollBackTrans();
						return false;
					}

					// Find the "orphaned" acl. I mean acl referencing the erased Object (map)
					// not referenced anymore by other objects

					$sql_acl_ids = implode(",", $acl_ids);

					$query = "SELECT a.id
										FROM acl a
											LEFT JOIN $object_map_table b ON a.id=b.acl_id
											LEFT JOIN $groups_map_table c ON a.id=c.acl_id
										WHERE value IS NULL
											AND section_value IS NULL
											AND group_id IS NULL
											AND a.id in ($sql_acl_ids)";
					$orphan_acl_ids = $this->db->GetCol($query);

				} // End of else section of "if ($object_type == "aco")"

				if ($orphan_acl_ids) {
				// If there are orphaned acls get rid of them

					foreach ($orphan_acl_ids as $acl) {
						$this->del_acl($acl);
					}
				}

			} // End of if ($acl_ids)

			// Finally delete the Object itself
			$query = "DELETE FROM $object_type WHERE id = '$object_id'";
			$this->db->Execute($query);

			if ( is_string( $this->db->ErrorNo() ) ) {
				$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
				$this->db->RollBackTrans();
				return false;
			}

			$this->db->CommitTrans();
			return true;

		} // End of "if ($erase)"


		if ($object_type == 'axo' OR $object_type == 'aro') {
			// If the object is "groupable" (may become unnecessary,
			// see above

			// Get id of groups where the object is assigned:
			// you must explicitly remove the object from its groups before
			// deleting it (don't know if this is really needed, anyway it's safer ;-)

			$query = "SELECT group_id FROM $object_group_table WHERE section_value = '$section_value' AND value = '$value'";
			$groups_ids = $this->db->GetCol($query);
		}

		if ($acl_ids OR $groups_ids) {
			// The Object is referenced somewhere (group or acl), can't delete it

			$this->debug_text("del_object(): Can't delete the object as it is being referenced by GROUPs (".@implode($group_ids).") or ACLs (".@implode($acl_ids,",").")");

			return false;
		} else {
			// The Object is NOT referenced anywhere, delete it

			$query = "DELETE FROM $object_type WHERE id = '$object_id'";
			$this->db->Execute($query);

			if ( is_string( $this->db->ErrorNo() ) ) {
				$this->debug_text("edit_object(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
				$this->db->RollBackTrans();
				return false;
			}

			$this->db->CommitTrans();
			return true;
		}

		return false;
	}

	/*
	 *
	 * Object Sections
	 *
	 */

	/*======================================================================*\
		Function:	get_object_section_section_id()
		Purpose:	Gets the object_section_id given the name OR value of the section.
						Will only return one section id, so if there are duplicate names, it will return false.
	\*======================================================================*/
	function get_object_section_section_id($name = null, $value = null, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_sections_table = 'aco_sections';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_sections_table = 'aro_sections';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_sections_table = 'axo_sections';
				break;
		}

		$this->debug_text("get_aco_section_section_id(): Value: $value Name: $name Object Type: $object_type");

		$name = trim($name);
		$value = trim($value);

		if (empty($name) AND empty($value) ) {
			$this->debug_text("get_object_section_section_id(): name ($name) OR value ($value) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("get_object_section_section_id(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$query = "select id from $object_sections_table where name='$name' OR value='$value'";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("get_object_section_section_id(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$row_count = $rs->RecordCount();

			if ($row_count > 1) {
				$this->debug_text("get_object_section_section_id(): Returned $row_count rows, can only return one. Please search by value not name, or make your names unique.");
				return false;
			} elseif($row_count == 0) {
				$this->debug_text("get_object_section_section_id(): Returned $row_count rows");
				return false;
			} else {
				$rows = $rs->GetRows();

				//Return only the ID in the first row.
				return $rows[0][0];
			}
		}
	}

	/*======================================================================*\
		Function:	add_object_section()
		Purpose:	Inserts an object Section
	\*======================================================================*/
	function add_object_section($name, $value=0, $order=0, $hidden=0, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_sections_table = 'aco_sections';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_sections_table = 'aro_sections';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_sections_table = 'axo_sections';
				break;
		}

		$this->debug_text("add_object_section(): Value: $value Order: $order Name: $name Object Type: $object_type");

		$name = trim($name);
		$value = trim($value);
		$order = trim($order);

		if ($order == NULL OR $order == '') {
			$order = 0;
		}

		if (empty($name) ) {
			$this->debug_text("add_object_section(): name ($name) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("add_object_section(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		$insert_id = $this->db->GenID($object_type.'_sections_seq',10);
		$query = "insert into $object_sections_table (id,value,order_value,name,hidden) VALUES($insert_id, '$value', '$order', '$name', $hidden)";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("add_object_section(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("add_object_section(): Added object_section as ID: $insert_id");
			return $insert_id;
		}
	}

	/*======================================================================*\
		Function:	edit_object_section()
		Purpose:	Edits a given Object Section
	\*======================================================================*/
	function edit_object_section($object_section_id, $name, $value=0, $order=0, $hidden=0, $object_type=NULL) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_sections_table = 'aco_sections';
				$object_map_table = 'aco_map';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_sections_table = 'aro_sections';
				$object_map_table = 'aro_map';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_sections_table = 'axo_sections';
				$object_map_table = 'axo_map';
				break;
		}

		$this->debug_text("edit_object_section(): ID: $object_section_id Value: $value Order: $order Name: $name Object Type: $object_type");

		$name = trim($name);
		$value = trim($value);
		$order = trim($order);

		if (empty($object_section_id) ) {
			$this->debug_text("edit_object_section(): Section ID ($object_section_id) is empty, this is required");
			return false;
		}

		if (empty($name) ) {
			$this->debug_text("edit_object_section(): name ($name) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("edit_object_section(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		//Get old value incase it changed, before we do the update.
		$query = "select value from $object_sections_table where id=$object_section_id";
		$old_value = $this->db->GetOne($query);

		$query = "update $object_sections_table set
																value='$value',
																order_value='$order',
																name='$name',
																hidden=$hidden
													where   id=$object_section_id";
		$rs = $this->db->Execute($query);

		if ( is_string( $this->db->ErrorNo() ) ) {
			$this->debug_text("edit_object_section(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
			return false;
		} else {
			$this->debug_text("edit_object_section(): Modified aco_section ID: $aco_section_id");

			if ($old_value != $value) {
				$this->debug_text("edit_object_section(): Value Changed, update other tables.");

				$query = "update $object_type set
																section_value='$value'
													where section_value = '$old_value'";
				$rs = $this->db->Execute($query);

				if ( is_string( $this->db->ErrorNo() ) ) {
					$this->debug_text("edit_object_section(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
					return false;
				} else {
					$query = "update $object_map_table set
																	section_value='$value'
														where section_value = '$old_value'";
					$rs = $this->db->Execute($query);

					if ( is_string( $this->db->ErrorNo() ) ) {
						$this->debug_text("edit_object_section(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
						return false;
					} else {
						$this->debug_text("edit_object_section(): Modified ojbect_map value: $value");
						return true;
					}
				}
			}

			return true;
		}
	}

	/*======================================================================*\
		Function:	del_object_section()
		Purpose:	Deletes a given Object Section and, if explicitly
						asked, all the section objects
						ERASE feature by: Martino Piccinato
	\*======================================================================*/
	function del_object_section($object_section_id, $object_type=NULL, $erase=FALSE) {

		switch(strtolower(trim($object_type))) {
			case 'aco':
				$object_type = 'aco';
				$object_sections_table = 'aco_sections';
				break;
			case 'aro':
				$object_type = 'aro';
				$object_sections_table = 'aro_sections';
				break;
			case 'axo':
				$object_type = 'axo';
				$object_sections_table = 'axo_sections';
				break;
		}

		$this->debug_text("del_object_section(): ID: $object_section_id Object Type: $object_type, Erase all: $erase");

		if (empty($object_section_id) ) {
			$this->debug_text("del_object_section(): Section ID ($object_section_id) is empty, this is required");
			return false;
		}

		if (empty($object_type) ) {
			$this->debug_text("del_object_section(): Object Type ($object_type) is empty, this is required");
			return false;
		}

		// Get the value of the section
		$query="SELECT value FROM $object_sections_table WHERE id='$object_section_id'";
		$section_value = $this->db->GetOne($query);

		// Get all objects ids in the section
		$object_ids = $this->get_object($section_value, 1, $object_type);

		if($erase AND $object_ids) {
			// Delete all objects in the section and for
			// each object delete the referencing object
			// (see del_object method)

			foreach ($object_ids as $id) {
				$this->del_object($id, $object_type, TRUE);
			}
		}

		if($object_ids AND !$erase) {
			// There are objects in the section and we
			// were not asked to erase them: don't delete it

			$this->debug_text("del_object_section(): Could not delete the section ($section_value) as it is not empty.");

			return false;

		} else {
			// The section is empty (or emptied by this method)

			$query = "DELETE FROM $object_sections_table where id='$object_section_id'";
			$this->db->Execute($query);

			if ( is_string( $this->db->ErrorNo() ) ) {
				$this->debug_text("del_object_section(): database error: ". $this->db->ErrorMsg() ." (". $this->db->ErrorNo() .")");
				return false;
			} else {
				$this->debug_text("del_object_section(): deleted section ID: $object_section_id Value: $section_value");
				return true;
			}

		}

		return false;
	}
}
?>