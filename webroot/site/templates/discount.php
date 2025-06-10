<?
/*
Discount template
AT
21.11.24
*/

$now = time();
$cssClass = ( $page->date_start < $now && $page->date_end > $now )? 'actual' : '';
$for = $discountPage->discount? 'за' : 'на';

$js[] = '/site/assets/js/form.js';
$css[] = '/site/assets/css/discount.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>

<section class="half-padded container border-box">
<? include '_shared/breadcrumbs.php' ?>
</section>

<section role="discount" class="<?=$cssClass?> half-padded container border-box">
	<div class="centered card">
		<div id="discount-intro"
			<? if( $page->image->url ): ?>
				data-back="<?=$page->image->url?>"
				data-min-height=320
			<? endif ?>
			class="half-padded flex flex-col flex-center flex-middle"
			>
			<? if($page->date_start && $page->date_end): ?>
				<h2 class="dates">
					с <?=Datetime\dateRu($page->date_start)?>
					по <?=Datetime\dateRu($page->date_end)?>
				</h2>
			<? endif ?>
			<h1>
				<?=$page->longtitle? : $page->title?>
			</h1>
		</div>
		<div class="padded centered">

			<? if( $page->discount || $page->price ): ?>
				<h1 id="discount-amount">
					<? if($page->discount): ?>
						-<?=$page->discount?>%
					<? elseif($page->price): ?>
						<? if($page->oldprice): ?>
							<span class="oldprice">
								<?=$page->oldprice?>
							</span>
						<? endif ?>
						<?=$page->price?> ₽
					<? endif ?>
				</h1>
			<? endif ?>

			<? if( count($page->offers)>0 ): ?>
				<h2 class="half-padded"><?=$for?> услуги</h2>
				<? foreach( $page->offers as $offerPage ): ?>
					<a href="<?=$offerPage->url?>" class="XL half-padded">
						<?=$offerPage->title?>
					</a>
				<? endforeach ?>
				<br><br>
			<? endif ?>

			<? if($page->summary): ?>
				<div class="XL">
					<?=$page->summary?>
				</div>
			<? endif ?>

			<? if($page->body): ?>
				<div class="body">
					<?=$page->body?>
				</div>
			<? endif ?>

			<? if($page->specialists !== null): ?>
				<? $specialistPages=$page->specialists; include 'specialists/specialists.php' ?>
			<? endif ?>

			<div class="padded">
				<button
					data-modal="#modal-appointment"
					class="XL"
				>
					Запись на приём
				</button>
			</div>

			<? if( $config->disclaimer !== false ): ?>
				<p class="centered comment">
					<?=$settings->general->discounts?>
				</p>
			<? endif ?>
		</div>
	</div>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/modals/modal-appointment.php' ?>
<? include '_shared/_epilog.php' ?>