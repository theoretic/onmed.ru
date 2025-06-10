<?
/*
offers menu
AT
20.11.23
*/


//$params = [ 'maxDepth'=>5, 'levelClass'=>'l', 'excludeURLSegments'=>['doctors', 'offers', 'specialists'] ];
//$params = [ 'maxDepth'=>5, 'levelClass'=>'l', 'useOnlyLastURLSegment'=>1 ];
$params = [ 'maxDepth'=>5, 'levelClass'=>'l', ];

?>

<div id="offers-menu">
	<a id="offers-menu-hide" class="L right-floated" data-toggle-class='{ "html":"offers-menu-visible" }'>
		✕
	</a>
	<?=renderNavTree($offersPage,$params)?>
</div>