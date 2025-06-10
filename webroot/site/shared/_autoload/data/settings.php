<?
/*
Settings
global wire() is ised to include this file from hooks etc.
AT
04.08.23
*/

namespace ProcessWire;

$settingsFactory = wire('modules')->get("SettingsFactory");
$settings = (Object)[];

$settingsPages = wire('pages')->find("template=admin,process=ProcessSettingsFactory,check_access=0");

foreach( $settingsPages as $settingsPage )
	$settings->{$settingsPage->name} = $settingsFactory->getSettings($settingsPage->name);