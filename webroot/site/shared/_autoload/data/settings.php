<?
/*
Settings
global wire() is ised to include this file from hooks etc.
Fields declaring a default 'value' in their settings file fall back to it
when the value was never saved or is empty (SettingsFactory itself ignores defaults).
AT
04.08.23
*/

namespace ProcessWire;

$settingsFactory = wire('modules')->get("SettingsFactory");
$settings = (Object)[];

$settingsPages = wire('pages')->find("template=admin,process=ProcessSettingsFactory,check_access=0");

foreach( $settingsPages as $settingsPage ){
	$name = $settingsPage->name;
	$pageSettings = $settingsFactory->getSettings($name) ?: new WireData();

	// defaults are read from the settings file: the module's own copy is overwritten by saved config
	$settingsFile = wire('config')->paths->templates . $settingsPage->sp_filepath;
	if( $settingsPage->sp_filepath && pathinfo($settingsFile, PATHINFO_EXTENSION)=='php' && is_file($settingsFile) )
		foreach( $settingsFactory->extractInputfields(wireRenderFile($settingsFile)) as $inputfield ){
			$default = $inputfield->attr('value');
			if( !$inputfield->name || $inputfield instanceof InputfieldCheckbox || $default===null || $default==='' || is_array($default) ) continue;
			$value = $pageSettings->get($inputfield->name);
			if( $value===null || $value==='' ) $pageSettings->set($inputfield->name, $default);
		}

	$settings->{$name} = $pageSettings;
}
