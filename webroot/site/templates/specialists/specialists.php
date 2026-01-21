<?
/*
specialists
$specialistPages should be defined outside this file
AT
29.12.25
*/

include_once '_shared/functions/Field.php';
//$i = 0;
?>

<div class="v-padded flex flex-center flex-gap">
	<? foreach( $specialistPages as $specialistPage ) include 'specialists/specialistCard.php' ?>
</div>