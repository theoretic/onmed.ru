<?
/*
home specializations
AT
10.04.19
*/

$specializationPages = $pages->find('template=specialization,sort=title');

?>

<? foreach( $specializationPages as $specializationPage ): ?>
	<a href="<?=$specializationPage->url?>">
		<?=$specializationPage->title?>
	</a>
<? endforeach ?>