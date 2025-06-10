<?
/*
AT
15.12.23
*/

namespace ProcessWire;

$settingsFactory = $modules->get("SettingsFactory");
$contacts = $settingsFactory->getSettings('contacts');

//echo '$contacts: ', var_dump($contacts);
?>
<a href="tel:<?=$contacts->phone?>">
	<?=$contacts->phone?>
</a>