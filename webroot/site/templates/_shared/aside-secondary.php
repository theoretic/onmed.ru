<?
/*
AT
21.05.25
*/

$asideBannerPages = $asideBannerPages? : $pages->find("id!={$page->id},in_aside!=,sort=sort,limit=10");
//echo '$asideBannerPages: ', var_dump($asideBannerPages);//

//pinning the coupon page
//$asideBannerPages->prepend( $pages->get('id!={$page->id},name=coupon,in_aside!=,include=all') );

?>

<? foreach( $asideBannerPages as $asideBannerPage ): ?>
	<div class="padded centered aside-banner">
		<div class="relative centered aside-banner-inner card block">
			<a href=<?=$asideBannerPage->url?>>
				<? if($asideBannerPage->image->url): ?>
					<div class="aside-banner-image">
						<img data-src="<?=$asideBannerPage->image->url?>">
					</div>
				<? else: ?>
					<br>
					<br>
				<? endif ?>
				<div class="half-padded aside-banner-texts">
					<h3 class="half-v-padded">
						<?=$asideBannerPage->title?>
					</h3>
					<? if($asideBannerPage->summary): ?>
						<?=$asideBannerPage->summary?>
					<? endif ?>
				</div>
			</a>
			<a href="<?=$asideBannerPage->parent->url?>" class="absolute S aside-banner-label">
				<?=$asideBannerPage->parent->title?>
			</a>
		</div>
	</div>
<? endforeach ?>

