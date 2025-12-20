<?
/*
branch banners slider
AT
28.08.25
*/

namespace ProcessWire;

$now = time();
$bannerPages = new PageArray();
$bannerPages->add( $pages->find("in_home_banners=1,sort=sort") );
$bannerPages->add( $pages->find("template=discount,date_start<$now,date_end>$now,sort=sort,limit=5") );

//echo '$bannerPages: ', var_dump($bannerPages);

?>

<? foreach( $bannerPages as $bannerPage ): ?>
		<?
			$current = ++$i == 1? 'current' : '';
			$class = ( $bannerPage->date_start < $now && $bannerPage->date_end > $now)? 'current' : '';
			$for = $bannerPage->discount? 'на' : 'за';
			$backgroundImageUrl = false;
			switch(true){
				case $bannerPage->background->url:
					$backgroundImageUrl = $bannerPage->background->url;
				break;

				default:
					$backgroundImageUrl = $discountsPage->background->url;
			}
		?>

		<a href="<?=$bannerPage->url?>" class="<?=$current?> absolute padded centered discount flex flex-col flex-center flex-middle slide block" data-back="<?=$backgroundImageUrl?>">
			<?
			$bannerTemplateCandidate = "branch/banner/{$bannerPage->template->name}.php";
			if( is_file($bannerTemplateCandidate) ) include $bannerTemplateCandidate;
			?>
		</a>
<? endforeach ?>


<div class="absolute dots">
<? foreach( $bannerPages as $bannerPage ): ?>
	<? $current = ++$i == 1? 'current' : '' ?>
	<a class="<?=$current?> dot"></a>
<? endforeach ?>
</div>

<a class="absolute prev flex flex-middle">
	<? $svgSprite=(Object)['symbol'=>'arrow']; include '_shared/svg-sprite.php' ?>
</a>

<a class="absolute next flex flex-middle">
	<? $svgSprite=(Object)['symbol'=>'arrow']; include '_shared/svg-sprite.php' ?>
</a>

<? $i=0 ?>