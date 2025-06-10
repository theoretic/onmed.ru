<?
/*
Warning message
AT
24.10.24
*/
?>

<header id="header1-warning" class="relative padded fullscreen <?=$warningPage->warning_type->name?> message flex flex-center flex-middle">

	<div class="XL padded">
		<?=$warningPage->title?>
	</div>

	<? if( $warningPage->page_ ): ?>
		<a
			href="<?=$warningPage->page_->url?>"
			class="small button"
		>
			подробнее...
		</a>
	<? endif ?>

	<a
		class="L absolute close"
		data-href="/api/warning/disable"
		data-event="warningDisabled"
		>
		✖
	</a>
</header>