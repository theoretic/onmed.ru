<?
/*
isYandexSpecialization
AT
19.03.25
*/

function isYandexSpecialization($specialization){
	$specializationsFile = "{$_SERVER['DOCUMENT_ROOT']}/site/shared/data/yandex-specializations.csv";
	$specializations = file( $specializationsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
//echo '$specializations: ', var_dump($specializations);//
	$result = in_array( mb_strtolower($specialization), $specializations );
//echo "isYandexSpecialization($specialization): -$result-<br\n>";
	return $result;
}