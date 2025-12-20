<?
/*
feedback button
AT
28.08.25
*/

$feedbackButtonConfig = $feedbackButtonConfig? : (Object)[
	'css'	=> 'simple inverted flex flex-middle margin-auto',
];

?>

<button data-modal="#modal-feedback" class="<?=$feedbackButtonConfig->css?>" >
	<? $svgSprite=(Object)['symbol'=>'edit', 'css'=>'XL padded icon']; include '_shared/svg-sprite.php' ?>
	оставьте отзыв
</button>

<? unset($feedbackButtonConfig) ?>