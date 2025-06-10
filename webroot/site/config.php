<?php namespace ProcessWire;

/**
 * ProcessWire Configuration File
 *
 * Site-specific configuration for ProcessWire
 *
 * Please see the file /wire/config.php which contains all configuration options you may
 * specify here. Simply copy any of the configuration options from that file and paste
 * them into this file in order to modify them.
 * 
 * SECURITY NOTICE
 * In non-dedicated environments, you should lock down the permissions of this file so
 * that it cannot be seen by other users on the system. For more information, please
 * see the config.php section at: https://processwire.com/docs/security/file-permissions/
 * 
 * This file is licensed under the MIT license
 * https://processwire.com/about/license/mit/
 *
 * ProcessWire 3.x, Copyright 2016 by Ryan Cramer
 * https://processwire.com
 *
 */

if(!defined("PROCESSWIRE")) die();

/*** SITE CONFIG *************************************************************************/

/** @var Config $config */

/**
 * Enable debug mode?
 *
 * Debug mode causes additional info to appear for use during dev and debugging.
 * This is almost always recommended for sites in development. However, you should
 * always have this disabled for live/production sites.
 *
 * @var bool
 *
 */
$config->debug = false;
//$config->debug = true;


/*** INSTALLER CONFIG ********************************************************************/

/**
	* Installer: Database Configuration
 * 
 */

//$config->dbHost = 'localhost';
//openserver6
$config->dbHost = 'MariaDB-11.7';
$config->dbPort = '3306';

$config->dbUser = 'mysql';
$config->dbPass = 'mysql';
$config->dbName = 'onmed';

if(!strstr(PHP_OS,'WIN')){
	/*
	//staging
	$config->dbUser = 'x92564o8_01';
	$config->dbPass = 'yRrG4S&M';
	$config->dbName = 'x92564o8_01';
	*/

	//production
	$config->dbUser = 'i92588et_onmed2';
	$config->dbPass = 'CcUl%k8C';
	$config->dbName = 'i92588et_onmed2';
}

$config->prependTemplateFile = '_shared/__prepend.php';

/**
 * Installer: User Authentication Salt 
 * 
 * Must be retained if you migrate your site from one server to another
 * 
 */
$config->userAuthSalt = '670daa7203ab472539f57230e2df78c7'; 

/**
 * Installer: File Permission Configuration
 * 
 */
$config->chmodDir = '0755'; // permission for directories created by ProcessWire
$config->chmodFile = '0644'; // permission for files created by ProcessWire 

/**
 * Installer: Time zone setting
 * 
 */
$config->timezone = 'Europe/Moscow';

/**
 * Installer: Admin theme
 * 
 */
$config->defaultAdminTheme = 'AdminThemeUikit';

/**
 * Installer: Unix timestamp of date/time installed
 * 
 * This is used to detect which when certain behaviors must be backwards compatible.
 * Please leave this value as-is.
 * 
 */
 
$config->usePoweredBy = false;
$config->advanced = true;
$config->moduleInstall('directory', true);
 
//AT: no DB cache to prevent DB freezing and some SQL errors
$config->dbCache = false;

//AT: session
$config->sessionExpireSeconds = 365 * 86400; //one year

$config->HannaCodeEdit = true;

//AT: DOCUMENT_ROOT global constant
include __DIR__.'/document_root.php';