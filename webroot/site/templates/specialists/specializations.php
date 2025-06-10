<?
/*
Specializations menu
✕
AT
03.11.24
*/

//echo '$branchPage: ', var_dump($branchPage);//
$selector = "template=specialization,sort=title";
//home
if( $branchPage->id == 1) $selector = "has_parent=/specializations," . $selector;

$specializationPages = $branchPage->find($selector);

?>
<div role="specializations" class="half-padded card">

	<? if( $input->urlSegments[1] ): ?>
		<a href="<?=$page->url?>">
			<strong>
				все специализации
			</strong>
		</a>
	<? endif ?>

	<? foreach( $specializationPages as $specializationPage ): ?>
		<?

//echo "{$specializationPage->title}: ", var_dump( $branchPage->find("template=specialist,specializations.id={$specializationPage->id}") );//

		$specialistsSelector = [];
		$specialistsSelector[] = "has_parent={$branchPage->id}";
		//home
		if( $branchPage->id == 1) $specialistsSelector[] = "has_parent=/doctors";
		$specialistsSelector[] = 'template=specialist';
		$specialistsSelector[] = "specializations.id={$specializationPage->id}";
		$specialistsSelector = implode( ',', $specialistsSelector );

		$specialistsCount = $branchPage->find($specialistsSelector)->count();
		if( $specialistsCount == 0 ) continue;

		$isCurrentSpecialization = ( $input->urlSegments[1]==$specializationPage->name );
		?>

		<? if(!$isCurrentSpecialization): ?>
			<a href="<?=$branchPage->url?>doctors/<?=$specializationPage->name?>/">
		<? else: ?>
			<b>
		<? endif ?>
			<?//=$specializationPage->title_plural? : $specializationPage->title?>
			<?=$specializationPage->title?>
			<span class="p1 comment">
				<?=$specialistsCount?>
			</span>
		<? if(!$isCurrentSpecialization): ?>
			</a>
		<? else: ?>
			</b>
		<? endif ?>
	<? endforeach ?>
</div>
