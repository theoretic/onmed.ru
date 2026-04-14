<?
/*
socials
//wa.me/79691512424?text=Здравствуйте,
AT
23.03.26
*/

//echo '$settings->socials->data: ', var_dump($settings->socials->data);//

?>

<? foreach($settings->socials->data as $name=>$value): ?>
	<?
	if( !$value || $value=='' ) continue;

	switch($name){
		default:
			$href=$value;
		break;
	}
	?>

	<a href="<?=$href?>" title="<?=$name?>" target="_blank" class="quarter-padded">
		<? $svgSprite=(Object)[ 'symbol'=>$name, 'title'=>$name, 'css'=>'L icon' ]; include '_shared/svg-sprite.php' ?>
	</a>

<? endforeach ?>