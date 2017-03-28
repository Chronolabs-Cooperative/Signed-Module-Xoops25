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


/**
 * 
 * @author Simon Roberts <simon@labs.coop>
 *
 */
class signedProcesses
{

	/**
	 *
	 * @var unknown
	 */
	var $_arrays = NULL;
	
	/**
	 *
	 * @var unknown
	 */
	var $_logger = NULL;
	
	/**
	 *
	 * @var unknown
	 */
	var $_io = NULL;

	/**
	 * 
	 * @return multitype:Ambigous <>
	 */
	function getFieldDescriptions()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			if (file_exists($file = _PATH_PROCESSES . _DS_ . constant('_SIGNED_CONFIG_LANGUAGE') . _DS_ . 'descriptions-fields.txt')) {
				foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $line) {
					$parts = signedArrays::getInstance()->trimExplode(explode('|', $line));
					if (!empty($parts[0]) && !empty($parts[1])) {
						$ret[$parts[0]] = $parts[1];
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 
	 * @return Ambigous <NULL, signedProcessess>
	 */
	static function getInstance()
	{
		static $object = NULL;
		if (!is_object($object))
			$object = new signedProcesses();
		return $object;
	}
	
	/**
	 *
	 * @return array
	 */
	function getLanguages()
	{
		static $ret = array();
		if (!count($ret)) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . _DS_ . 'languages.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]) && isset($parts[2]) && isset($parts[3]))
					$ret[$parts[0]] = array('path' => $parts[0], 'langcode' => $parts[1], 'charset' => $parts[2], 'title' => $parts[3]);
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getAPICalls()
	{
		static $ret = array();
		if (!count($ret)) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . 'api-calls.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]))
				if (constant("_SIGNED_API_FUNCTION_".strtoupper($parts[0]))==true)
					$ret[$parts[0]] = sprintf($parts[1], _URL_API);
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getEmailTemplateTypes()
	{
		static $ret = array();
		if (!isset($ret[constant('_SIGNED_CONFIG_LANGUAGE')])) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'email-template-types.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]))
					$ret[constant('_SIGNED_CONFIG_LANGUAGE')][$parts[0]] = $parts[1];
			}
		}
		return $ret[constant('_SIGNED_CONFIG_LANGUAGE')];
	}
		
	/**
	 *
	 * @return array
	 */
	function getSignatures()
	{
		static $ret = array();
		if (!isset($ret[constant('_SIGNED_CONFIG_LANGUAGE')])) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'signatures.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]))
					$ret[constant('_SIGNED_CONFIG_LANGUAGE')][$parts[0]] = $parts[1];
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getSites()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . 'apis-and-sites-available.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]) && isset($parts[2]) && isset($parts[3]) && isset($parts[4]))
					$ret[$parts[0]] = array('uri' => $parts[0], 'api-uri' => $parts[1], 'name' => (defined($parts[2])?constant($parts[2]):$parts[2]), 'search' => (defined($parts[3])?constant($parts[3]):$parts[3]), 'protocol' => $parts[4], 'code' => signedSecurity::getInstance()->getHostCode($parts[0]));
			}
		}
		return $ret;
	}
	
	
	/**
	 *
	 * @return array
	 */
	function getCcEmails()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . 'emails-cc.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]))
					$ret[$parts[1]] = array('name' => $parts[0], 'email' => $parts[1]);
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getBccEmails()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . 'emails-bcc.txt')) as $values) {
				$parts = signedArrays::getInstance()->trimExplode(explode('|', $values));
				if (isset($parts[0]) && isset($parts[1]))
					$ret[$parts[1]] = array('name' => $parts[0], 'email' => $parts[1]);
			}
		}
		return $ret;
	}
	

	/**
	 *
	 * @return array
	 */
	function getFieldnamesArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			foreach(	array(	'countries', 'dates', 'emails', 'enumerators', 'images',
					'months', 'numeric', 'strings', 'urls', 'years', 'logos', 'photos') as $types) {
					if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'fieldnames-'.$types.'.txt')) {
						foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'fieldnames-'.$types.'.txt')) as $fieldname) {
							$ret[trim($fieldname)] = trim($types);
						}
					}
			}
		}
		return $ret;
	}
	

	/**
	 *
	 * @return array
	 */
	function getProcessesArray()
	{
		static $ret = array();
		if (empty($ret[constant('_SIGNED_CONFIG_LANGUAGE')]) && count($ret[constant('_SIGNED_CONFIG_LANGUAGE')]) == 0) {
			if ($files = signedLists::getFileListAsArray(_PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE'))) {
				foreach($files as $file) {
					if (substr($file, strlen($file)-3, 3)=='txt') {
						$data = signedArrays::getInstance()->trimExplode(signedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . $file));
						$ret[constant('_SIGNED_CONFIG_LANGUAGE')][$file] = array('md5'=>md5(implode("\n", $data)), 'data'=>$data, 'path'=>constant('_SIGNED_CONFIG_LANGUAGE'));
					}
				}
			}
		}
		return $ret[constant('_SIGNED_CONFIG_LANGUAGE')];
	}
	
	/**
	 *
	 * @return array
	 */
	function getEnumeratorsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'fieldnames-enumerators.txt')) {
				foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $fieldname) {
					if (file_exists($enumfile =  _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'enumerator-'.$fieldname.'.txt')) {
						foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($enumfile)) as $enumeration) {
							if (strpos($enumeration, '|')>0) {
								$parts = signedArrays::getInstance()->trimExplode(explode('|', $enumeration));
								$ret[$fieldname][$parts[0]] = defined($parts[1])?constant($parts[1]):$parts[1];
							} else {
								$ret[$fieldname][str_replace(array(' ', '/', '\\', '_', '+', '&'), '-', strtolower($enumeration))] = $enumeration;
							}
						}
					} else
						die("Missing Definition File for Enumeration: " . $enumfile);
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getClassArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'class-'.$key.'.txt')) {
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $classes) {
						if (strpos($classes, '|')>0) {
							$parts = signedArrays::getInstance()->trimExplode(explode('|', $classes));
							$ret[$key][$parts[0]] = defined($parts[1])?constant($parts[1]):$parts[1];
						} else {
							$ret[$key][str_replace(array(' ', '/', '\\', '_', '+', '&'), '-', strtolower($classes))] = $classes;
						}
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getRequestPromptsArray($serial = '')
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'prompts-request-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $id => $prompts) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $prompts));
						$partsb = signedArrays::getInstance()->trimExplode(explode(':', $partsa[1]));
						if ($partsa[0]=='fields' && in_array($partsb[0], array_keys($_SESSION["signed"]['request']['request'][$key]))) {
							$ret[$key][$partsa[0].'-'.$partsb[0].'-'.$partsb[1]] = array('type' => $partsa[0], 'for' => $partsb[0], 'class' => $partsb[1]);
							$ret['order'][$key][$order++] = $partsa[0].'-'.$partsb[0].'-'.$partsb[1];
						} elseif ($partsa[0]!='fields')  {
							$ret[$key][$partsa[0].'-'.$partsb[0].'-'.$partsb[1]] = array('type' => $partsa[0], 'for' => $partsb[0], 'class' => $partsb[1]);
							$ret['order'][$key][$order++] = $partsa[0].'-'.$partsb[0].'-'.$partsb[1];
						}
					}
				} else {
					die("Missing: $file");
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getPromptsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'prompts-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $id => $prompts) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $prompts));
						$partsb = signedArrays::getInstance()->trimExplode(explode(':', $partsa[1]));
						$ret[$key][$partsa[0].'-'.$partsb[0].'-'.$partsb[1]] = array('type' => $partsa[0], 'for' => $partsb[0], 'class' => $partsb[1]);
						$ret['order'][$key][$order++] = $partsa[0].'-'.$partsb[0].'-'.$partsb[1];
					}
				} else {
					die("Missing: $file");
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getProvidedArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'provided-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $prompts) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $classes));
						$partsb = signedArrays::getInstance()->trimExplode(explode(';', $partsa[1]));
						$partsc = signedArrays::getInstance()->trimExplode(explode(':', $partsb[1]));
						$ret[$key][$partsa[0].'-'.$partsb[0].'-'.$partsc[0].'-'.$partsc[1]] = array('what' => $partsa[0], 'in' => $partsb[0], 'as' => $partsc[0], 'name' => $partsc[1]);
						$ret['order'][$key][$order++] = $partsa[0].'-'.$partsb[0].'-'.$partsc[0].'-'.$partsc[1];
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getSaltsArray($keys = array())
	{
		static $ret = array();
		$addresses_for_salt = array_unique(array_merge($this->getCcEmails(), $this->getBccEmails()));
		if (empty($keys) && count($keys)==0) {
			$keys = array_keys($addresses_for_salt);
		}
		if (!isset($ret[$key = md5(signedArrays::getInstance()->collapseArray($keys))])) {
			foreach($keys as $key) {
				$values = $addresses_for_salt[$key];
				if (in_array($values['email'], $keys)) {
					if (file_exists($file = _PATH_PROCESSES . _DS_ . $values['email'] . '.txt')) {
						$ret[$key][$values['email']] = array();
						$ret[$key][$values['email']]['file'] = $file;
						$ret[$key][$values['email']]['when'] = filemtime($file);
						$ret[$key][$values['email']]['salt'] = $data = signedArrays::getFileContents($file);
						$ret[$key][$values['email']]['md5'] = md5($data);
					}
				}
			}
		}
		return $ret[$key];
	}
	
	/**
	 *
	 * @return array
	 */
	function getIdentificationsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'identifications-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $identification) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $identification));
						$fields = signedArrays::getInstance()->trimExplode(explode(',', $partsa[2]));
						$partsb = signedArrays::getInstance()->trimExplode(explode(':', $partsa[1]));
						$ret[$key][$partsb[1]] = array('title' => defined($partsa[0])?constant($partsa[0]):$partsa[0], 'points' => $partsb[0], 'fieldname' => $partsb[1], 'fields' => $fields);
						$ret['order'][$key][$order++] = $partsb[1];
						$ret['points'][$key][$partsb[1]] = $partsb[0];
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getRequestStatesArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$fields = array();
			foreach($this->getFieldnamesArray() as $field => $type) {
				if ($type=="images"||$type=="logos"||$type=="photos") {
					$fields['images'][$type][$field] = $field;
				} else {
					$fields['all'][$type][$field] = $field;
				}
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'request-states.txt')) {
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $state) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $state));
						$ret[$partsa[0]] = array('code' => $partsa[0], 'description' => $partsa[1], 'expires-signature' => ($partsa[2]=='expires'?true:false), 'supported' => array('fields'=>$fields[$partsa[3]]));
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getDimensionsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = $this->getSignatures();
			foreach(array('resize', 'upload') as $type) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . $type . '-dimensions.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $dimension) {
						$partsa = signedArrays::getInstance()->trimExplode(explode(':', $dimension));
						$pixels = signedArrays::getInstance()->trimExplode(explode('x', $partsa[2]));
						$ret[$type][$partsa[0]][$partsa[1]] = array('display' => $partsa[2], 'width' => $pixels[0], 'height' => $pixels[1], 'scape' => $partsa[0], 'type' => $partsa[1]);
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getFieldsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$validations = $this->getValidationsArray();
			$fieldnames = $this->getFieldnamesArray();
			$signatures = $this->getSignatures();
			$signatures[constant('_SIGNED_CONFIG_LANGUAGE')]['identification'] = 'Identification';
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'fields-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $field) {
						$partsa = signedArrays::getInstance()->trimExplode(explode('|', $field));
						if (in_array($partsa[0], array_keys($fieldnames))) {
							if (isset($validations[$key]) && isset($validations[$key]['required'][$key]['fields']))
								$ret[$key][$partsa[0]] = array('name' => $partsa[0], 'type' => $fieldnames[$partsa[0]], 'title' => defined($partsa[1])?constant($partsa[1]):$partsa[1], 'required' => (trim($partsa[2])=='required'||in_array($partsa[0], $validations[$key]['required'][$key]['fields'])?true:false), 'class'=>$partsa[2]);
							else
								$ret[$key][$partsa[0]] = array('name' => $partsa[0], 'type' => $fieldnames[$partsa[0]], 'title' => defined($partsa[1])?constant($partsa[1]):$partsa[1], 'required' => (trim($partsa[2])=='required'?true:false), 'class'=>$partsa[2]);
							$ret['order'][$key][$order++] = $partsa[0];
						}
					}
				}
			}
		}
		return $ret;
	}
	
	
	/**
	 *
	 * @return array
	 */
	function getValidationsArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			$signatures = array_merge($this->getSignatures());
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'validations-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $validation) {
						$partsa = signedArrays::getInstance()->trimExplode(explode(':', $validation));
						$typal = signedArrays::getInstance()->trimExplode(explode('-', $partsa[0]));
						if (count($typal)>2) {
							$type = $typal[count($typal)-1];
							unset($typal[count($typal)-1]);
							$keyz = implode('-', $typal);
						} else {
							$type = $typal[1];
							$keyz = $typal[0];
						}
						unset($typal);
						if (!empty($partsa[1]) && strpos("|", $partsa[1])>0) {
							$partsc = signedArrays::getInstance()->trimExplode(explode('|', $partsa[1]));
							$function = $partsc[1];
							$fields = signedArrays::getInstance()->trimExplode(explode(",", $partsc[0]));
						} else {
							$function = _SIGNED_VALIDATION_FUNCTION;
							$fields = signedArrays::getInstance()->trimExplode(explode(",", $partsa[1]));
						}
						$ret[$key][$type][$keyz] = array('type' => $type, 'key' => $keyz, 'fields' => $fields, 'function' => $function);
						$ret['order'][$key][$type][$order++] = $keyz;
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 *
	 */
	function getLanguageFiles($path)
	{
		$ret = array();
		$ret['mail_template']['html']['path'] =  DIRECTORY_SEPARATOR . 'mail_template'  . DIRECTORY_SEPARATOR . 'html';
		$ret['mail_template']['txt']['path'] =  DIRECTORY_SEPARATOR . 'mail_template'  . DIRECTORY_SEPARATOR . 'text';
		$ret['sms_template']['txt']['path'] =  DIRECTORY_SEPARATOR . 'sms_template';
		$ret['constants']['php']['path'] = DIRECTORY_SEPARATOR;
		$ret['mail_template']['html']['files'] = signedLists::getFileListAsArray($path . DIRECTORY_SEPARATOR . 'mail_template'  . DIRECTORY_SEPARATOR . 'html');
		$ret['mail_template']['txt']['files'] = signedLists::getFileListAsArray($path . DIRECTORY_SEPARATOR . 'mail_template'  . DIRECTORY_SEPARATOR . 'text');
		$ret['sms_template']['txt']['files'] = signedLists::getFileListAsArray($path . DIRECTORY_SEPARATOR . 'sms_template');
		$ret['constants']['php']['files'] = signedLists::getFileListAsArray($path);
		return $ret;
	}
	
	/**
	 *
	 * @return array
	 */
	function getLanguageFilesArray()
	{
		static $ret = array();
		if (empty($ret) && count($ret) == 0) {
			if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . 'language-files.txt')) {
				foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $languagefile) {
					$ret['global'][$languagefile] = $languagefile;
				}
			}
			$signatures = array_merge($this->getSignatures());
			foreach($signatures[constant('_SIGNED_CONFIG_LANGUAGE')] as $key => $signature) {
				if (file_exists($file = _PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . 'language-files-'.$key.'.txt')) {
					$order = 0;
					foreach(signedArrays::getInstance()->trimExplode(signedArrays::getFile($file)) as $languagefile) {
						$ret[$key][$languagefile] = $languagefile;
					}
				}
			}
		}
		return $ret;
	}
	
	
}