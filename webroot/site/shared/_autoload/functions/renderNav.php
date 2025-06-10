<?php namespace ProcessWire;

function renderNav(PageArray $items) {

	$out = '';

	foreach($items as $item) {
		$class = ( $item->id == wire('page')->id )? 'class="current"' : '';

		$out .= "<a href='$item->url' $class>$item->title</a> ";

		// if the item has summary text, include that too
		//if($item->summary) $out .= "<div class='summary'>$item->summary</div>";

		}

	return $out;
}