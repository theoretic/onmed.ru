<?
/*
Yandex YML offer
https://yandex.ru/support/webmaster/ru/search-appearance/doctors.html

<offer id="offer_1">
  <url>https://example/doctors/2246852315/checkout/123</url>
  <online_schedule>true</online_schedule> 
  <appointment>true</appointment>
  <price>
    <base_price>5200</base_price>
    <currency>RUR</currency>
    <discount name="Клубная карта">4000</discount>
    <free_appointment>При условии дальнейшего лечения</free_appointment>
  </price>
  <service id="service_1"/>
  <clinic id="clinic_1">
    <doctor id="doctor_1">
      <speciality>стоматолог-хирург</speciality>
      <children_appointment>true</children_appointment>
      <adult_appointment>true</adult_appointment>
      <house_call>true</house_call>
      <telemed>true</telemed>
      <is_base_service>true</is_base_service>
    </doctor>
  </clinic>
</offer>
<offer id="offer_2">
  <url>https://example/doctors/2246852315/checkout/124</url>
  <oms>true</oms>
  <online_schedule>true</online_schedule> 
  <appointment>true</appointment>
  <service id="service_2"/>
  <clinic id="clinic_1">
    <doctor id="doctor_1">
      <speciality>стоматолог-хирург</speciality>
      <children_appointment>true</children_appointment>
      <adult_appointment>true</adult_appointment>
      <house_call>false</house_call>
      <telemed>false</telemed>
      <is_base_service>false</is_base_service>
    </doctor>
  </clinic>
</offer>

Обязательные элементы: oms,price,service,speciality

AT
02.09.25
*/

//looking for minimal price
$minPriceServiceItem = $offerPage->prices->sort('price')->first();
$specialistPage = $pages->get("template=specialist,offers.id={$offerPage->id}");
$speciality = false;
if( $specialistPage->specializations )
	$speciality = $specialistPage->specializations->first()->title;

if(!$speciality) $speciality = 'терапевт';//tmp!

if( !$minPriceServiceItem ) return;

?>

<offer id="offer_<?=$offerPage->id?>">
	<url>https://<?=$_SERVER['HTTP_HOST']?><?=$offerPage->path?></url>
	<oms>true</oms>
	<online_schedule>true</online_schedule> 
	<appointment>true</appointment>
	<? if($minPriceServiceItem->price): ?>
		<price>
			<base_price><?=$minPriceServiceItem->price?></base_price>
			<currency>RUR</currency>
			<!--
			<discount name="Клубная карта">4000</discount>
			<free_appointment>При условии дальнейшего лечения</free_appointment>
			-->
		</price>
	<? endif ?>
	<? if($minPriceServiceItem): ?>
		<service id="service_<?=$minPriceServiceItem->id?>"/>
	<? endif ?>
	<clinic id="clinic_1">
		<doctor id="doctor_<?=$specialistPage->id?>">
			<? if($speciality): ?>
				<speciality><?=$speciality?></speciality>
			<? endif ?>
			<children_appointment>true</children_appointment>
			<adult_appointment>true</adult_appointment>
			<house_call>true</house_call>
			<telemed>true</telemed>
			<is_base_service>true</is_base_service>
		</doctor>
	</clinic>
</offer>