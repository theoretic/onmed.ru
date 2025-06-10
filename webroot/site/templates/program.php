<?
/*
Program template
AT
25.11.24
*/

include_once '_shared/functions/Field.php';
$tablePages = $page->offers('price>0,sort=title');
$specialistPages = $page->specialists;
if( count($specialistPages)==0 )
	$specialistPages = $branchPage->find("has_parent=" . $branchPage->get("name=doctors")->url . ",template=specialist,programs={$page->id},sort=title");
$page->lastname = $page->title;

$tableColumns = $tableColumns? : [
	//'code'			=> 'Код',
	'title'			=> 'Услуга',
	];

$css[] = '/site/assets/css/program.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/title.php' ?>

<section role="program" class="h-padded container">
<div class="flex card">

	<div id="image-details">
		<div id="image">
			<img data-src="<?=$page->image->url?>" data-aspect="1:1">
		</div>
		<div id="details" class="padded centered">
			<h5>Длительность</h5>
			<div class="L">
				<?=$page->duration?>
			</div>
			<h5>Стоимость</h5>
			<div class="L price">
				<?=$page->price?> ₽
			</div>
			<!--
			<br>
			<button class="L">
				заказать программу
			</button>
			-->
		</div>
	</div>

	<div id="texts">
		<? if($page->summary): ?>
			<div class="XL padded">
				<?=$page->summary?>
			</div>
		<? endif ?>
		<div class="padded body">
			<?=$page->body?>
		</div>
		<? if( count($tablePages)>0 ): ?>
			<div id="offers" class="padded">
				<h2>Услуги в программе</h2>
				<? include 'prices/prices-table.php' ?>
			</div>
		<? endif ?>
		<? if( count($specialistPages)>0 ): ?>
			<div id="specialists" class="">
				<!-- <h2 class="h-padded">Программу ведут</h2> -->
				<? include 'specialists/specialists.php' ?>
			</div>
		<? endif ?>
	</div>

</div>

<!--

<? //if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
<? //include '_shared/sections.php' ?>
-->

</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>