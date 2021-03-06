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
 * @subpackage		class
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */


defined('_PATH_ROOT') or die('Restricted access');

/**
 *
 * @author Simon Roberts <simon@labs.coop>
 *
*/

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedobject.php'; 
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedprocesses.php'; 
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedstorage.php'; 
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedciphers.php'; 
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedlists.php'; 
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'signedcache.php'; 


/**
 *
 * @author Simon Roberts <simon@labs.coop>
 *
 */
class signedPackages extends signedObject 
{

	/**
	 *
	 * @var unknown
	 */
	var $_processes = NULL;

	/**
	 *
	 * @var unknown
	 */
	var $_ciphers = NULL;
	
	
	/**
	 *
	 */
	function __construct()
	{
		
		$this->_processes = signedProcesses::getInstance();
		$this->_ciphers = signedCiphers::getInstance();
	}
	
	/**
	 *
	 */
	function __destruct()
	{
	
	}
	
	/**
	 *
	 * @return Ambigous <NULL, signedProcessess>
	 */
	static function getInstance()
	{
		static $object = NULL;
		if (!is_object($object))
			$object = new signedPackages();
		$object->intialise();
		return $object;
	}
	

	/**
	 * 
	 */
	static function getCachedData($package = array())
	{
		foreach($package as $key => $value)
		{
			if (is_array($value) && !empty($value)) {
				$package[$key] = self::getCachedData($value);
			} elseif (is_string($value) && substr($value, 0, 7) == 'cached:')
			{
				$package[$key] = signedCache::getInstance()->read(str_replace('cached:', 'data_', $value));
				signedCache::getInstance()->delete(str_replace('cached:', 'data_', $value));
			}
		}
		return $package;
	}

	 /*
	  * 
	  */
	function saveEnsigmentPackage($serial = '', $code = '', $certificate = '', $package = array(), $verify = array())
	{
		$data = array();
		$package = self::getCachedData($package);
		$_SESSION["signed"]['signedSignature']['current'] = $serial;
		
		$this->_io = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE);
		
		if (!is_dir(_PATH_CALENDAR_GENERATED . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m')))
			mkdir(_PATH_CALENDAR_GENERATED . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m'), 0777, true);
		
		$ticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_GENERATED . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m')))+1;
		
		$data['pathways']['serials'] = $this->_io->load(_PATH_PATHWAYS_SERIALS, $serial);
		if (isset($_SESSION["signed"]['pathways']['emails']) && !empty($_SESSION["signed"]['pathways']['emails'])) {
			foreach($_SESSION["signed"]['pathways']['emails'] as $key => $data) {
				$data['pathways']['emails'][$key] = $this->_io->load(_PATH_PATHWAYS_EMAILS, $key);
				$data['pathways']['emails'][$key][$serial] = array('pathway' => array('data' => $data,  'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
				$data['pathways']['serials']['emails'][$key] = array('pathway' => array('data' => $data, 'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
			}
		}
		if (isset($_SESSION["signed"]['pathways']['names']) && !empty($_SESSION["signed"]['pathways']['names'])) {
			foreach($_SESSION["signed"]['pathways']['names'] as $key => $data) {
				$data['pathways']['names'][$key] = $this->_io->load(_PATH_PATHWAYS_NAMES, $key);
				$data['pathways']['names'][$key][$serial] = array('pathway' => array('data' => $data,  'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
				$data['pathways']['serials']['names'][$key] = array('pathway' => array('data' => $data, 'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
			}
		}
		if (isset($_SESSION["signed"]['pathways']['dates']) && !empty($_SESSION["signed"]['pathways']['dates'])) {
			foreach($_SESSION["signed"]['pathways']['dates'] as $key => $data) {
				$data['pathways']['dates'][$key] = $this->_io->load(_PATH_PATHWAYS_DATES, $key);
				$data['pathways']['dates'][$key][$serial] = array('pathway' => array('data' => $data,  'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
				$data['pathways']['serials']['dates'][$key] = array('pathway' => array('data' => $data, 'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
			}
		}

		$signaturesHandler = xoops_getmodulehandler('signatures', 'signed');
		$_SESSION["signed"]['signedSignature'][$serial] = $signaturesHandler->create();
		$_SESSION["signed"]['signedSignature'][$serial]->setVar('serial', $serial);
		$_SESSION["signed"]['signedSignature'][$serial]->setVar('type', $package['signature']['type']);
		$_SESSION["signed"]['signedSignature'][$serial]->setVar('name', $package['signature']['personal']['name']);
		$_SESSION["signed"]['signedSignature'][$serial]->setVar('state', 'progress');
		if (isset($package['signature']['entity']['name']))
			$_SESSION["signed"]['signedSignature'][$serial]->setVar('entity', $package['signature']['entity']['name']);
		if ($package['signature']['expiry']['when'] != 'never')
			$_SESSION["signed"]['signedSignature'][$serial]->setVar('expires', $package['signature']['expiry']['when']);
		$_SESSION["signed"]['signedSignature'][$serial] = $signaturesHandler->get($signaturesHandler->insert($_SESSION["signed"]['signedSignature'][$serial]));
		
		$data['pathways']['code'] = array('pathway' => array('data' => $code, 'serial-number' => $serial, 'ticket' => $ticket), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
		$data['pathways']['certificate'] = array('pathway' => array('data' => $certificate, 'serial-number' => $serial, 'ticket' => $ticket), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
		$data['signatures'] = array('resources' => array('code' => $code, 'pathways' => $data['pathways'], 'certificate' => $certificate, 'signature' => $package, 'serial-number' => $serial, 'ticket' => $ticket, 'sent' => 0), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
		$data['verification'] = array('verification' => array('emails' => ($toverify = $this->getVerifiableEmails($package, $verify['verification']['email']['fields'])), 'mobiles' => ($mobileverify = $this->getVerifiableMobiles($package, $verify['verification']['mobile']['fields'])), 'serial-number' => $serial, 'ticket' => $ticket, 'expired' => false, 'verified' => (count($toverify)+count($mobileverify)>0?false:true)), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
		$data['calendar']['generated'] = array('generated' => array('type' => $package['signature']['type'], 'type' => $package['signature']['class'], 'generated' => microtime(true), 'serial-number' => $serial, 'ticket' => $ticket), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
		
		if (isset($package['signature']['expiry']['when']) && $package['signature']['expiry']['when'] != 'never') {
			$data['calendar']['expiry'] = array('expiry' => array('type' => $package['signature']['type'], 'class' => $package['signature']['class'], 'expires' => ($expires = strtotime(date('Y-m-d 0:0:01', $package['signature']['expiry']['when']))), 'serial-number' => $serial), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
			$expiryticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $expires) . DIRECTORY_SEPARATOR . date('m', $expires)))+1;
			foreach($package['identifications'] as $key => $identification) {
				if ($identification['identification']['expires']!='never') {
					$expiriesticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires'])))+1;
					$expiresdata = array('expires' => array('type' => 'identification', 'package' => $identification, 'expires' => $identification['identification']['expires'], 'serial-number' => $serial, 'ticket' => $expiriesticket, 'key' => $key), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
					if (!empty($expiresdata)) {
						$this->_io->save($expiresdata, _PATH_CALENDAR_EXPIRES . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires']), $expiriesticket, _SIGNED_CALENDAR_STORAGE);
					}
				}
			}
		} else {
			foreach($package['identifications'] as $key => $identification) {
				if ($identification['identification']['expires']!='never') {
					$expiriesticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires'])))+1;
					$expiresdata = array('expires' => array('type' => 'identification', 'package' => $identification, 'expires' => $identification['identification']['expires'], 'serial-number' => $serial, 'ticket' => $expiriesticket, 'key' => $key), 'signid' => $_SESSION["signed"]['signedSignature'][$serial]->getVar('signid'));
					if (!empty($expiresdata)) {
						$this->_io->save($expiresdata, _PATH_CALENDAR_EXPIRES . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires']), $expiriesticket, _SIGNED_CALENDAR_STORAGE);
					}
				}
			}
		}

		$this->_io->save($data['signatures'], _PATH_REPO_SIGNATURES, $serial);
		$this->_io->save($data['verification'], _PATH_REPO_VALIDATION, $serial);
		$this->_io->save($data['calendar']['generated'], _PATH_CALENDAR_GENERATED . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m'), $ticket);
		
		if (isset($data['calendar']['expiry'])) {
			$this->_io->save($data['calendar']['expiry'], _PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $expires) . DIRECTORY_SEPARATOR . date('m', $expires), md5(implode("", signedArrays::getInstance()->trimExplode(explode("\n", $certificate)))));
		}
		
		if (isset($data['pathways']['dates']) && !empty($data['pathways']['dates']))
		{
			foreach($data['pathways']['dates'] as $key => $values) {
				$this->_io->save($values, _PATH_PATHWAYS_DATES, $key);
			}
		}
		
		if (isset($data['pathways']['names']) && !empty($data['pathways']['names']))
		{
			foreach($data['pathways']['names'] as $key => $values) {
				$this->_io->save($values, _PATH_PATHWAYS_NAMES, $key);
			}
		}
		
		if (isset($data['pathways']['emails']) && !empty($data['pathways']['emails']))
		{
			foreach($data['pathways']['emails'] as $key => $values) {
				$this->_io->save($values, _PATH_PATHWAYS_NAMES, $key);
			}
		}
		
		$this->_io->save($data['pathways']['serials'], _PATH_PATHWAYS_SERIALS, $serial);
		$this->_io->save($data['pathways']['certificate'], _PATH_PATHWAYS_CERTIFICATES, md5(implode("", signedArrays::getInstance()->trimExplode(explode("\n", $certificate)))));
		$this->_io->save($data['pathways']['code'], _PATH_PATHWAYS_CODES, md5($code));

		if (count($mobileverify)>0 && !empty($mobileverify)) {
			$this->sendSMSMobileValidations($mobileverify, $serial, $package);
		}

		if (count($toverify)>0 && !empty($toverify)) {
			$this->sendEmailAddressValidations($toverify, $serial, $package);
		}

	}
	
	
	/**
	 *
	 */
	function saveEditedEnsigmentPackage($serial = '', $package = array())
	{
		$_SESSION["signed"]['signedSignature']['current'] = $serial;
		$package = self::getCachedData($package);
		$this->_io = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE);
	
		$data['pathways']['serials'] = $this->_io->load(_PATH_PATHWAYS_SERIALS, $serial);
		$serials = array();
	
		foreach(array(	'emails' 	 =>  constant("_PATH_PATHWAYS_EMAILS"),
				'names' 	 =>  constant("_PATH_PATHWAYS_NAMES"),
				'dates' 	 =>  constant("_PATH_PATHWAYS_DATES")) as $state => $path) {
				if (isset($data['pathways']['serials'][$state])) {
					foreach($data['pathways']['serials'][$state] as $key => $values) {
						if (!in_array($key, array_keys($_SESSION["signed"]['pathways'][$state])))
							unset($data['pathways'][$state][$key][$serial]);
						if (isset($_SESSION["signed"]['pathways'][$state]) && !empty($_SESSION["signed"]['pathways'][$state])) {
							foreach($_SESSION["signed"]['pathways'][$state] as $skey => $data) {
								if (!in_array($skey, array_keys($data['pathways']['serials'][$state]))) {
									$data['pathways'][$state][$key] = $this->_io->load($path, $key);
									$data['pathways'][$state][$key][$serial] = array('pathway' => array('data' => $data,  'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
									$serials[$state][$key] = array('pathway' => array('data' => $data, 'key' => $key, 'serial-number' => $serial, 'ticket' => $ticket));
								}
							}
						} else {
							unset($data['pathways'][$state][$key][$serial]);
						}
						$this->_io->save($data['pathways'][$state][$serial][$key], $path, $key);
					}
				} elseif (isset($_SESSION["signed"]['pathways'][$state])) {
					foreach($_SESSION["signed"]['pathways'][$state] as $skey => $data) {
						$data['pathways'][$state][$skey] = $this->_io->load($path, $skey);
						$data['pathways'][$state][$skey][$serial] = array('pathway' => array('data' => $data,  'key' => $skey, 'serial-number' => $serial, 'ticket' => $ticket));
						$serials[$state][$skey] = array('pathway' => array('data' => $data, 'key' => $skey, 'serial-number' => $serial, 'ticket' => $ticket));
						$this->_io->save($data['pathways'][$state][$skey], $path, $skey);
					}
				}
		}
		$this->_io->save($data['pathways']['serials'] = $serials, _PATH_PATHWAYS_SERIALS, $serial);
		$signature = $this->_io->load(_PATH_REPO_SIGNATURES, $serial);
		$signature['resources']['signature'] = $package;
		$signature['resources']['pathways']['serials'] = $data['pathways']['serials'];
		$signature['resources']['pathways']['emails'] = $data['pathways']['emails'];
		$signature['resources']['pathways']['names'] = $data['pathways']['names'];
		$signature['resources']['pathways']['dates'] = $data['pathways']['dates'];
		$signature = $this->_io->save($signature, _PATH_REPO_SIGNATURES, $serial);
			
		if (isset($package['signature']['expiry']['when']) && $package['signature']['expiry']['when'] != 'never') {
			$data['calendar']['expiry'] = array('expiry' => array('type' => $package['signature']['type'], 'class' => $package['signature']['class'], 'expires' => ($expires = strtotime(date('Y-m-d 0:0:01', $package['signature']['expiry']['when']))), 'serial-number' => $serial));
			$expiryticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $expires) . DIRECTORY_SEPARATOR . date('m', $expires)))+1;
			foreach($package['identifications'] as $key => $identification) {
				if ($identification['identification']['expires']!='never') {
					$expiriesticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires'])))+1;
					$expiresdata = array('expires' => array('type' => 'identification', 'package' => $identification, 'expires' => $identification['identification']['expires'], 'serial-number' => $serial, 'ticket' => $expiriesticket, 'key' => $key));
					if (!empty($expiresdata)) {
						$this->_io->save($expiresdata, _PATH_CALENDAR_EXPIRES . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires']), $expiriesticket, _SIGNED_CALENDAR_STORAGE);
					}
				}
			}
		} else {
			foreach($package['identifications'] as $key => $identification) {
				if ($identification['identification']['expires']!='never') {
					$expiriesticket = count(signedLists::getFileListAsArray(_PATH_CALENDAR_EXPIRY . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires'])))+1;
					$expiresdata = array('expires' => array('type' => 'identification', 'package' => $identification, 'expires' => $identification['identification']['expires'], 'serial-number' => $serial, 'ticket' => $expiriesticket, 'key' => $key));
					if (!empty($expiresdata)) {
						$this->_io->save($expiresdata, _PATH_CALENDAR_EXPIRES . DIRECTORY_SEPARATOR . date('Y', $identification['identification']['expires']) . DIRECTORY_SEPARATOR . date('m', $identification['identification']['expires']), $expiriesticket, _SIGNED_CALENDAR_STORAGE);
					}
				}
			}
		}
	
		$verification = $this->_io->load(_PATH_REPO_VALIDATION, $serial);
		// Expire Permantly
		if ($verification['verification']['expired'] == true) {
			$verification['verification']['expired'] == false;
			$this->_io->save($verification, _PATH_REPO_VALIDATION, $serial);
			$this->lodgeCallbackSessions($serial, 'expired');
		}
	}
	
	/**
	 *
	 */
	function lodgeCallbackSessions($serial = '', $type = ''){
		if (file_exists($file = _PATH_REPO_SIGNED . DIRECTORY_SEPARATOR . $serial . '.xml')) {
			$signed = XML2Array::createArray(signedArrays::getFileContents($file));
		} else {
			$signed = array('binded' => array());
		}
		foreach($signed['binded'] as $key => $values) {
			if ($values['callback']['action']==true) {
				switch($type) {
					case 'expire':
						if (!$callbacks = signedCache::read('callback-calls'))
							$callbacks = array();
						$callbacks[] = array('type' => $type, 'destination' => $values['callback'], 'package' =>  array(), 'expired' => true, 'updated' => false);
						signedCache::write('callback-calls', $callbacks, 3600*24*7*4*24);
						break;
					case 'rejected':
					case 'request':
					case 'update':
						if (!$callbacks = signedCache::read('callback-calls'))
							$callbacks = array();
						$resource = XML2Array::createArray(signedArrays::getFileContents(_PATH_REPO_SIGNATURES . DIRECTORY_SEPARATOR . $serial . '.xml'));
						$callbacks[] = array('type' => 'update', 'destination' => $values['callback'], 'package' => $resource['resources']['signature'], 'expired' => false, 'updated' => true);
						signedCache::write('callback-calls', $callbacks, 3600*24*7*4*24);
						break;
				}
			}
		}
		if (isset($_SESSION["signed"]['request']['callback']) && !empty($_SESSION["signed"]['request']['callback']))
		{
			if ($type == 'request' && $_SESSION["signed"]['request']['callback']['action'] == true) {
				if (!$callbacks = signedCache::read('callback-calls'))
					$callbacks = array();
				foreach($_SESSION["signed"]['request']['callback'] as $key => $values) {
					if ($key != 'action') {
						foreach($values as $hash => $callback) {
							$callbacks[] = array('type' => $type, 'destination' => $callback, 'package' =>  $resource['resources']['signature'], 'rejected' => false, 'updated' => true);
						}
					}
				}
				signedCache::write('callback-calls', $callbacks, 3600*24*7*4*24);
			}
		}
	}
		
	/**
	 *
	 */
	function sendSignatureEmail($serial = '') {
	
		if ($this->_io = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE)) {
			$array = $this->_io->load(_PATH_REPO_SIGNATURES, $serial);
			if ($array['resources']['sent'] == false) {
				$array['resources']['sent'] = microtime(true);
	
				$f = fopen(_PATH_UPLOADS . DIRECTORY_SEPARATOR . $serial . '.xrt', 'w');
				fwrite($f, $array['resources']['certificate'], strlen($array['resources']['certificate']));
				fclose($f);
	
				$codeparts = explode("::", $array['resources']['code']);
				$attachments = array(	'signature'	 => 	array(	'source'	 => 	($signature = $this->_io->save($array['resources']['signature']['signature'], _PATH_UPLOADS, $serial, 'signature', _SIGNED_SIGNED_STORAGE)),
						'filename' => str_replace(array(" ", "'"), "-", $codeparts[1].'--'.strtolower($array['resources']['signature']['personal']['name'])) . '.' . _SIGNED_SIGNED_STORAGE),
						'certificate'	 => 	array(	'source'	 => 	($certificate = _PATH_UPLOADS . DIRECTORY_SEPARATOR . $serial . '.xrt'),
								'filename' => str_replace(array(" ", "'"), "-", $codeparts[1].'--'.strtolower($array['resources']['signature']['personal']['name'])).'.xrt'));
	
				$mailer = signed_getMailer();
				$mailer->setTemplateDir(_PATH_ROOT . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . _SIGNED_CONFIG_LANGUAGE  . DIRECTORY_SEPARATOR . 'mail_template');
	
				switch($array['resources']['signature']['signature']['type']) {
					case 'personal':
						$to 	= 	array(	'name'	 => 	$array['resources']['signature']['personal']['name'],
						'email'	 => 	$array['resources']['signature']['personal']['email']);
						break;
					case 'entity':
						$to 	= 	array(	0  => 	array(	'name'	 => 	$array['resources']['signature']['personal']['name'],
						'email'	 => 	$array['resources']['signature']['personal']['email']),
						1  => 	array(	'name'	 => 	$array['resources']['signature']['personal']['name'],
						'email'	 => 	$array['resources']['signature']['entity']['entity-email']));
						break;
				}
	
				$data['SIGNED_CODE'] = $array['resources']['code'];
				$data['SIGNED_CERTIFICATE'] = $array['resources']['certificate'];
				if ($array['resources']['signature']['signature']['expiry']['when']!='never')
					$data['SIGNED_EXPIRED'] = date('Y-m-d H:i:s', $array['resources']['signature']['signature']['expiry']['when']);
				else
					$data['SIGNED_EXPIRED'] = 'Never Expires!';
				$data['SERIAL_NUMBER'] = $serial;
				$data['PERSON_FOR'] = $array['resources']['signature']['signature']['signer']['name'];
				$data['PERSON_BY'] = $array['resources']['signature']['signature']['signee']['name'];
	
				$body = $mailer->getBodyFromTemplate('signature-email', $data, true);
	
				if ($mailer->sendMail($to, $this->_processes->getCcEmails(), $this->_processes->getBccEmails(), 'Digital Signature for '.$array['resources']['signature']['signature']['signer']['name'], $body['body'], $attachments, array(), $body['isHTML'])) {
					$file = $this->_io->save($array, _PATH_REPO_SIGNATURES, $serial);
					signedLogger::getInstance()->logDelivery('default', array('type' => 'email', 'recipients' => $to, 'asset-id' => $GLOBAL['logger']->_last_asset_id, 'data' => $data), array('type' => 'digital signature', 'serial' => $serial));
				}
				if (is_object($_SESSION["signed"]['signedSignature'][$serial]))
				{
					$_SESSION["signed"]['signedSignature']['current'] = $serial;
					$_SESSION["signed"]['signedSignature'][$serial]->setVar('state', 'active');
					$_SESSION["signed"]['signedSignature'][$serial]->setVar('issued', time());
				}
				unlink($signature);
				unlink($cerificate);
				return true;
			}
		}
		return false;
	}
	

	/**
	 *
	 */
	function sendSMSMobileValidations($mobiles = array(), $serial = '', $package = array()){
		foreach($mobiles as $key => $mobile) {
			$smser = signed_getSMSer();
			$smser->setTemplateDir(_PATH_ROOT . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . _SIGNED_CONFIG_LANGUAGE . DIRECTORY_SEPARATOR . 'sms_template');
			$data['SERIAL_NUMBER'] = $serial;
			$data['TO_NUMBER'] = $mobile['number'];
			$data['VERIFY_KEY'] = $mobile['key'];
			$data['PERSON_FOR'] = $package['signature']['signer']['name'];
			$data['PERSON_BY'] = $package['signature']['signee']['name'];
			$data['VERIFY_URL'] = signedCiphers::getInstance()->shortenURL(_URL_ROOT . '/=verifor=/?op=mobile&serial='.$serial.'&key='.$key.'&hash='.$mobile['key']);
			$body = $smser->getBodyFromTemplate('verify-mobile', $data, false);
			if (!$smser->sendSMS($mobile['number'], $body))
			{
				foreach($smser->errors as $id => $error)
					$GLOBALS['errors'][] = $error;
			}
		}
	}
	
	/**
	 *
	 */
	function sendEmailAddressValidations($emails = array(), $serial = '', $package = array()){
		foreach($emails as $key => $email) {
			$mailer = signed_getMailer();
			$mailer->setTemplateDir(_PATH_ROOT . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . _SIGNED_CONFIG_LANGUAGE . DIRECTORY_SEPARATOR . 'mail_template');
			$data['SERIAL_NUMBER'] = $serial;
			$data['TO_EMAIL'] = $email['to'];
			$data['VERIFY_KEY'] = $email['key'];
			$data['PERSON_FOR'] = $package['signature']['signer']['name'];
			$data['PERSON_BY'] = $package['signature']['signee']['name'];
			$data['VERIFY_URL'] = signedCiphers::getInstance()->shortenURL(_URL_ROOT . '/=verifor=/?op=email&serial='.$serial.'&key='.$key.'&hash='.$email['key']);
			$body = $mailer->getBodyFromTemplate('verify-email', $data, true);
			if (!$mailer->sendMail(array('name' => $email['to'], 'email' => $email['to']), array(), array(), 'Digital Signature for '.$package['signature']['signer']['name'], $body['body'], array(), array(), $body['isHTML']))
			{
				foreach($mailer->errors as $id => $error)
					$GLOBALS['errors'][] = $error;
			}
		}
	}
	
	/**
	 *
	 */
	function getVerifiableMobiles($package = array(), $validation = array())
	{
		if (constant('_SIGNED_VERIFY_MOBILE')==false)
			return array();
		$mobiles = array();
		foreach($validation as $field) {
			$number = $this->extractField($package, $field);
			if (!empty($number)) {
				$mobiles[$number]['number'] = $number;
				$mobiles[$number]['key'] = $this->_ciphers->getHash($number.microtime(true),'xcp',9);
				$mobiles[$number]['verified'] = false;
			}
		}
		return $mobiles;
	}
	
	/**
	 *
	 */
	function getVerifiableEmails($package = array(), $validation = array()){
		if (constant('_SIGNED_VERIFY_EMAIL')==false)
			return array();
		$emails = array();
		foreach($validation as $field) {
			$address = $this->extractField($package, $field);
			if (!empty($address)) {
				$emails[$key = str_replace(array('.','@','+'), '-',$address)]['to'] = $address;
				$emails[$key]['key'] = $this->_ciphers->getHash($address.microtime(true),'xcp',9);
				$emails[$key]['verified'] = false;
			}
		}
		return $emails;
	}
	
	/**
	 *
	 */
	function extractField($package = array(), $field = '')
	{
		if (in_array($field, array_keys($package)))
		{
			return $package[$field];
		} else {
			$result = '';
			foreach($package as $key => $values) {
				if (empty($result)) {
					if (is_array($values)) {
						$result = $this->extractField($values, $field);
					}
				}
			}
		}
		return $result;
	}
	

	/**
	 *
	 */
	function sealPackage($type = 'personal') {
		if (isset($_SESSION["signed"]['sealed']))
			return false;
		$_SESSION["signed"]['package']['signature']['type'] = $type;
		$_SESSION["signed"]['package']['signature']['class'] = $_SESSION["signed"]['class'];
		$_SESSION["signed"]['package']['signature']['timezone'] = _PHP_TIMEZONE;
		$_SESSION["signed"]['package']['signature']['made'] = microtime(true);
		$_SESSION["signed"]['package']['signature']['compilation']['language'] = 'PHP';
		$_SESSION["signed"]['package']['signature']['compilation']['version'] = PHP_VERSION;
		$_SESSION["signed"]['package']['signature']['compilation']['id'] = PHP_VERSION_ID;
		$_SESSION["signed"]['package']['signature']['type-set']['code'] = _SIGNED_CONFIG_LANGCODE;
		$_SESSION["signed"]['package']['signature']['type-set']['characters'] = _SIGNED_CONFIG_CHARSET;
		$_SESSION["signed"]['package']['signature']['expiry']['metric'] = $_SESSION["signed"]['expiry'];
		if ($_SESSION["signed"]['expiry']['years']<=0 && $_SESSION["signed"]['expiry']['months']<=0 ) {
			$_SESSION["signed"]['package']['signature']['expiry']['when'] = 'never';
		} else {
			$_SESSION["signed"]['package']['signature']['expiry']['when'] = strtotime(date('Y-m-d 0:0:01', $_SESSION["signed"]['package']['signature']['made'] + (3600*24*7*4*$_SESSION["signed"]['expiry']['months']) + (3600*24*7*4*12*$_SESSION["signed"]['expiry']['years'])));
		}
		$_SESSION["signed"]['package']['signature']['signee']['name'] = _SIGNED_TITLE;
		$_SESSION["signed"]['package']['signature']['signee']['email'] = _SIGNED_EMAIL;
		$_SESSION["signed"]['package']['signature']['signee']['netbios'] = strtolower($_SERVER["HTTP_HOST"]);
		$_SESSION["signed"]['package']['signature']['signee']['ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".$_SERVER["SERVER_ADDR"]."/json.api"), true);
		$_SESSION["signed"]['package']['signature']['signee']['signed'] = microtime(true);
		$_SESSION["signed"]['package']['signature']['signer']['name'] = $_SESSION["signed"]['package']['personal']['name'];
		$_SESSION["signed"]['package']['signature']['signer']['email'] = $_SESSION["signed"]['package']['personal']['email'];
		$_SESSION["signed"]['package']['signature']['signer']['netbios'] = $netbios = gethostbyaddr($ip = signedSecurity::getInstance()->getIP(true));
		$_SESSION["signed"]['package']['signature']['signer']['ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".signedSecurity::getInstance()->getIP(true)."/json.api"), true);
		$_SESSION["signed"]['package']['signature']['signer']['signed'] = microtime(true);
		$_SESSION["signed"]['package']['serial-number'] = md5(signedArrays::getInstance()->collapseArray($_SESSION["signed"]['package']));
		$_SESSION["signed"]['package']['signature']['requests'] = array();
		$_SESSION["signed"]['package']['requests'] = array();
		$_SESSION["signed"]['package']['retrieve']['log'][(isset($_SESSION["signed"]['package']['log']['url'])?count($_SESSION["signed"]['package']['log']['url'])+1:0)] = _URL_API . '/retrieve/?netbios='.$netbios . '?ip=' . $ip;
		return $_SESSION["signed"]['sealed'] = true;
	}
	
	/**
	 *
	 */
	/**
	 *
	 */
	function sealEditedPackage($type = 'personal') {
		if (isset($_SESSION["signed"]['sealed']))
			return false;
		if (isset($_SESSION["signed"]['package']['signature']['requests']))
			$editnumber = $this->_arrays->makeAlphaCount(count($_SESSION["signed"]['package']['signature']['requests']) + 1);
		else
			$editnumber = $this->_arrays->makeAlphaCount(1);
		$_SESSION["signed"]['package']['signature']['requests'][$editnumber]['requested'] = $_SESSION["signed"]['request']['created'];
		$_SESSION["signed"]['package']['signature']['requests'][$editnumber]['start-update'] = $_SESSION["signed"]['start'];
		$_SESSION["signed"]['package']['signature']['requests'][$editnumber]['sealed-update'] = microtime(true);
		$_SESSION["signed"]['package']['requests'][$editnumber]['when']['started'] = $_SESSION["signed"]['start'];
		$_SESSION["signed"]['package']['requests'][$editnumber]['when']['ended'] = microtime();
		$_SESSION["signed"]['package']['requests'][$editnumber]['when']['took'] = $_SESSION["signed"]['package']['requests'][$editnumber]['when']['ended'] - $_SESSION["signed"]['package']['requests'][$editnumber]['when']['started'];
		$_SESSION["signed"]['package']['requests'][$editnumber]['server']['netbios'] = gethostbyaddr($_SERVER["SERVER_ADDR"]);
		$_SESSION["signed"]['package']['requests'][$editnumber]['server']['ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".$_SERVER["SERVER_ADDR"]."/json.api"), true);
		$_SESSION["signed"]['package']['requests'][$editnumber]['client']['netbios'] = gethostbyaddr(signedSecurity::getInstance()->getIP(true));
		$_SESSION["signed"]['package']['requests'][$editnumber]['client']['ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".signedSecurity::getInstance()->getIP(true)."/json.api"), true);
		$_SESSION["signed"]['package']['requests'][$editnumber]['prompts'] = $_SESSION["signed"]['prompts'];
		$_SESSION["signed"]['package']['requests'][$editnumber]['requests'] = $_SESSION["signed"]['request'];
		$_SESSION["signed"]['package']['signature']['requests'][$editnumber]['serial-number'] = $_SESSION["signed"]['package']['requests']['serial-number'][$editnumber] = md5(collapseArray($_SESSION["signed"]['package']));
		return $_SESSION["signed"]['sealed'] = true;
	}
	
}