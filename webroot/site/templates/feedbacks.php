<?
/*
Feedbacks template
AT
12.02.25
*/

$feedbackPages = $pages->find("template=feedback,sort=-date,sort=-created");
$totalFeedbackPages = count($feedbackPages);
$perPage = 10;
$currentPage = $input->urlSegments[1]? : 1;
$start = $perPage * ($currentPage-1);
$totalPages = ceil( count($feedbackPages)/$perPage );
$feedbackPages->filter("start=$start,limit=$perPage");

//echo '$feedbackPages: ', var_dump($feedbackPages);//

$css[] = '/site/assets/css/feedbacks.css';
$js[] = '/site/assets/js/form.js';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include 'feedbacks/title.php' ?>


<div class="h-padded container" >
	<h2>Оставьте свой отзыв</h2>
	<section class="v-padded">
		<div class="padded card">
			<? include 'feedbacks/feedback-form.php' ?>
		</div>
	</section>
	<br>

	<? if( $totalPages>1 ) include '_shared/pagination.php' ?>
	<? include 'feedbacks/feedbacks.php' ?>
	<? if( $totalPages>1 ) include '_shared/pagination.php' ?>

	<?//=$page->body?>
	<? if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
</div>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>