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
	
	foreach($_REQUEST as $field => $value) {
		if (!empty($value)||strlen(trim($value))!=0)
			$data[$field] = $value;
	}

	$GLOBALS['io'] = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE);
	
	$states = signedProcesses::getInstance()->getRequestStatesArray();
	$fields = signedProcesses::getInstance()->getFieldsArray();
	$identifications = signedProcesses::getInstance()->getIdentificationsArray();
	$signatures = signedProcesses::getInstance()->getSignatures();
	
	if (signedAPI::getInstance()->verifyAPIFields(basename(dirname(__FILE__)), $data)==true) {
		$servicekey = signedSecurity::getInstance()->extractServiceKey($data['code'], $data['certificate'], $data['verification-key']);
		if (signedSecurity::getInstance()->getHostCode()==$servicekey) {
			if ($signature = signedCiphers::getInstance()->getSignature($data['serial-number'], $data['code'], $data['certificate'], $data['any-name'], $data['any-email'], $data['any-date'], true)) {
				$request = $GLOBALS['io']->load(_PATH_PATHWAYS_REQUEST, $signature['serial-number']);
				$type = $signature['signature']['type'];
				if ($request['sent']!=0 || $request['sent']<time()) {
					if (function_exists('http_response_code'))
						http_response_code(400);
					echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'The corresponding request has been sent to the client, this means until it is resolved you will be not be able to request anymore changes to their signature!', 'error-code' => '302'));
					exit(0);
				}	
				if (!in_array($data['type-key'], $types = array_merge(array_key($identifications[$type]), array_key($signatures)))) {
					if (function_exists('http_response_code'))
						http_response_code(400);
					echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'The corresponding \'type-key\' is not found ~ the only available options for this signature are: '.implode(', ', $types) . '!', 'error-code' => '303'));
					exit(0);
				}
				if (!in_array($data['request-code'], array_key($states))) {
					if (function_exists('http_response_code'))
						http_response_code(400);
					echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'The corresponding \'request-code\' is not found ~ the only available options for this signature are: '.implode(', ', array_key($states)) . '!', 'error-code' => '304'));
					exit(0);
				}
				if (in_array($data['type-key'], array_key($identifications[$type]))) 
				{
					$clause = 'identification';
				} else {
					$clause = $data['type-key'];
				}
				foreach($data['fields'] as $key => $field) {
					if (!in_array($field, array_key($fields[$clause]))) {
						unset($data['fields'][$key]);
					}
				}
				foreach($data['fields'] as $key => $field) {
					if (in_array($data['type-key'], array_key($identifications[$type])))
					{
						$request['request'][$type][$clause][$data['type-key']][$field] = $data['request-code'];
					} else {
						$request['request'][$type][$clause][$field] = $data['request-code'];
					}
				}
				if (in_array($data['type-key'], array_key($identifications[$type])))
				{
					$request['fields'][$data['type-key']] = array_keys($request['request'][$type][$clause][$data['type-key']][$field]);
				} else {
					$request['fields'][$clause] = array_keys($request['request'][$type][$clause][$field]);
				}
				if (isset($data['callback-url'])) {
					$request['callback']['action'] = true;
					$request['callback'][md5($data['callback-url'])]['url'] = $data['callback-url'];
					$request['callback'][md5($data['callback-url'])]['fields']['signature-package'] = $data['signature-package-field'];
					$request['callback'][md5($data['callback-url'])]['fields']['request-rejected'] = $data['request-rejected-field'];
					$request['callback'][md5($data['callback-url'])]['fields']['signature-updated'] = $data['signature-updated-field'];
				} else {
					if (!isset($request['callback']['action']))
						$request['callback']['action'] = false;
				}
				
				$uniqueid = (isset($data['polling-unique-id'])&&!empty($data['polling-unique-id'])?$data['polling-unique-id']:'TM-'.time()) . "--" . (count(array_keys($request['client'])) + 1);
				if (in_array($uniqueid, array_keys($request['client'])))
					$uniqueid .= ':--' . str_replace('.', '-', microtime(true));
				if (isset($data['client-name']))
					$request['calling'][$uniqueid]['client']['name'] = $data['client-name'];
				if (isset($data['client-uname']))
					$request['calling'][$uniqueid]['client']['uname'] = $data['client-uname'];
				if (isset($data['client-email']))
					$request['calling'][$uniqueid]['client']['email'] = $data['client-email'];
				
				if (isset($data['client-site-name']))
					$request['calling'][$uniqueid]['client']['sitename'] = $data['site-name'];
				if (isset($data['client-site-uri']))
					$request['calling'][$uniqueid]['client']['uri'] = $data['site-uri'];
				$request['calling'][$uniqueid]['client']['netbios'] = gethostbyaddr(signedSecurity::getInstance()->getIP(true));
				$request['calling'][$uniqueid]['client']['ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".signedSecurity::getInstance()->getIP(true)."/json.api"), true);
				$request['calling'][$uniqueid]['instance'] = $_SESSION["signed"]['instance']['number'];
				$request['calling-ids'] = array_keys($request['calling']);
				
				$GLOBALS['io']->save($request, _PATH_PATHWAYS_REQUEST, $signature['serial-number']);
				
				if (function_exists('http_response_code'))
					http_response_code(200);
				echo $GLOBALS['api']->format(array('success'=> true, 'queued-requests' => $request['request'], 'sending-request' => date('Y-m-d H:i:s', $request['reminder']), 'when' => time()));
				@$GLOBALS['logger']->logPolling('default', basename(dirname(__FILE__)), array('server' => $_SERVER, 'request' => $_REQUEST));
				exit(0);
				
			} else {
				if (function_exists('http_response_code'))
					http_response_code(400);
				echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'The corresponding field(s):  '.implode(', ', array('serial-number', 'code', 'certificate')) . ' ~ did not correspond with the same signature or was wrong!', 'error-code' => '104'));
				exit(0);
			}
		} else {
			foreach(signedProcesses::getInstance()->getSites() as $key => $srv) {
				if ($srv['code'] == $servicekey) {
					$service = $srv;
					continue;
				}
			}
				
			if (isset($service)) {
				if (!$ch = curl_init($url = $service['protocol'] . '://' . $service['api-uri'] . '/' . basename(dirname(__FILE__)) . '/')) {
					trigger_error('Could not intialise CURL file: '.$url);
					return false;
				}
				$cookies = _PATH_CACHE.'/api-'.md5($url).'.cookie';
			
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 190);
				curl_setopt($ch, CURLOPT_TIMEOUT, 190);
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
				$data = curl_exec($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
				if (function_exists('http_response_code'))
					http_response_code($info['http_code']);
				echo $data;
				exit(0);
			} else {
				if (function_exists('http_response_code'))
					http_response_code(400);
				echo $GLOBALS['api']->format(array('success'=> false, 'error'=> 'Service Key:~  '.$servicekey.' is unknown and not a trusted ensignator!', 'error-code' => '115'));
				exit(0);
			}
		}
	}
?>