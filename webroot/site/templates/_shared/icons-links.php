<?
/*
Icons with links
not only socials!
AT
18.11.23
*/
?>

<div id="icons-links" class="flex flex-right flex-middle">
	<? if( $settings->contacts->email): ?>
		<a href="mailto:<?=$settings->contacts->email?>" class="margin-xs flex flex-middle">
			<? $svgSprite=(Object)[ 'symbol'=>'email', 'title'=>$settings->contacts->email, 'css'=>'L margin-xs icon' ]; include '_shared/svg-sprite.php' ?>
			<?=$settings->email?>
		</a>
	<? endif ?>
	<? include '_shared/socials.php' ?>
	<a href="//lk.onmed.ru" title='Личный кабинет' target="_blank" class="quarter-padded">
		<? $svgSprite=(Object)[ 'symbol'=>'user-hand-up', 'title'=>'Личный кабинет', 'css'=>'L icon' ]; include '_shared/svg-sprite.php' ?>
	</a>
</div>