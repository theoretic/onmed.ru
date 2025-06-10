<?
/*
Specialists template
AT
03.11.24
*/

//include_once '_shared/functions/Field.php';

//echo '$branchPage: ', var_dump($branchPage);//

$specialistsSelector = [];
$specialistsSelector[] = "has_parent={$branchPage->id}";
//home
if( $branchPage->id == 1) $specialistsSelector[] = "has_parent=/doctors";
$specialistsSelector[] = "template=specialist";

if( $input->urlSegments[1] ){

//echo "template=specialization,name={$input->urlSegments[1]}";

	//looking for the specialization
	$specializationPageCandidate = $branchPage->findOne("template=specialization,name={$input->urlSegments[1]}");

	if( !$specializationPageCandidate->id ){
		//throw new \ProcessWire\Wire404Exception();
		//$errorPage = $pages->get("/http404");
		//$errorPage->render();
		//die();
		header("HTTP/1.1 404 Not Found");
		readfile( DOCUMENT_ROOT . '/404.html' );
		die();
	}

	//looking for specialists having this specialization
	$specialistsSelector[] = "specializations.id={$specializationPageCandidate->id}";
}

//$specialistsSelector[] = "include=all";
$specialistsSelector[] = "sort=title";
$specialistsSelector = implode( ',', $specialistsSelector );

//echo '$branchPage: ', var_dump($branchPage);//
//echo '$specialistsSelector: ', var_dump($specialistsSelector);//

$specialistPages = $branchPage->find($specialistsSelector);
$noAsideBanners = 1;

//

$css[] = '/site/assets/css/specialists.css';

?>

<? include '_shared/_prolog.php' ?>
<? include '_shared/layout-sidebars/prolog.php' ?>

	<section class="padded container" >
		<? include '_shared/banner.php' ?>
		<? include '_shared/title.php' ?>
		<? include 'specialists/specializations.php' ?>
		<?//=$page->body?>
		<? include 'specialists/specialists.php' ?>
	</section>

<? include '_shared/layout-sidebars/epilog.php' ?>
<? include '_shared/_epilog.php' ?>