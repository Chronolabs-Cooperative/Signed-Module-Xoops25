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

class signedSMSHandlerCardboardfish extends SignedSMSController
{

	// When attempted connection timesout for curl
	var $curl_connection_timeout = 35;
	
	// When waiting for response timesouts for curl
	var $curl_timeout = 45;
	
	// curl User Agent
	var $curl_user_agent = 'SMS/1 Digital Signature PHP Curler';
	
	function __construct($fromNumber = '')
	{
		parent::__construct($fromNumber);
	}
	
	function Send()
	{
		$result = array();
		foreach(parent::getToNumbers() as $key=> $number) {
			$params = array();
			$params['S'] = 'H';
			$params['UN'] = constant('_SIGNED_CARDBOARDFISH_API_USERNAME');
			$params['P'] = constant('_SIGNED_CARDBOARDFISH_API_PASSWORD');
			$params['DA'] = $number;
			$params['SA'] = parent::getFromNumber();
			$params['M'] = parent::getBody();
			$params['ST'] = '1';
			$result = $this->curl(constant('_SIGNED_CARDBOARDFISH_API_URL'), $params);
			if (substr(' '.$result['response'], 'OK')>0) {
				$result[$number] = true;
			} else {
				$result[$number] = false;
			}
		}
		return $result;
	}
	
	function curl($url = '', $params = array()) {
		if (substr($url, strlen($url)-1, 1) == "?")
			$url .= http_build_query($params);
		else
			$url .= "?" . http_build_query($params);
		if (!$cc = curl_init($url)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		curl_setopt($cc, CURLOPT_CONNECTTIMEOUT, $this->curl_connection_timeout);
		curl_setopt($cc, CURLOPT_TIMEOUT, $this->curl_timeout);
		curl_setopt($cc, CURLOPT_USERAGENT, $this->curl_user_agent);
		curl_setopt($cc, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cc, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($cc);
		$info = curl_getinfo($cc);
		curl_close($cc);
		return array('response' => $data, 'info' => $info);
	}
}
