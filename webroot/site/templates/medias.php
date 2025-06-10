<?
/*
Medias template
AT
20.11.24
*/

$mediaPages = $page->children();

$css[] = '/site/assets/css/medias.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/title.php' ?>

<section class="flex flex-center container block">
	<?//=$page->body?>
	<? foreach( $mediaPages as $mediaPage ): ?>
		<?
		$image = $mediaPage->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';
		?>
		<div class="padded media">
			<a href=<?=$mediaPage->url?> class="centered card block">
				<div class="media-image">
					<img data-src="<?=$image?>" data-aspect="1:1" class="adaptive">
				</div>
				<div class="padded media-texts">
					<h3 class="half-v-padded">
						<?=$mediaPage->title?>
					</h3>
					<?=$mediaPage->summary?>
				</div>
			</a>
		</div>
	<? endforeach ?>
	<? //include '_shared/sections.php' ?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>