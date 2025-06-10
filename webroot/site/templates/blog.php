<?
/*
Blog template
AT
07.12.21
*/

$css[] = '/site/assets/css/blog.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/title.php' ?>

<section class="container">
	<?//=$page->body?>
	<? include '_shared/posts.php' ?>
</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>