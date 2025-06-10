<?
/*
Banner
AT
10.01.24
*/

namespace ProcessWire;

if( !$bannerPage ){
	if( $page->banners )
		$bannerPage = $page->banners->first();
}

?>

<? if( $bannerPage ): ?>
<div class="padded">
	<? if( $bannerPage->page_->url ): ?>
		<a href="<?=$bannerPage->page_->url?>" target=_banner
	<? else: ?>
		<div
	<? endif ?>
		class="banner"
	>

		<? if($bannerPage->image->url): ?>
			<img data-src="<?=$bannerPage->image->url?>" class="adaptive"/>
		<? endif ?>

		<? if( $bannerPage->page_->url ): ?>
			</a>
		<? else: ?>
			</div>
		<? endif ?>
</div>
<? endif ?>