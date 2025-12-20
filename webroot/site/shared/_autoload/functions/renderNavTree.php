<?php
/*
renderNavTree
mod by AT
01.09.25
 */


function renderNavTree($root, $params) {

//echo '$root: ', var_dump($root);//

	//$params['_initialDepth'] = $params['_initialDepth']? : count($root->parents);
	if( $root->parents ) $params['_initialDepth'] = count($root->parents);
	$params['maxDepth'] = $params['maxDepth']? : 0;

	extract($params);

	//$onlyNonempty = $onlyNonempty? : true;
	//$fieldNames = $fieldNames? : '';

	//echo "renderNavTree($root, $maxDepth, $onlyNonempty, $fieldNames) started.<br>";//

	// if we were given a single Page rather than a group of them, we'll pretend they gave us a group of them (a group/array of 1)
	$items = ($root instanceof \Processwire\Page)? $root->children : $root;
	$out = '';

	foreach($items as $item) {
		//$item = ($item instanceof \Processwire\Page)? $item : \Processwire\wire('pages')->get($item);

		if( $onlyNonempty && !$item->hasChildren() ) continue;
		if( $deniedItemTemplates && in_array($item->template,$deniedItemTemplates) ) continue;
		if( $allowedItemTemplates && !in_array($item->template,$allowedItemTemplates) ) continue;

		$level = count($item->parents) - $params['_initialDepth'];

		//classes

		$hasChildren = $item->hasChildren();
		$hasCurrentPageInside = $item->hasChildren( 'id=' . \Processwire\wire('page')->id );

//echo $item->title . ' $hasCurrentPageInside: ', var_export($hasCurrentPageInside), '<br>';//

		$ulClass = '';
		if( $levelClass ) $ulClass = $levelClass . $level;
		$ulClass = $ulClass? "class='$ulClass'" : '';

		$liClasses = [];
		if( $item->id == \Processwire\wire('page')->id ) $liClasses[]= 'current';
		if( $hasChildren ) $liClasses[] = 'expandable';
		if( $hasCurrentPageInside ) $liClasses[] = 'expanded';
		$liClasses = count($liClasses)>0? "class='" . implode(' ', $liClasses) . "'" : '';

		$aClass = $aClass? "class='$aClass'" : '';
		$aTitle = $item->title;

		$expander = '';
		switch( true ){
			case $hasCurrentPageInside:
				$expander = "<a class=expander>-</a>";
			break;
			case $hasChildren:
				$expander = "<a class=expander>+</a>";
			break;
		}


		$url = $item->url;

		//excluding URL segments if neessary
		if( $excludeURLSegments ){
			$segments = explode( '/', $url );
			foreach( $segments as $i=>$segment ){
				if( !in_array( $segment, $excludeURLSegments )) continue;
				unset( $segments[$i] );
			}
			$url = implode( '/', $segments );
		}

		//using only the last url segment if necessary
		if( $useOnlyLastURLSegment ){
			$segments = explode( '/', $url );
//echo '$segments: ', var_dump($segments);//
			$lastSegment = array_pop($segments);
			if( $lastSegment == '' ) $lastSegment = array_pop($segments);
			$url = "/$lastSegment";
		}

		//open the list item
		$out .= "
<li $liClasses>
	$expander
	<a href='{$url}' title='$aTitle' $aClass>{$item->title}</a>
";

		/*
		//extra fields
		if($fieldNames) foreach(explode(' ', $fieldNames) as $fieldName) {
			$value = ($fieldName == 'total')?  count($item->children) : $item->get($fieldName);
			if($value) $out .= " <div class='$fieldName'>$value</div>";
		}
		*/

		//recursion occurs here
		if( $item->hasChildren() && $level <= $params['maxDepth'] ) {
			//vardump($params['maxDepth']);
			$out .= renderNavTree( $item->children, $params );
		}

		// close the list item
		$out .= "</li>";
	}

	// if output was generated above, wrap it in a <ul>
	if($out) $out = "<ul $ulClass>$out</ul>";

	$hasChildren = false;
	$hasCurrentPageInside = false;

	// return the markup we generated above
	return $out;
}