<?
/*
offers grid
AT
30.12.21
*/

$offersGrid = (Object)[];
$offersGrid->pages = 10;
$offersGrid->pagesPerRow = 3;
$offersGrid->cellsPerRow = 10;
$offersGrid->cellsPerPageMin = 2;
$offersGrid->cellsPerPageMax = 4;

$now = $now? : time();
$offerPages = $offerPages? : $pages->find("template=offer,sort=title");
$offerPages->filter("limit={$offersGrid->pages}");

//$pagesInCurrentRow = 0;
//$cellsRemaining = $offersGrid->cellsPerRow;

/*
//calculating page widths
$offersGrid->rows = ceil( $offersGrid->pages / $offersGrid->pagesPerRow );
$i = 0;
$pageWidths = [];

foreach( $row=0; $row<$offersGrid->rows; $row++ ){

	$cellsRemaining = $offersGrid->cellsPerRow;

	foreach( $page=0; $page<$offersGrid->pagesPerRow; $page++ ){
		$max = $cellsRemaining > $offersGrid->cellsPerPageMax? $offersGrid->cellsPerPageMax : $cellsRemaining;
		$pageWidths[$i] = rand( $offersGrid->cellsPerPageMin, $max );
		$cellsRemaining -= $pageWidths[$i];
	}

}
*/

$cellsRemaining = $offersGrid->cellsPerRow;

?>

<? foreach( $offerPages as $offerPage ): ?>
	<?
	$pagesInCurrentRow++;

	//non-last page in a row
	if( $pagesInCurrentRow < $offersGrid->pagesPerRow ){
		$max = $cellsRemaining > $offersGrid->cellsPerPageMax? $offersGrid->cellsPerPageMax : $cellsRemaining;
		//$max = $offersGrid->cellsPerPageMax;
		$pageWidth = rand( $offersGrid->cellsPerPageMin, $max );
		$cellsRemaining -= $pageWidth;
	}
	//last page in a row
	else{
		$pageWidth = $cellsRemaining;
		$pageWidth = $pageWidth >= $offersGrid->cellsPerPageMin? $pageWidth : $offersGrid->cellsPerPageMin;
		$pageWidth = $pageWidth <= $offersGrid->cellsPerPageMax? $pageWidth : $offersGrid->cellsPerPageMax;
		$pagesInCurrentRow = 0;
		$cellsRemaining = $offersGrid->cellsPerRow;
	}

	$image = $offerPage->image->url? : '/site/assets/files/images/defaults/medical-service.jpg';

	$discountPage = $pages->findOne("template=discount, date_start<$now, date_end>$now, offers.id={$offerPage->id}, discount|price>0, sort=discount");
	$discountedPrice = $discountPage->discount? round( $offerPage->price * (1-$discountPage->discount/100), 2 ) : false;

	?>
	<div class="grid-offer grid-offer-<?=$pageWidth?>">
		<div class="padded grid-offer-inner">
		<div class="card">
			<a href="<?=$offerPage->url?>" >
				<!--
				<div class="offer-image">
					<img data-src="<?=$image?>" data-aspect="3:2">
				</div>
				-->
				<div class="padded offer-title">
					<!--
					<h5>
						<?=$offerPage->title?>
					</h5>
					-->
						pagesInCurrentRow:<?=$pagesInCurrentRow?><br>
						pageWidth :<?=$pageWidth?><br>
						cellsRemaining: <?=$cellsRemaining?><br>
				</div>
				<!--
				<div class="XL padded offer-price">
					<? if($offerPage->price) include '_shared/price.php' ?>
				</div>
				-->
			</a>
		</div>
	</div>
	</div>
<? endforeach ?>