<?php 
/**
 * Chronolabs Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license			General Software Licence (http://labs.coop/briefs/legal/general-software-license/10,3.html)
 * @license			End User License (http://labs.coop/briefs/legal/end-user-license/11,3.html)
 * @license			Privacy and Mooching Policy (http://labs.coop/briefs/legal/privacy-and-mooching-policy/22,3.html)
 * @license			General Public Licence 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @category		signed
 * @since			2.1.9
 * @version			2.2.0
 * @author			Simon Antony Roberts (Aus Passport: M8747409) <wishcraft@users.sourceforge.net>
 * @author          Simon Antony Roberts (Aus Passport: M8747409) <wishcraft@users.sourceforge.net>
 * @subpackage		module
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */
	
	if (!isset($GLOBALS['signedBoot']))
		$GLOBALS['signedBoot'] = microtime(true);
	//echo basename(__DIR__) . DIRECTORY_SEPARATOR . basename(__FILE__) . "::" .__LINE__ . '<br/>';
	if (is_file(dirname(dirname(dirname(__DIR__))) . '/mainfile.php'))
		require_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
	else
		die('missing: ' .dirname(dirname(dirname(__DIR__))) . '/mainfile.php');
	//echo basename(__DIR__) . DIRECTORY_SEPARATOR . basename(__FILE__) . "::" .__LINE__ . '<br/>';
	header('Origin: *');
	header('Access-Control-Allow-Origin: *');
	
	if (!isset($_SESSION["signed"]['configurations']) || empty($_SESSION["signed"]['configurations']))
	{
		$module_handler = xoops_gethandler('module');
		$config_handler = xoops_gethandler('config');
		$_SESSION["signed"]['module'] = $module_handler->getByDirname('signed');
		if (is_object($_SESSION["signed"]['module']))
			$_SESSION["signed"]['configurations'] = $config_handler->getConfigList($_SESSION["signed"]['module']->getVar('mid'));
		else 
			return false;
	}
	
	include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.php';
	
	ini_set('memory_limit', $_SESSION["signed"]['configurations']['memory_limits'].'M');
	ini_set('log_errors', _SIGNED_ERRORS_LOGGED);
	ini_set('display_errors', _SIGNED_ERRORS_DISPLAYED);
	error_reporting(_SIGNED_ERRORS_REPORTING);
	
	$GLOBALS['eventHandler'] = xoops_getmodulehandler('events', 'signed');
	
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.php';
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configs.php';
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';
	
	if (constant('_SIGNED_USE_SSL')==true && !defined('_SIGNED_RUNNING_API') && empty($_SERVER['HTTPS']) && !defined('_SIGNED_CRON_EXECUTING')) {
		if (!headers_sent($line, $file))
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		else
			redirect_header('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],0, _SIGNED_MI_REDIRECT_HEADERSENT);
		exit(0);
	}
	
	if (function_exists('date_default_timezone_set'))
		date_default_timezone_set(_PHP_TIMEZONE);
	
	/**
	 * Set System Contextualisations for Service Discovery
	 */
	if (constant("_SIGNED_DISCOVERABLE")==true && !headers_sent($line, $file)) {
		header('Signed-Version: ' . _SIGNED_VERSION);
		header('Signed-Type: ' . _SIGNED_SYSTEM_TYPE);
		header('Signed-Key: ' . _SIGNED_SYSTEM_KEY);
		header('Signed-Time: ' . microtime(true));
		header('Signed-Timezone: ' . _PHP_TIMEZONE);
		header('Signed-Host-Code: ' . signedSecurity::getInstance()->getHostCode());
		header('Signed-Host-Name: ' . strtolower($_SERVER["HTTP_HOST"]));
		header('Signed-Your-IP: ' . signedSecurity::getInstance()->getIP(true));
		header('Signed-Https: ' . (constant("_SIGNED_USE_SSL")==true?"Enforced":"Open"));
		if (constant("_SIGNED_API_ENABLED")==true) {
			header('Signed-API-Enabled: Yes');
			header('Signed-API-Version: ' . _SIGNED_API_VERSION);
			header('Signed-API-Path: ' . _URL_API);
		} else {
			header('Signed-API-Enabled: No');
		}
		header('Signed-Fingerprint: ' . sha1(strtolower($_SERVER["HTTP_HOST"])._PHP_TIMEZONE._SIGNED_VERSION._SIGNED_SYSTEM_TYPE._SIGNED_SYSTEM_KEY._URL_API.date('Y-m-d H')));
	}
	
	/**
	 * Signature Mode
	 */
	if ((isset($_POST['signature_mode'])||isset($_GET['signature_mode'])) && !defined('_SIGNATURE_MODE')) {
		$_SESSION["signed"]['signature_mode'] = $_REQUEST['signature_mode']; 
	} elseif (defined('_SIGNATURE_MODE')) {
		$_SESSION["signed"]['signature_mode'] = constant('_SIGNATURE_MODE');
	}
	
	if (isset($_SESSION["signed"]['signature_mode']) && !defined('_SIGNATURE_MODE')) {
		define('_SIGNATURE_MODE', $_SESSION["signed"]['signature_mode']);
	}
	
	/**
	 * Sets Multilingual Settings
	 */
	$languages = signedProcesses::getInstance()->getLanguages();
	$languageskeys = array_keys($languages);
	if (isset($_GET['language'])) {
		if (in_array($_GET['language'], $languageskeys))
			$_SESSION["signed"]['language-set'] = $_GET['language'];
	} 
	if (isset($_SESSION["signed"]['language-set']) && !defined('_SIGNED_CONFIG_LANGUAGE')) {
		define('_SIGNED_CONFIG_LANGUAGE', $_SESSION["signed"]['language-set']);
	}
	if (!defined('_SIGNED_CONFIG_LANGUAGE'))
		define('_SIGNED_CONFIG_LANGUAGE', $languageskeys[0]);
	if (!defined('_CHARSET'))
		define('_CHARSET', $languages[constant("_SIGNED_CONFIG_LANGUAGE")]['charset']);
	if (!defined('_LANGCODE'))
		define('_LANGCODE', $languages[constant("_SIGNED_CONFIG_LANGUAGE")]['langcode']);
	
	/**
	 * Language Constants
	 */
	require_once dirname(__FILE__) . _DS_ . 'language.php';
	
	/**
	 * Clear Session Unlinks
	 */
	if (isset($_SESSION["signed"]['unlink']) && !empty($_SESSION["signed"]['unlink'])) {
		foreach(array_reverse(array_keys($_SESSION["signed"]['unlink'])) as $key) {
			if (file_exists($_SESSION["signed"]['unlink'][$key]))
				unlink($_SESSION["signed"]['unlink'][$key]);
			unset($_SESSION["signed"]['unlink'][$key]);
		}
	}
	
?>
