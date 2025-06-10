<?
/*
Yandex YML template
https://yandex.ru/support/webmaster/search-appearance/doctors.html
https://cachev2-m9-12.cdn.yandex.net/download.cdn.yandex.net/from/yandex.ru/support/ru/webmaster/files/doctors.xml?lid=221
AT
30.05.25
*/

namespace ProcessWire;

/*
//finding specialists in all branches
$branchPages = $pages->find("template=branch");
$specialistsSelector = "template=specialist,price!=,oldprice!=,specializations.count>0,offers.count>0,specializations.off_yml=";
$specializationPages = new PageArray();
$specialistPages = new PageArray();

foreach( $branchPages as $branchPage ){
	if( strstr($branchPage->name, 'dent') ) continue;
	$doctorsPage = $branchPage->get("name=doctors");
//echo '$branchPage: ', var_dump($branchPage);//
//echo '$doctorsPage: ', var_dump($doctorsPage);//
//echo "\$specialistPages for {$doctorsPage->url}: ", var_dump( $doctorsPage->find($specialistsSelector) ), "<br>\n\n";//
	$specialistPages->add( $doctorsPage->find($specialistsSelector) );
}
*/

//finding specialists in main branch only
$specialistPages = $pages->get("/doctors")->find("template=specialist,specializations.count>0,offers.count>0,price!=,oldprice!=,specializations.off_yml=");

//getting specialists and specializations from non-main branches
foreach( $specialistPages as $i=>$specialistPage ){
	//looking for the same specialist from non-main branch
	$otherBranchesSpecialistPages = $pages->find("id!={$specialistPage->id},title={$specialistPage->title},firstname={$specialistPage->firstname},patronymic={$specialistPage->patronymic}");
	if( count($otherBranchesSpecialistPages)==0 ) continue;

	//temporary modifying the specialist page
	foreach( $otherBranchesSpecialistPages as $otherBranchesSpecialistPage ){
		$specialistPage->specializations->add($otherBranchesSpecialistPage->specializations);
	}

//	$specialistPages->add($otherBranchesSpecialistPages);

}

$specialistPages = $specialistPages->unique();//

//foreach( $specialistPages as $specialistPage ) echo "specialist: {$specialistPage->title} {$specialistPage->firstname} {$specialistPage->id} <br>\n";//
//echo "\$specialistPages: ", var_dump( $specialistPages ), "<br>\n\n";//

//specializations

include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/isYandexSpecialization.php";

foreach( $specialistPages as $specialistPage ){
	foreach( $specialistPage->specializations as $specializationPage ){
		if( !isYandexSpecialization($specializationPage->title) ) continue;
		$specializationPages->add($specializationPage);
	}
}

//echo '$specialistPages: ', var_dump($specialistPages);
//echo '$specializationPages: ', var_dump($specializationPages);

////

header("Content-Type: text/xml");
//header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0");
header("Cache-Control: max-age=0");
header("Pragma: no-cache");

?>

<yml_catalog
	date="<?=date('Y-m-d H:i')?>"
	>
	<shop>
		<name>
			<?=$settings->general->site_name?>
		</name>

		<company>
			<?=$settings->requisites->company_fullname?>
		</company>

		<url>
			https://<?=$_SERVER['HTTP_HOST']?>
		</url>

		<email>
			<?=$settings->contacts->email?>
		</email>

		<picture>
			<?=$settings->general->logo?>
		</picture>
		<description>Каталог врачей</description>

		<currencies>
			<currency id="RUR" rate="1"/>
		</currencies>

		<categories>
			<category id="1">Врач</category>
		</categories>

		<sets>
			<?
			foreach( $specializationPages as $specializationPage ){
				$specialistsCount = $pages->count("template=specialist,specializations={$specializationPage}");
				if( $specialistsCount == 0 ) continue;
				include 'yml/set.php';
			}
			?>
		</sets>

		<offers>
			<?
			foreach( $specialistPages as $specialistPage )
				include 'yml/offer.php';
			?>
		</offers>

	</shop>
</yml_catalog>