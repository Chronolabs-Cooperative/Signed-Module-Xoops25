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
 * @subpackage		api
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

	// Enables API Runtime Constant
	define('_SIGNED_API_FUNCTION', basename(dirname(__FILE__)));
	define('_SIGNED_EVENT_SYSTEM', 'api');
	define('_SIGNED_EVENT_TYPE', basename(dirname(__FILE__)));
	require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'common.php';
	require dirname(dirname(__FILE__)) . _DS_ . 'validate.php';

	// Checks if API Function is Enabled
	if (!in_array(basename(dirname(__FILE__)), $GLOBALS['api']->callKeys())) {
		header("Location: " . _URL_ROOT);
		exit(0);
	}
	
	if (isset($_GET['language'])&&!empty($_GET['language'])) {
		if (function_exists('http_response_code'))
			http_response_code(200);
		echo $GLOBALS['api']->format(array('success'=> true, 'providers' => signedProcesses::getInstance()->getProvidedArray(), 'when' => time()));
		@$GLOBALS['logger']->logPolling('default', basename(dirname(__FILE__)), array('server' => $_SERVER, 'request' => $_REQUEST));
		exit(0);

	} else {
		if (function_exists('http_response_code'))
			http_response_code(400);
		echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'The corresponding field(s):  '.implode(', ', array('language')) . ' ~ was not specified!', 'error-code' => '135'));
		exit(0);
	}
?>