<?php
/**
 * Given a group of pages, render a <ul> navigation tree
 *
 * This is here to demonstrate an example of a more intermediate level
 * shared function and usage is completely optional. This is very similar to
 * the renderNav() function above except that it can output more than one
 * level of navigation (recursively) and can include other fields in the output.
 *
 * @param array|PageArray $items
 * @param int $maxDepth How many levels of navigation below current should it go?
 * @param string $fieldNames Any extra field names to display (separate multiple fields with a space)
 * @param string $class CSS class name for containing <ul>
 * @return string
 *

mod by AT
27.11.19
 */


function renderNavTree($root, $params) {

	$params['_initialDepth'] = $params['_initialDepth']? : count($root->parents);
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

		$ulClass_ = '';
		if( $levelClass ) $ulClass_ = $levelClass . $level;
		$ulClass_ = $ulClass_? "class='$ulClass_'" : '';

		$liClasses_ = [];
		if( $item->id == \Processwire\wire('page')->id ) $liClasses_[]= 'current';
		if( $item->hasChildren() ) $liClasses_[] = 'expandable';
		$liClasses_ = count($liClasses_)>0? "class='" . implode(' ', $liClasses_) . "'" : '';

		$aClass = $aClass? "class='$aClass'" : '';
		$aTitle_ = $item->longtitle? : $item->title;

		$expander = $item->hasChildren()? "<a class=expander>+</a>" : "";

		//open the list item
		$out .= "
<li $liClasses_>
	$expander
	<a href='{$item->url}' title='$aTitle_' $aClass>{$item->title}
</a>
";

		/*
		//extra fields
		if($fieldNames) foreach(explode(' ', $fieldNames) as $fieldName) {
			$value = ($fieldName == '_total')?  count($item->children) : $item->get($fieldName);
			if($value) $out .= " <div class='$fieldName'>$value</div>";
		}
		*/

		//recursion occurs here
		if( $item->hasChildren() && $level <= $params['maxDepth'] ) {
			//var_dump($params['maxDepth']);
			$out .= renderNavTree( $item->children, $params );
		}

		// close the list item
		$out .= "</li>";
	}

	// if output was generated above, wrap it in a <ul>
	if($out) $out = "<ul $ulClass_>$out</ul>";

	// return the markup we generated above
	return $out;
}

