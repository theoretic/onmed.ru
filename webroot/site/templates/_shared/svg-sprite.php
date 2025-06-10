<?
/*
svg sprite
AT
21.04.22
*/

$svgSprite = $svgSprite? : (Object)[];
$svgSprite->file = $svgSprite->file? : '/site/assets/svg/sprite.symbol.svg';

?>

<svg class="<?=$svgSprite->css?>">
	<? if($svgSprite->title): ?>
		<title><?=$svgSprite->title?></title>
	<? endif ?>
	<use xlink:href="<?=$svgSprite->file?>#<?=$svgSprite->symbol?>"></use>
</svg>

<?
unset( $svgSprite );