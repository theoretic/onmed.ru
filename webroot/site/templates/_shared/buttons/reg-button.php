<?
/*
reg button
AT
13.05.26
*/

$regButtonConfig = $regButtonConfig? : (Object)[
	'css'	=> 'simple inverted flex flex-middle margin-xs button',
];

?>

<!--
<a href="//reg.onmed.ru/" target=_blank class="<?=$regButtonConfig->css?>" >
	<? //$svgSprite=(Object)['symbol'=>'reception', 'css'=>'XL padded icon']; include '_shared/svg-sprite.php' ?>
	Запись на приём
</a>
-->

<? unset($regButtonConfig) ?>