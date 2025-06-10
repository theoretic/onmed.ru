<?
/*
Discounts template
AT
20.11.24
*/

//echo '$branchPage: ', var_dump($branchPage);

$now = time();
//$selector = $page->non_actual? "template=discount, (date_start>$now), (date_end<$now)" : "template=discount, date_start<$now, date_end>$now";
//$discountPages = $pages->find($selector);
//$nonActualPage = $pages->findOne("non_actual>0");
//$discountPages = $branchPage->find("template=discount, date_start<$now, date_end>$now");
//$discountPages = $branchPage->find("template=discount");
$discountPages = $branchPage->find("template=discount,date_end>$now");

//echo '$discountPages: ', var_dump($discountPages);//

$css[] = '/site/assets/css/discounts.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

		<div role="content">
			<?=$page->body?>
			<section role="discounts" class="flex flex-center flex-gap container">
				<? include 'discounts/discounts.php' ?>
			</section>

<?/*
			<? if( $page != $nonActualPage): ?>
				<div class="centered">
					<a href="<?=$nonActualPage->url?>" class="XL transparent button">
						неактуальные скидки
					</a>
				</div>
			<? endif ?>
*/?>
				<?//=$page->body?>

			<?// if($page->images) { $images = $page->images; include '_shared/thumbs.php'; } ?>
			<?// if( count($page->sections)>0 ) include '_shared/sections.php' ?>
			<br>
			<br>
		</div>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>