<?php
function __autoload($class) {
	require_once('lib/' . strtolower($class) . '.php');
}


function bootstrapItems($items, $level = 0) {
	
	// Starting from items at root level
	if( !is_array($items) ) {
		$items = $items->roots();
	}
	
	foreach( $items as $item ) {
		$child = $item->hasChildren();
		echo '<li'.(($child && $level != 0) ? ' class="dropdown-submenu"' : '').'>';
		echo '<a href="'.$item->link->get_url().'"'.(($child) ? ' class="dropdown-toggle" data-toggle="dropdown"' : ' target="frame2"').'>';
		echo $item->link->get_text();
		if($child && $level == 0){
			echo ' <b class="caret"></b>';
		}
		echo '</a>';
		if($child) {
			echo '<ul class="dropdown-menu'.(($level == 0) ? ' multi-level' : '').'">';
			bootstrapItems( $item->children(), $level + 1 );
			echo '</ul>';
		}
		echo '</li>';
	}
}

?>