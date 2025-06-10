<?
/*
Programs template
AT
20.11.24
*/

$css[] = '/site/assets/css/programs.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>
<? include '_shared/title.php' ?>

<div class="half-padded flex container">
	<?//=$page->body?>
	<div class="flex flex-gap flex-center">
		<? include 'programs/programs.php' ?>
	</div>
</div>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>