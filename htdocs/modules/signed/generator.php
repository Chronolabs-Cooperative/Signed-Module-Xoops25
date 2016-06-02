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
 * @subpackage		module
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

	define('_SIGNED_EVENT_SYSTEM', 'generator');
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';
	
	if (!defined('_SIGNATURE_MODE')) {
		if (!headers_sent($line, $file))
			header('Location: '. _URL_ROOT);
		else
			redirect_header(_URL_ROOT, 0,  _SIGNED_MI_REDIRECT_HEADERSENT);
		exit(0);
	}
	
	/**
	 * Prompting Sessioning
	 */
	if (!isset($_SESSION["signed"]['prompts'])) {
		$prompts = signedProcesses::getInstance()->getPromptsArray();
		$_SESSION["signed"]['prompts'] = $prompts[constant('_SIGNATURE_MODE')];
	}
	$promptskeys = array_keys($_SESSION["signed"]['prompts']);

	if (!isset($_SESSION["signed"]['action'])) {
		$_SESSION["signed"]['action'] = $promptskeys[0]; 
		$_SESSION["signed"]['stepstogo'] = signedPrompts::getInstance()->getStepsInPrompt($_SESSION["signed"]['action']);
		$_SESSION["signed"]['finished'] = false;
		$_SESSION["signed"]['package'] = array();
		$_SESSION["signed"]['workbook'] = array();
	}

	/**
	 * Inbound Data
	 */
	if (strtoupper($_SERVER["REQUEST_METHOD"])=="POST" && isset($_REQUEST['step'])) {
		if (signedPrompts::getInstance()->goVerifyAndPackagePrompt($_SESSION["signed"]['action'], $_REQUEST['step'])) {
			if (count(signedPrompts::getInstance()->getStepsLeftPrompt($_SESSION["signed"]['action'], $_REQUEST['step']))==0) {
				$next = '';
				$found = false;
				foreach($promptskeys as $prompt) {
					if ($found==true) {
						$_SESSION["signed"]['action'] = $prompt;
						$_SESSION["signed"]['stepstogo'] = signedPrompts::getInstance()->getStepsInPrompt($_SESSION["signed"]['action']);
						$found= false;
						continue;
						continue;
					}
					if ($prompt == $_SESSION["signed"]['action']) {
						unset($_SESSION["signed"]['stepstogo'][$_REQUEST['step']]);
						if (count($_SESSION["signed"]['stepstogo'])==0) {
							$_SESSION["signed"]['action'] = '';
							$found = true;
						} else {
							continue;
						}
					}
				}
				if ($found == true && empty($_SESSION["signed"]['action'])) {
					$_SESSION["signed"]['finished'] = true;
					$_SESSION["signed"]['stepstogo'] = array();
					$_SESSION["signed"]['action'] = $promptskeys[count($promptskeys)-1];
				}		
			}
		}
	}
	define('_SIGNED_EVENT_TYPE', $_SESSION["signed"]['action']);
	
	/**
	 * Buffer Templates to output buffer
	 */
	signedCanvas::getInstance()->getContentBuffer($_SESSION["signed"]['action'], signedPrompts::getInstance()->getNextStepInPrompt($_SESSION["signed"]['action'], $_SESSION["signed"]['stepstogo']));
	
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>