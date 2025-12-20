<?
/*
Header menu
AT
28.08.25
*/

$branchSpecificTemplates = [
	'discounts',
	'specialists',
	'prices',
	'offers',
	'news',
	'feedbacks',
];

$deprecatedTemplates = [
	'branch',
	'specializations',
];

$shouldHaveChildrenTemplates = [
	'prices',
	'contacts',
];

//

$selector = 'template!=' . implode( '|', $deprecatedTemplates );
$menuPages = $homePage->children($selector);
//$menuPages->prepend($homePage); 
//$menuPages->append( $pages->find('in_main_menu=1') ); 

//echo '$menuPages: ', var_dump($menuPages);//

?>

<nav id="header2-menu" class="w100 flex flex-middle flex-between">
	<? foreach($menuPages as $menuPage): ?>
		<?
		//$rootParent = $menuPage->rootParent;
		switch( true ){
			case in_array( $menuPage->template->name, $branchSpecificTemplates ):
				$menuPage = $branchPage->get("template={$menuPage->template->name}");
			break;
		}

		if( !$menuPage->id ) continue;

		//if( in_array($menuPage->template->name, $shouldHaveChildrenTemplates) && count($menuPage->children)==0 ) continue;

		$url = $menuPage->page_? $menuPage->page_->url : $menuPage->url;

		$class = $menuPage === $page?  'current' : '';
		$class .= $menuPage === $page->rootParent? ' parent' : '';
		?>
		<a
			href='<?=$url?>'
			class='<?=$class?>'
		>
			<?=$menuPage->title?>
		</a>
	<? endforeach ?>
</nav>