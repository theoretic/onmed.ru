<?
/*
News template
AT
12.11.24
*/

if( count($_GET)>0 ){
	//$session->redirect('/http404/'); //not SEO-friendly
	//throw new \ProcessWire\Wire404Exception();
	header("HTTP/1.1 404 Not Found");
	readfile( DOCUMENT_ROOT . '/404.html' );
	die();
}

$css[] = '/site/assets/css/news.css';

?>


<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/banner.php' ?>
<? include '_shared/title.php' ?>

<section class="container">
	<?//=$page->body?>
	<? include 'news/news.php' ?>

	<div class="padded centered">
	<? $regButtonConfig=(Object)['css'=>'simple L margin-l button']; include '_shared/buttons/reg-button.php' ?>
	</div>

</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>