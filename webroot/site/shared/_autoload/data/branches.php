<?
/*
branches data
AT
17.10.23
*/

$branches = [
	'home'		=> [ 'color' => '0,133,255' ],
	'deti'		=> [ 'color' => '131,192,0' ],
	'dent'		=> [ 'color' => '0,75,163' ],
	];

foreach( $branches as $name=>$branch ){
	if($branchPage->name != $name) continue;
	$currentBranch = $branch;
	break;
}