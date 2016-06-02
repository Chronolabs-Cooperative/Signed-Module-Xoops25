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
 * @subpackage		sms
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

require_once('sip.class.php');

class signedSMSHandlerSip extends SignedSMSController
{

	function __construct($fromNumber = '')
	{
		parent::__construct($fromNumber);
	}
	
	function Send()
	{
		foreach(parent::getToNumbers() as $key=> $number) {
			try
			{
				$api = new signedSIP($_SERVER["SERVER_ADDR"]); // IP we will bind to
				$api->setUsername($_SESSION["signed"]['sip_user']); // authentication username
				$api->setPassword($_SESSION["signed"]['sip_pass']); // authentication password
				$api->setMethod('MESSAGE');
				$api->setTo('sip:'.$_SESSION["signed"]['sip_user'].'@+'.$number);
				$api->setBody(parent::getBody());
				$api->setFrom('sip:'.$_SESSION["signed"]['sip_user'].'@'.$_SESSION["signed"]['sip_server']);
				$api->setUri('sip:'.$_SESSION["signed"]['sip_user'].'@'.$_SESSION["signed"]['sip_server']);
				$res = $api->send();			
			} catch (Exception $e) {
			}
		}
		return $result;
	}
}
