<?
/*
Specialist template
appointment view
AT
05.05.26
*/

$page->lastname = $page->title;

$image = $page->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

//

//$css[] = '/site/assets/css/specialist.css';
$css[] = '/site/assets/css/appointment-specialist.css';
$js[] = '/site/assets/js/components/appointment-specialist.js';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include 'specialist/title.php' ?>

<section class="padded container block">
	<appointment-specialist></appointment-specialist>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>