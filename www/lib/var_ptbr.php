<?php defined('INDVR') or exit();

/*
    Copyright (c) 2010-2011 Bluecherry, LLC.
    http://www.bluecherrydvr.com / support@bluecherrydvr.com
    Confidential, all rights reserved. No distribution is permitted.
 */

#db
define('VAR_CONF_PATH', '/etc/bluecherry.conf');

define('VAR_ROOT_PATH', '/');

define('VAR_LOG_PATH', '/var/log/bluecherry.log');
define('VAR_WWW_LOG_PATH', '/var/log/apache2/bluecherry-error.log');

define('VAR_NEWS_XML', 'http://www.bluecherrydvr.com/feed/');

define('VAR_SESSION_NAME', 'bluecherrydvr');

define('JS_RELOAD', '<script>window.location = "/";</script>');

#versions
define('VAR_PATH_TO_CURRENT_VERSION', 'http://versioncheck.bluecherrydvr.com/server-version');
define('VAR_PATH_TO_INSTALLED_VERSION', '/usr/share/bluecherry/version');

define('VAR_PATH_TO_IPCAMLIST_VERSION', 'http://versioncheck.bluecherrydvr.com/ipcamtableversion');
define('VAR_PATH_TO_IPCAMLIST_IPCAM', 'http://versioncheck.bluecherrydvr.com/ipcam.csv');
define('VAR_PATH_TO_IPCAMLIST_DRIVER', 'http://versioncheck.bluecherrydvr.com/ipcamdriver.csv');
define('VAR_PATH_TO_IPCAMLIST_PTZ', 'http://versioncheck.bluecherrydvr.com/ipcamptz.csv');

define('VAR_TARRED_LOGS_TMP_LOCATION', '/tmp/bc-logs.tar'); #before logs are downloaded they will be compressed to this file
define('VAR_MYSQLDUMP_TMP_LOCATION', '/tmp/bluecherry-backup.tgz');

define('VAR_PATH_TO_IPCAMLIST_UPDATE', 'http://versioncheck.bluecherrydvr.com/ipcamlistupdate.php');
define('VAR_IPCAMLIST_UPDATE_TOKEN', 'Hiuhg3TnbJl1676T');

define('RM_CLIENT_DOWNLOAD', 'http://www.bluecherrydvr.com/downloads');

define('VAR_LICENSE_AUTH', 'http://keycheck.bluecherrydvr.com');

define('AUDIT_FAILEDLOGIN_ID', 1);
define('AUDIT_USRLOGIN_ID', 2);
define('AUDIT_USRLOGOUT_ID', 3);
define('AUDIT_DEVCONFCHANGED_ID', 4);
define('AUDIT_NEWDEVICE_ID', 5);
define('AUDIT_DEVDISABLED_ID', 6);
define('AUDIT_DEVDELETED_ID', 7);
define('AUDIT_PSWDCHANGE_ID', 8);
define('AUDIT_GENSETTINGSCHANGED_ID', 9);
define('AUDIT_GLOBSCHEDCHANGED_ID', 10);
define('AUDIT_EMAILNOTIFCHANGED_ID', 11);



$varpub = VarPub::get();
$varpub_globals = Array();
#local card props
$GLOBALS['buffer'] = array(
	'post' => array(0,1,2,3,4,5,10,15,20),
	'pre' => array(0,1,2,3,4,6,8,10)
);
$varpub_globals['buffer'] = $GLOBALS['buffer'];


$GLOBALS['local_device_fps'] = array(30, 15, 10, 7.5, 5, 3, 2);
$varpub_globals['local_device_fps'] = $GLOBALS['local_device_fps'];
$GLOBALS['capacity'] = array(
	'tw5864'   => 9999999,
	'generic'  => 9999999,
	'solo6010' => 480,
	'solo6110' => 600,
	'solo6x10' => 480 #backward compatibility
);
$varpub_globals['capacity'] = $GLOBALS['capacity'];

$GLOBALS['resolutions'] = array(
	'NTSC' => array('704x480', '352x240'),
	'PAL'  => array('704x576', '352x288')
);
$varpub_globals['resolutions'] = $GLOBALS['resolutions'];

#anaglog ptz properties
$GLOBALS['ptz'] = array(
	'PROTOCOLS' => array('PELCO'),
	'DEFAULT_PATH' => '/dev/ttyS0',
	'BAUD' => array(1200, 2400, 4800, 9600, 19200, 38400),
	'BIT' =>  array(8, 7),
	'STOP_BIT' => array(1, 2),
	'PARITY' => array('None', 'Even', 'Odd')
);
$varpub_globals['ptz'] = $GLOBALS['ptz'];

$GLOBALS['cts_record_durations'] = array(
	'900' => '15 minutos' , 
	'1800' => '30 minutos', 
	'3600' => '1 horas', 
	'7200' => '2 horas', 
	'10800' => '3 horas', 
	'14400' => '4 horas',
	'21600' => '6 horas'
);
$varpub_globals['cts_record_durations'] = $GLOBALS['cts_record_durations'];

$GLOBALS['bandwidth_limits'] = array(
	'64' => '64 kbps',
	'128' => '128 kbps',
	'256' => '256 kbps',
	'384' => '384 kbps',
	'512' => '512 kbps',
	'0' => 'Desligado'
);
$varpub_globals['bandwidth_limits'] = $GLOBALS['bandwidth_limits'];

$varpub->globals = $varpub_globals;