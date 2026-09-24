<?
/*
Opens Medflex appointment modal (#reg, see _shared/modal-reg.php)
AT
24.09.26
*/

namespace ProcessWire;

$css = $css? : 'small button';
$css = 'reg '.$css;
$title = $title? : 'Записаться на приём';

?>
<a href="#reg" class="<?=$css?>">
	<?=$title?>
</a>
