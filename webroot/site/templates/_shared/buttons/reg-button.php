<?
/*
reg button
AT
10.11.23
*/

$regButtonConfig = $regButtonConfig? : (Object)[
	'css'	=> 'simple inverted flex flex-middle margin-xs button',
];

?>

<a href="//reg.onmed.ru/" target=_blank class="<?=$regButtonConfig->css?>" >
	<? //$svgSymbol='reception'; $svgClass='XL padded icon'; include '_shared/svg-sprite.php' ?>
	Запись на приём
</a>

<? unset($regButtonConfig) ?>