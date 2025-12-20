<?
/*
Yandex YML service
https://yandex.ru/support/webmaster/ru/search-appearance/doctors.html

<service id="service_1">
  <name>Первичный приём</name>
  <gov_id>A01.07.001</gov_id>
  <description>Первичный приём стоматолога-хирурга, диагностика возможности и степени хирургического вмешательства</description>
  <internal_id>123</internal_id>
</service>

AT
02.09.25
*/

if( !$servicePage->code && mb_strpos( $servicePage->title, '(' ) ){
	$parts = explode( '(', $servicePage->title );
	$code = array_pop($parts);
	$code = str_replace( ')', '', $code );
	$code = trim($code);
	$servicePage->code = $code;
}

?>

<service id="service_<?=$servicePage->id?>">
	<name><?=$servicePage->title?></name>
	<? if($servicePage->code): ?>
		<gov_id><?=$servicePage->code?></gov_id>
	<? endif ?>
	<description><?=$servicePage->comment? : $servicePage->title?></description>
	<internal_id><?=$servicePage->id?></internal_id>
</service>