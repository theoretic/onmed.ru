<?
/*
Specialist template
appointment view
AT
12.05.26
*/

$page->lastname = $page->title;

$image = $page->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

//

//$css[] = '/site/assets/css/specialist.css';
$css[] = '/site/assets/css/appointment-specialist.css';
$css[] = '/site/assets/css/appointment-form.css';
$js[] = '/site/assets/js/components/appointment-specialist.js';
$js[] = '/site/assets/js/components/appointment-form.js';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include 'specialist/title.php' ?>

<section class="padded container block">

		<div class="padded flex flex-center flex-middle gap centered">
			<div class="min-w-16r">
				<img data-src="<?=$image?>" class="margin-L rounded height-M">
			</div>
			<div class="min-w-16r">
				<h2>
					Запись на приём
				</h2>
			</div>
		</div>

	<appointment-specialist doctor_id="<?=$page->id_medflex?>"></appointment-specialist>
	<appointment-form></appointment-form>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>