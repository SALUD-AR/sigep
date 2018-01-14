<?php
require_once("admin/gacl_admin.inc.php");

$db_table_prefix = $gacl->_db_table_prefix;
$db_type = $gacl->_db_type;
$db_name = $gacl->_db_name;
$db_host = $gacl->_db_host;
$db_user = $gacl->_db_user;
$db_password = $gacl->_db_password;
$db_name = $gacl->_db_name;

function echo_success($text) {
    echo "<font color=\"green\"><b>Success!</b></font> $text<br>\n";   	
}

function echo_failed($text) {
	global $failed;
	
	echo "<font color=\"red\"><b>Failed!</b></font> $text<br>\n";
	
	$failed++;
}

function echo_normal($text) {
        echo "$text<br>\n";   	
}

/*
 * Test database connection
 */
echo_normal("<hr>");
echo_normal("Testing database connection...");
echo_normal("<br>");

if (is_resource($db->_connectionID)) {
	echo_success("Connected to \"<b>$db_type</b>\" database on \"<b>$db_host</b>\".");
} else {
	echo_failed("<b>ERROR</b> connecting to database,
				  <br>are you sure your specified the proper host, user name, password, and database in <b>admin/gacl_admin.inc.php</b>?
				  <br>Did you create the database, and give read/write permissions to \"<b>$db_user</b>\" already?");
	exit;
}

/*
 * Do database specific stuff.
 */
echo_normal("<hr>");
echo_normal("Testing database type...");
echo_normal("<br>");

switch ($db_type) {
	case mysql:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");
	
		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("show databases");

		if (in_array($db_name, $databases) ) {
			echo_success("Good, database \"<b>$db_name</b>\" already exists!");	
		} else {
			echo_normal("Database \"<b>$db_name</b>\" does not exist!");
			echo_normal("Lets try to create it...");
			
			if (!$db->Execute("create database $db_name") ) {
				echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
			} else {
				echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

				//Reconnect. Hrmm, this is kinda weird.
				$db->Connect($db_host, $db_user, $db_password, $db_name);
			}			
		}
		
		/*
		 * Create tables.
		 */
		echo_normal("Attempting to create tables in \"<b>$db_name</b>\"");

		$table_array = 	array (		acl =>
																"
																CREATE TABLE acl (
																  id int(12) NOT NULL default '0',
																  allow smallint(1) NOT NULL default '0',
																  enabled smallint(1) NOT NULL default '0',
																  return_value text default NULL,
																  note text default NULL,
																  updated_date int(12) NOT NULL default '0',
																  UNIQUE KEY id (id),
																  INDEX (enabled)
																  ) TYPE=MyISAM
																",
													aco =>
																"
																CREATE TABLE aco (
																  id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY value (section_value,value),
																  UNIQUE KEY id (id),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													aco_map =>
																"
																CREATE TABLE aco_map (
																  acl_id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '0',
																  INDEX (acl_id)
																) TYPE=MyISAM
																",
													aco_sections =>
																"
																CREATE TABLE aco_sections (
																  id int(12) NOT NULL default '0',
																  value varchar(230) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY value (value),
																  UNIQUE KEY id (id),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													aro =>
																"
																CREATE TABLE aro (
																  id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY value (section_value,value),
																  UNIQUE KEY id (id),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													aro_map =>
																"
																 CREATE TABLE aro_map (
																  acl_id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '0',
																  INDEX (acl_id)
																) TYPE=MyISAM
																",
													aro_sections =>
																"
																CREATE TABLE aro_sections (
																  id int(12) NOT NULL default '0',
																  value varchar(255) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY id (id),
																  UNIQUE KEY value (value),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													axo =>
																"
																CREATE TABLE axo (
																  id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY value (section_value,value),
																  UNIQUE KEY id (id),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													axo_map =>
																"
																 CREATE TABLE axo_map (
																  acl_id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '0',
																  INDEX (acl_id)
																) TYPE=MyISAM
																",
													axo_sections =>
																"
																CREATE TABLE axo_sections (
																  id int(12) NOT NULL default '0',
																  value varchar(255) NOT NULL default '',
																  order_value int(10) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  hidden smallint(1) NOT NULL default '0',
																  UNIQUE KEY id (id),
																  UNIQUE KEY value (value),
																  INDEX (hidden)
																) TYPE=MyISAM
																",
													aro_groups =>
																"
																CREATE TABLE aro_groups (
																  id int(12) NOT NULL default '0',
																  parent_id int(12) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  PRIMARY KEY  (id),
																  KEY parent_id (parent_id)
																) TYPE=MyISAM
																",
													groups_aro_map =>
																"
																CREATE TABLE groups_aro_map (
																  group_id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '0',
																  UNIQUE KEY group_id (group_id,section_value,value)
																) TYPE=MyISAM
																",
													aro_groups_map =>
																"
																CREATE TABLE aro_groups_map (
																  acl_id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  PRIMARY KEY  (acl_id,group_id),
																  INDEX (acl_id)
																) TYPE=MyISAM
																",	
													aro_groups_path =>
																"
																CREATE TABLE aro_groups_path (
																  id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  tree_level int(12) NOT NULL default '0',
																  PRIMARY KEY  (id,tree_level),
																  KEY group_id (group_id,tree_level)
																) TYPE=MyISAM
																",
													aro_groups_path_map =>
																"
																CREATE TABLE aro_groups_path_map (
																  path_id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  PRIMARY KEY  (path_id,group_id)
																) TYPE=MyISAM
																",
													axo_groups =>
																"
																CREATE TABLE axo_groups (
																  id int(12) NOT NULL default '0',
																  parent_id int(12) NOT NULL default '0',
																  name varchar(255) NOT NULL default '',
																  PRIMARY KEY  (id),
																  KEY parent_id (parent_id)
																) TYPE=MyISAM
																",
													groups_axo_map =>
																"
																CREATE TABLE groups_axo_map (
																  group_id int(12) NOT NULL default '0',
																  section_value varchar(230) NOT NULL default '0',
																  value varchar(230) NOT NULL default '0',
																  UNIQUE KEY group_id (group_id,section_value,value)
																) TYPE=MyISAM
																",
													axo_groups_map =>
																"
																CREATE TABLE axo_groups_map (
																  acl_id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  PRIMARY KEY  (acl_id,group_id),
																  INDEX (acl_id)
																) TYPE=MyISAM
																",	
													axo_groups_path =>
																"
																CREATE TABLE axo_groups_path (
																  id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  tree_level int(12) NOT NULL default '0',
																  PRIMARY KEY  (id,tree_level),
																  KEY group_id (group_id,tree_level)
																) TYPE=MyISAM
																",
													axo_groups_path_map =>
																"
																CREATE TABLE axo_groups_path_map (
																  path_id int(12) NOT NULL default '0',
																  group_id int(12) NOT NULL default '0',
																  PRIMARY KEY  (path_id,group_id)
																) TYPE=MyISAM
																"
													);

		$tables = $db->GetCol("show tables");
		if (!$tables) {
			$tables = array();	
		}

		foreach ($table_array as $table_name => $schema) {
			echo_normal("Attempting to create table: \"<b>$table_name</b>\"...");

			//Check to see if table already exists.
			if (in_array($table_name, $tables) ) {
				echo_success("Table: \"<b>$table_name</b>\" already exists! ");	
			} else {
				if (!$db->Execute($schema) ) {
					echo_failed("Creation of table: \"<b>$table_name</b>\" ");	
				} else {
					echo_success("Table \"<b>$table_name</b>\" created successfully!");	
				}
			}		
		}
		
		break;
	case postgres7:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");
	
		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("select datname from pg_database");

		if (in_array($db_name, $databases) ) {
			echo_success("Good, database \"<b>$db_name</b>\" already exists!");	
		} else {
			echo_normal("Database \"<b>$db_name</b>\" does not exist!");
			echo_normal("Lets try to create it...");
			
			if (!$db->Execute("create database $db_name") ) {
				echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
			} else {
				echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

				//Reconnect. Hrmm, this is kinda weird.
				$db->Connect($db_host, $db_user, $db_password, $db_name);
			}			
		}

		/*
		 * Create tables.
		 */
		echo_normal("Attempting to create tables in \"<b>$db_name</b>\"");

		$table_array = array ( 	acl =>
																	"
																	CREATE TABLE acl (
																	   id integer NOT NULL default 0,
																	   allow smallint NOT NULL default 0,
																	   enabled smallint NOT NULL default 0,
																	   return_value text default NULL,
																	   note text default NULL,								   
																	   updated_date integer NOT NULL default 0
																	);
																	create unique index id_acl on acl(id);
																	create index id_enabled_acl on acl(id,enabled);

																	",
											aco =>
																	"
																	CREATE TABLE aco (
																	   id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index section_value_value_aco on aco(section_value,value);
																	create unique index id_aco on aco(id);
																	create index hidden_aco on aco(hidden);
																	",
											aco_map =>
																	"
																	CREATE TABLE aco_map (
																	   acl_id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default 0
																	);
																	create index acl_id_aco_map on aco_map(acl_id);
																	",
											aco_sections =>
																	"
																	CREATE TABLE aco_sections (
																	   id integer NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index id_aco_sections on aco_sections(id);
																	create unique index value_aco_sections on aco_sections(value);
																	create index hidden_aco_sections on aco_sections(hidden);								
																	",
											aro =>
																	"
																	CREATE TABLE aro (
																	   id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index section_value_value_aro on aro(section_value,value);
																	create unique index id_aro on aro(id);
																	create index hidden_aro on aro(hidden);
																	",
											aro_map =>
																	"
																	CREATE TABLE aro_map (
																	   acl_id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default 0
																	);
																	create index acl_id_aro_map on aro_map(acl_id);														
																	",
											aro_sections =>
																	"
																	CREATE TABLE aro_sections (
																	   id integer NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index id_aro_sections on aro_sections(id);
																	create unique index value_aro_sections on aro_sections(value);
																	create index hidden_aro_sections on aro_sections(hidden);								
																	",
											axo =>
																	"
																	CREATE TABLE axo (
																	   id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index section_value_value_axo on axo(section_value,value);
																	create unique index id_axo on axo(id);
																	create index hidden_axo on axo(hidden);
																	",
											axo_map =>
																	"
																	CREATE TABLE axo_map (
																	   acl_id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default 0
																	);
																	create index acl_id_axo_map on axo_map(acl_id);														
																	",
											axo_sections =>
																	"
																	CREATE TABLE axo_sections (
																	   id integer NOT NULL default 0,
																	   value varchar(255) NOT NULL default '',
																	   order_value integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default '',
																	   hidden smallint NOT NULL default '0'
																	);
																	create unique index id_axo_sections on axo_sections(id);
																	create unique index value_axo_sections on axo_sections(value);
																	create index hidden_axo_sections on axo_sections(hidden);								
																	",
											aro_groups =>
																	"
																	CREATE TABLE aro_groups (
																	   id integer NOT NULL default 0,
																	   parent_id integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default ''
																	);
																	create unique index id_aro_groups on aro_groups(id);
																	create index parent_id_aro_groups on aro_groups(parent_id);								
																	",
											groups_aro_map =>
																	"
																	CREATE TABLE groups_aro_map (
																	   group_id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default 0
																	);
																	create unique index group_id_aro_id_groups_aro_map on groups_aro_map(group_id,section_value, value);
																	",
											aro_groups_map =>
																	"
																	CREATE TABLE aro_groups_map (
																	   acl_id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0
																	);
																	create unique index acl_id_group_id_aro_groups_map on aro_groups_map(acl_id, group_id);
																	",
											aro_groups_path =>
																	"
																	CREATE TABLE aro_groups_path (
																	   id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0,
																	   tree_level integer NOT NULL default 0
																	);
																	create unique index id_group_id_tree_level_aro_groups_path on aro_groups_path(id, group_id, tree_level);
																	",
											aro_groups_path_map =>
																	"
																	CREATE TABLE aro_groups_path_map (
																	   path_id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0
																	);
																	create unique index path_id_group_id_aro_groups_path_map on aro_groups_path_map(path_id, group_id);
																	",
											axo_groups =>
																	"
																	CREATE TABLE axo_groups (
																	   id integer NOT NULL default 0,
																	   parent_id integer NOT NULL default 0,
																	   name varchar(255) NOT NULL default ''
																	);
																	create unique index id_axo_groups on axo_groups(id);
																	create index parent_id_axo_groups on axo_groups(parent_id);								
																	",
											groups_axo_map =>
																	"
																	CREATE TABLE groups_axo_map (
																	   group_id integer NOT NULL default 0,
																	   section_value varchar(255) NOT NULL default 0,
																	   value varchar(255) NOT NULL default 0
																	);
																	create unique index group_id_axo_id_groups_aro_map on groups_axo_map(group_id,section_value, value);
																	",
											axo_groups_map =>
																	"
																	CREATE TABLE axo_groups_map (
																	   acl_id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0
																	);
																	create unique index acl_id_group_id_axo_groups_map on axo_groups_map(acl_id, group_id);
																	",
											axo_groups_path =>
																	"
																	CREATE TABLE axo_groups_path (
																	   id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0,
																	   tree_level integer NOT NULL default 0
																	);
																	create unique index id_group_id_tree_level_axo_groups_path on axo_groups_path(id, group_id, tree_level);
																	",
											axo_groups_path_map =>
																	"
																	CREATE TABLE axo_groups_path_map (
																	   path_id integer NOT NULL default 0,
																	   group_id integer NOT NULL default 0
																	);
																	create unique index path_id_group_id_axo_groups_path_map on axo_groups_path_map(path_id, group_id);
																	"																
										);
												
		$tables = $db->GetCol("select tablename from pg_tables");
		if (!$tables) {
			$tables = array();	
		}

		foreach ($table_array as $table_name => $schema) {
				echo_normal("Attempting to create table: \"<b>$table_name</b>\"...");

				//Check to see if table already exists.
				if (in_array($table_name, $tables) ) {
						echo_success("Table: \"<b>$table_name</b>\" already exists! ");
				} else {
						if (!$db->Execute($schema) ) {
								echo_failed("Creation of table: \"<b>$table_name</b>\" ");
						} else {
								echo_success("Table \"<b>$table_name</b>\" created successfully!");
						}
				}
		}

		break;

	case oci8-po:
		echo_success("Compatible database type \"<b>$db_type</b>\" detected!");

		echo_normal("Making sure database \"<b>$db_name</b>\" exists...");

		$databases = $db->GetCol("select '$db_name' from dual");

		if (in_array($db_name, $databases) ) {
				echo_success("Good, database \"<b>$db_name</b>\" already exists!");
		} else {
				echo_normal("Database \"<b>$db_name</b>\" does not exist!");
				echo_normal("Lets try to create it...");

				if (!$db->Execute("create database $db_name") ) {
						echo_failed("Database \"<b>$db_name</b>\" could not be created, please do so manually.");
				} else {
						echo_success("Good, database \"<b>$db_name</b>\" has been created!!");

						//Reconnect. Hrmm, this is kinda weird.
						$db->Connect($db_host, $db_user, $db_password, $db_name);
				}
		}

		/*
		 * Create tables.
		 */
		echo_normal("Attempting to create tables in \"<b>$db_name</b>\"");

		$index_array = array (
											acl_idx_1 =>			"create unique index id_acl on acl(id)",
											ace_idx_2 =>			"create index id_enabled_acl on acl(id,enabled)",
											aco_idx1 => 			"create unique index section_value_value_aco on aco(section_value,value)",
											ace_idx2 =>     		"create unique index id_aco on aco(id)",
											aco_map_idx =>		"create index acl_id_aco_map on aco_map(acl_id)",
											aco_sec_idx_1 =>	"create unique index id_aco_sections on aco_sections(id)",
											aco_sec_idx_2 =>	"create unique index value_aco_sections on aco_sections(value)",
											aro_map_idx_1 =>	"create index acl_id_aro_map on aro_map(acl_id)",
											group_idx_1 =>		"create unique index id_groups on groups(id)",
											group_dix_2 =>		"create index parent_id_groups on groups(parent_id)",
											idx_1  => 				"create unique index group_id_aro_id_groups_aro_map on groups_aro_map(group_id,aro_section_value, aro_value)",
											idx_2  => 				"create unique index path_id_group_id_groups_path_map on groups_path_map(path_id, group_id)",
											idx_3  => 				"create unique index id_group_id_tree_level_groups_path on groups_path(id, group_id, tree_level)",
											idx_4  => 				"create unique index acl_id_group_id_groups_map on groups_map(acl_id, group_id)"
										);

		$table_array = array (
											acl =>
																	"
																	CREATE TABLE acl (
																	   id integer default 0 NOT NULL,
																	   allow smallint default 0 NOT NULL,
																	   enabled smallint default 0 NOT NULL,
																	   updated_date integer default 0 NOT NULL
																	)",
											aco =>
																	"
																	CREATE TABLE aco (
																	   id integer default 0 NOT NULL,
																	   section_value varchar(255) default 0 NOT NULL,
																	   value varchar(255) default '' NOT NULL,
																	   order_value integer default 0 NOT NULL,
																	   name varchar(255) default '' NOT NULL
																	)        ",
											aco_map =>
																	"
																	CREATE TABLE aco_map (
																	   acl_id integer default 0 NOT NULL,
																	   aco_section_value varchar(255) default '' NOT NULL,
																	   aco_value varchar(255) default '' NOT NULL
																	)
																	",
											aco_sections =>
																	"
																	CREATE TABLE aco_sections (
																	   id integer default 0 NOT NULL,
																	   value varchar(255)  default '' NOT NULL,
																	   order_value integer default 0 NOT NULL,
																	   name varchar(255) default '' NOT NULL
																	)
																	",
											aro =>
																	"
																	CREATE TABLE aro (
																	   id integer default 0 NOT NULL,
																	   section_value varchar(255) default 0 NOT NULL,
																	   value varchar(255) default '' NOT NULL,
																	   order_value integer default 0 NOT NULL,
																	   name varchar(255) default '' NOT NULL
																	)
																	",
											aro_map =>
																   "
																	CREATE TABLE aro_map (
																	   acl_id integer default 0 NOT NULL,
																	   aro_section_value varchar(255) default '' NOT NULL,
																	   aro_value varchar(255) default '' NOT NULL
																	)
																	",
											aro_sections =>
																	"
																	CREATE TABLE aro_sections (
																	   id integer default 0 NOT NULL,
																	   value varchar(255) default '' NOT NULL,
																	   order_value integer default 0 NOT NULL,
																	   name varchar(255) default '' NOT NULL
																	)
																	",
											groups =>
																	"
																	CREATE TABLE groups (
																	   id integer default 0 NOT NULL,
																	   parent_id integer default 0 NOT NULL,
																	   name varchar(255) default '' NOT NULL
																	)
																	",
											groups_aro_map =>
																	"
																	CREATE TABLE groups_aro_map (
																	   group_id integer default 0 NOT NULL,
																	   aro_section_value varchar(255) default '' NOT NULL,
																	   aro_value varchar(255) default '' NOT NULL
																	)
																	",
											groups_map =>
																	"
																	CREATE TABLE groups_map (
																	  acl_id integer default 0 NOT NULL,
																	   group_id integer default 0 NOT NULL
																	)
																	",
											groups_path =>
																	"
																	CREATE TABLE groups_path (
																	   id integer default 0 NOT NULL,
																	   group_id integer default 0 NOT NULL,
																	   tree_level integer default 0 NOT NULL
																	)
																	",
											groups_path_map =>
																	"
																	CREATE TABLE groups_path_map (
																	   path_id integer default 0 NOT NULL,
																	   group_id integer default 0 NOT NULL
																	)
																	"
		);

		$tables = $db->GetCol("select tablename from all_tables");
		if (!$tables) {
				$tables = array();
		}

		foreach ($table_array as $table_name => $schema) {
				echo_normal("Attempting to create table: \"<b>$table_name</b>\"...");

				//Check to see if table already exists.
				if (in_array($table_name, $tables) ) {
						echo_success("Table: \"<b>$table_name</b>\" already exists! ");
				} else {
						if (!$db->Execute($schema) ) {
								echo_failed("Creation of table: \"<b>$table_name</b>\" ");
						} else {
								echo_success("Table \"<b>$table_name</b>\" created successfully!");
						}
				}
		}

		foreach ($index_array as $index_name => $schema) {
				echo_normal("Attempting to create index: \"<b>$table_name</b>\"...");

			   if (!$db->Execute($schema) ) {
						echo_failed("Creation of index: \"<b>$index_name</b>\" ");
				} else {
						echo_success("Index \"<b>$index_name</b>\" created successfully!");
				}
		}

		break;
	default:
		echo_failed("Sorry, <b>setup.php</b> currently does not support \"<b>$db_type</b>\" databases.
					<br>You'll need to create the tables manually.
					<br> Please email <b>$author_email</b> table schemas so support for \"<b>$db_type</b>\" can be added.");
}

if ( $failed <= 0 ) {
	echo_success(
"
Installation Successful!!!<br>\n
<br>\n
<font color=\"red\"><b>*IMPORTANT*</b></font><br>\n
Please make sure you create the <b>&lt;phpGACL root&gt;/admin/smarty/templates_c</b> directory, <br>\n
and give it <b>write permissions</b> for the user your web server runs as. 

Please read the manual, and example.php, then <a href=\"admin/acl_admin.php\"><b>Go here!</b></a> to get started.
");	
} else {
	echo_failed("Please fix the above errors and try again.");	
}
?>
