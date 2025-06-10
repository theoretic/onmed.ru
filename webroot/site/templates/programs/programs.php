<?
/*
Programs
AT
14.12.19
*/

$programPages = $programPages? : $page->children("id!={$page->id}");

$css[] = '/site/assets/css/programs.css';

?>

<? foreach( $programPages as $programPage ): ?>

	<div class="program card">
		<a href="<?=$programPage->url?>">
			<div class="program-image">
				<img data-src="<?=$programPage->image->url?>">
			</div>
			<div class="centered half-padded program-texts">
				<h3>
					<?=$programPage->title?>
				</h3>
				<?=$programPage->summary?>
			</div>
		</a>
		<div class="centered program-details flex flex-center flex-middle">
			<div class="half-padded">
				<h5>Длительность</h5>
				<div class="L">
					<?=$programPage->duration?>
				</div>
			</div>
			<div class="half-padded">
				<h5>Стоимость</h5>
				<div class="L">
					<?=$programPage->price?> ₽
				</div>
			</div>
		</div>
	</div>

<? endforeach ?>