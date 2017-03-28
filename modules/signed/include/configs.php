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

	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'mainfile.php');
	
	//Signee Details
	define('_SIGNED_TITLE', $_SESSION["signed"]['configurations']['title']);
	define('_SIGNED_EMAIL', $_SESSION["signed"]['configurations']['email']);
	
	// SSL Enforcement Constant
	define('_SIGNED_USE_SSL', $_SESSION["signed"]['configurations']['use_ssl']);

	// Verification Settings
	define('_SIGNED_VERIFY_EMAIL', $_SESSION["signed"]['configurations']['verify_email']);
	define('_SIGNED_VERIFY_MOBILE', $_SESSION["signed"]['configurations']['verify_mobile']);

	// Settings for EMAIL
	define('_SIGNED_EMAIL_FROMADDR', $_SESSION["signed"]['configurations']['email_fromaddr']);
	define('_SIGNED_EMAIL_FROMNAME', $_SESSION["signed"]['configurations']['email_fromname']);
	define('_SIGNED_EMAIL_PRIORITY', $_SESSION["signed"]['configurations']['email_priority']); //= low, normal, high
	define('_SIGNED_EMAIL_METHOD', $_SESSION["signed"]['configurations']['email_method']); //= mail, smtp,  sendmail
	define('_SIGNED_EMAIL_SMTP_HOSTNAME', $_SESSION["signed"]['configurations']['email_smtp_host']); //= SMTP Server for smtp method
	define('_SIGNED_EMAIL_SMTP_USERNAME', $_SESSION["signed"]['configurations']['email_smtp_user']); //= SMTP Username for smtp method
	define('_SIGNED_EMAIL_SMTP_PASSWORD', $_SESSION["signed"]['configurations']['email_smtp_pass']); //= SMTP Password for smtp method
	define('_SIGNED_EMAIL_SENDMAIL', $_SESSION["signed"]['configurations']['email_sendmail']); //= Sendmail path
	define('_SIGNED_EMAIL_QUEUED', $_SESSION["signed"]['configurations']['email_queued']);
	
	// Settings for SMS
	define('_SIGNED_SMS_METHOD', $_SESSION["signed"]['configurations']['sms_method']);
	define('_SIGNED_SMS_FROMNUMBER', $_SESSION["signed"]['configurations']['sms_fromnumber']);
	
	// Settings for SMS (Create at cardboardfish.com)
	define('_SIGNED_CARDBOARDFISH_API_URL', $_SESSION["signed"]['configurations']['cardboardfish_uri']);
	define('_SIGNED_CARDBOARDFISH_API_USERNAME', $_SESSION["signed"]['configurations']['cardboardfish_user']);
	define('_SIGNED_CARDBOARDFISH_API_PASSWORD', $_SESSION["signed"]['configurations']['cardboardfish_pass']);	

?>
