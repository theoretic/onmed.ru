<?
/*
AT
01.09.25
*/
?>

<div id="submenu" class="flex">
<?
echo renderNavTree(
	$page->rootParent,
	[
		'maxDepth'			=> 1,
	]
	);
?>

</div>