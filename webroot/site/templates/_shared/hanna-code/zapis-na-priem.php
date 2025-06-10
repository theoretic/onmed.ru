<?
/*
AT
24.01.24
*/

namespace ProcessWire;

$url = $url? : '//reg.onmed.ru/';
$css = $css? : 'small button';
$css = 'reg '.$css;
$title = $title? : 'Записаться на приём';

?>
<a href="<?=$url?>" target="_reg" class="<?=$css?>">
	<?=$title?>
</a>