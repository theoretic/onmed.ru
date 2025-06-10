<?
/*
Specialist template
AT
13.03.25
*/

include_once '_shared/functions/Field.php';
$specializations = Field\implodePagesFields( $page->specializations, 'title' );

//looking for prices
//echo '$page->offers: ', var_dump($page->offers);//
$pagesWithPrices = $page->offers->find("prices.count>0");
//echo '$pagesWithPrices: ', var_dump($offerPages);//
//$pagesWithPrices->filter("has_parent=$branchPage");

$feedbackPages = $pages->find("template=feedback,has_parent=$page,sort=-date,sort=-created");
$totalFeedbackPages = count($feedbackPages);
$perPage = 10;
$currentPage = $input->urlSegments[1]? : 1;
$start = $perPage * ($currentPage-1);
$totalPages = ceil( count($feedbackPages)/$perPage );
$feedbackPages->filter("start=$start,limit=$perPage");

$page->lastname = $page->title;

$image = $page->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

//

$css[] = '/site/assets/css/specialist.css';
$css[] = '/site/assets/css/feedbacks.css';
$css[] = '/site/assets/css/prices.css';
$js[] = '/site/assets/js/form.js';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include 'specialist/title.php' ?>

<section role="specialist" class="padded container block">
	<div class="flex flex-top card">
		<div id="image-offers">
			<div id="image">
				<img data-src="<?=$image?>">
			</div>
			<div id="offers" class="padded centered">
				<div class="padded">
					<? if($page->summary): ?>
						<h6 class="half-padded">
							<?=$page->summary?>
						</h6>
					<? endif ?>
					<? if($page->year_since): ?>
						<p class="strong centered">
							Стаж с 
							<?=$page->year_since?> г.
						</p>
					<? endif ?>
					<? if( count($page->files)>0 ) include '_shared/files.php' ?>
					<!--
					<? if($page->specializations): ?>
						<p>
							<?=$specializations?>
						</p>
					<? endif ?>
					-->
				</div>

				<? if($page->price): ?>
					<div class="w100 half-padded XL">
						Стоимость приёма от<br>
						<? include 'specialist/prices.php' ?>
					</div>
				<? endif ?>

				<br>
				<a href="<?=$page->archimedURL?>" target=_blank class="XL button">
					Запись на приём
				</a>

			</div>
		</div>
		<div id="texts">
			<div id="qualification-skills" class="flex flex-top">
				<? if($page->qualification): ?>
					<div id="qualification" class="padded">
						<h2>Квалификация и опыт работы</h2>
						<?=$page->qualification?>
					</div>
				<? endif ?>
				<? if($page->skills): ?>
					<div id="skills" class="padded">
						<h2>Профессиональные навыки</h2>
						<?=$page->skills?>
					</div>
				<? endif ?>
			</div>
		</div>
		<? if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
		<? include '_shared/sections.php' ?>
	</div>
</section>

<? if( count($pagesWithPrices)>0 ): ?>

	<section class="padded flex flex-col flex-gap" data-accordion='{"mode":"single"}'>
		<? foreach( $pagesWithPrices as $pageWithPrices): ?>
			<div class="half-h-padded card" data-accordion-item>
				<a class="strong p2 flex flex-middle flex-space-between" data-accordion-toggle>
					<?=$pageWithPrices->longtitle? : $pageWithPrices->title ?>
					<div class="XL strong" data-accordion-toggle-icon>
						+
					</div>
				</a>
				<div class="hidden" data-accordion-content>
					<a href="<?=$pageWithPrices->url?>" class="S right-floated button">
						подробнее...
					</a>
					<br>
					<? $priceItems = $pageWithPrices->prices; $config=(Object)['disclaimer'=>false]; include 'prices/prices-table.php' ?>
				</div>
			</div>
		<? endforeach ?>
	</section>
<? endif ?>

<section class="padded">
	<h2>Оставьте свой отзыв</h2>
	<div class="padded card">
		<? include 'feedbacks/feedback-form.php' ?>
	</div>

	<? if( count($feedbackPages)>0 ): ?>
		<br>
		<div class="flex">
			<h2>Отзывы</h2>
			<div>
				<span class="blue badge">
					<?=$totalFeedbackPages?>
				</span>
			</div>
		</div>
		<? if( $totalPages>1 ) include '_shared/pagination.php' ?>
		<? include 'feedbacks/feedbacks.php' ?>
		<? if( $totalPages>1 ) include '_shared/pagination.php' ?>
	<? endif ?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>