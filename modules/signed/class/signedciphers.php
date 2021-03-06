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

/**
 *
 * @author Simon Roberts <simon@labs.coop>
 *
 */
class signedCiphers extends signedObject
{


	/**
	 *
	 * @var unknown
	 */
	var $_processes = NULL;
	
		
	/**
	 *
	 */
	function __construct()
	{
		$this->_processes = signedProcesses::getInstance();
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
		ini_set('display_errors', true);
		error_reporting(E_ERROR);
		
		static $object = NULL;
		if (!is_object($object))
			$object = new signedCiphers();
		$object->intialise();
		return $object;
	}
	
	/**
	 * 
	 */
	function watermarkOriginalImage($wideimage = '')
	{
		if (empty($wideimage) && !is_object($wideimage))
			return false;

		try {
			$originalWidth = $wideimage->getWidth();
			$originalHeight = $wideimage->getHeight();
			$rangeWidth = ($originalWidth - 10) / 2;
			$rangeHeight = ($originalHeight - 10) / 2;
			$watermark = @WideImage::load(_PATH_ROOT . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . _SIGNED_WATERMARK_GIF);
			$watermarkWidth = $watermark->getWidth();
			$watermarkHeight = $watermark->getHeight();
			$numberperquad = ((($rangeWidth / $watermarkWidth) + ($rangeHeight / $watermarkHeight)) /2 ) * _SIGNED_WATERMARK_NUMBERTOSCALE;
			for($r=0;$r<=$numberperquad;$r++) {
				mt_srand(mt_rand(-microtime(true), microtime(true)));
				$marker = $watermark->rotate(mt_rand(1,359));
				$wideimagea = $wideimage->merge($marker, 'left + ' . mt_rand(0, $rangeWidth) + 5, 'top + ' . mt_rand(0, $rangeHeight) + 5, mt_rand(_SIGNED_WATERMARK_MINIMUM_OPACITY, _SIGNED_WATERMARK_MAXIMUM_OPACITY));
				$marker = $watermark->rotate(mt_rand(1,359));
				$wideimageb = $wideimagea->merge($marker, 'left + ' . mt_rand(0, $rangeWidth) + 5, 'bottom - ' . mt_rand(0, $rangeHeight) + 5, mt_rand(_SIGNED_WATERMARK_MINIMUM_OPACITY, _SIGNED_WATERMARK_MAXIMUM_OPACITY));
				$marker = $watermark->rotate(mt_rand(1,359));
				$wideimagec = $wideimageb->merge($marker, 'right - ' . mt_rand(0, $rangeWidth) + 5, 'bottom - ' . mt_rand(0, $rangeHeight) + 5, mt_rand(_SIGNED_WATERMARK_MINIMUM_OPACITY, _SIGNED_WATERMARK_MAXIMUM_OPACITY));
				$marker = $watermark->rotate(mt_rand(1,359));
				unset($wideimage);
				$marker = $watermark->rotate(mt_rand(1,359));
				$wideimage = $wideimagec->merge($marker, 'right - ' . mt_rand(0, $rangeWidth) + 5, 'top + ' . mt_rand(0, $rangeHeight) + 5, mt_rand(_SIGNED_WATERMARK_MINIMUM_OPACITY, _SIGNED_WATERMARK_MAXIMUM_OPACITY));
			}
		}
		catch (Exception $e) { trigger_error('Error Watermarking Image: ' . $e); }
		return $wideimage;
	}
	
	/**
	 *
	 */
	function makeAlphaCount($num = 0)
	{
		return signedArrays::getInstance()->makeAlphaCount($num);
	}
		
	/**
	*
	* @param string $data
	* @param string $method
	*/
	function fingerprint($data = '', $method = 'md5')
	{
		if (is_array($data))
			return $this->getHash(signedArrays::getInstance()->collapseArray($data), $method);
		else
			return $this->getHash($data, $method);
	}
	

	/**
	 *
	 */
	function extractServiceKey($code = '', $certicate = '', $verificationkey = '')
	{
		if (!empty($code)) {
			$parts = signedArrays::getInstance()->trimExplode(explode('-', $code));
			return $parts[0];
		} elseif(!empty($verificationkey)) {
			$parts = signedArrays::getInstance()->trimExplode(explode('-', $code));
			return $parts[0];
		} elseif (!empty($certicate)) {
			foreach(signedArrays::getInstance()->trimExplode(explode("\n", $certicate)) as $line) {
				if (substr($line, 0, 2)=='==') {
					return substr($line, 2);
				}
			}
		}
		return md5(NULL);
	}
	
	/**
	 *
	 */
	function getSignatureCode($mode, $package = array())
	{
		return signedSecurity::getInstance()->getSignatureCode($mode, $package);
	}
	
	/**
	 *
	 */
	function generateSignatureKey($signature = array(), $data = array())
	{
		return $this->getHash(sha1($signature['serial-number'].$data['docid'].sha1($data['doctitle']).$this->getSalt()), 'xcp', 59);
	}
	

	/**
	 *
	 */
	function shortenURL($url = '')
	{
		$hash = $this->getHash(md5($url), 'xcp', 7);
		if ($this->_io = signedStorage::getInstance(_SIGNED_URLS_STORAGE))
			$this->_io->save(array('url'=>$url), _PATH_URLS, sha1($hash));
		return _URL_URLS . '/' . $hash;
	}
	
	
	/**
	 *
	 */
	function getURL($hash = '')
	{
		if ($this->_io = signedStorage::getInstance(_SIGNED_URLS_STORAGE))
			$array = $this->_io->load(_PATH_URLS, sha1($hash));
		if (isset($array['url'])) {
			$_SESSION["signed"]['unlink'][] = _PATH_URLS . DIRECTORY_SEPARATOR . sha1($hash) . '.' . $this->_io->_extensions[_SIGNED_URLS_STORAGE];
			return $array['url'];
		}
		if (file_exists(_PATH_URLS . DIRECTORY_SEPARATOR . sha1($hash) . '.url')) {
			$array = signedArrays::getFile(_PATH_URLS . DIRECTORY_SEPARATOR . sha1($hash) . '.url');
			$_SESSION["signed"]['unlink'][] = _PATH_URLS . DIRECTORY_SEPARATOR . sha1($hash) . '.url';
			if (!empty($array) && isset($array[0]) && strlen($array[0]) > 0) {
				return $array[0];
			}
		}
		return false;
	}
	
	/**
	 *
	 */
	function getSignature($serial = '', $code = '', $certificate = '', $name = '', $email = '', $date = '', $needsverified = false)
	{
		static $resources = array();
		$GLOBALS['io'] = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE);
		$dossier = array('serial', 'code', 'certificate');
		foreach($dossier as $field) {
			if (empty($resources[$field])) {
				$pointers = array();
				switch($field)
				{
					case 'serial':
						if ($GLOBALS['io']->file_exists(_PATH_REPO_SIGNATURES, $filetitle = $serial)) {
							$array = $GLOBALS['io']->load(_PATH_REPO_SIGNATURES, $filetitle);
							$resources[$field] = $array['resources'];
						}
						break;
					case 'code':
						if ($GLOBALS['io']->file_exists(_PATH_PATHWAYS_CODES, $filetitle = md5($code))) {
							$pointers = $GLOBALS['io']->load(_PATH_PATHWAYS_CODES, $filetitle);
						}
						if ($GLOBALS['io']->file_exists(_PATH_REPO_SIGNATURES, $filetitle = $pointers['pathway']['serial-number'])) {
							$array = $GLOBALS['io']->load(_PATH_REPO_SIGNATURES, $filetitle);
							$resources[$field] = $array['resources'];
						}
						break;
					case 'certificate':
						if ($GLOBALS['io']->file_exists(_PATH_PATHWAYS_CERTIFICATES, $filetitle = md5(implode("", signed_trimExplode(explode("\n", $certificate)))))) {
							$pointers = $GLOBALS['io']->load(_PATH_PATHWAYS_CERTIFICATES, $filetitle);
						}
						if ($GLOBALS['io']->file_exists(_PATH_REPO_SIGNATURES, $filetitle = $pointers['pathway']['serial-number'])) {
							$array = $GLOBALS['io']->load(_PATH_REPO_SIGNATURES, $filetitle);
							$resources[$field] = $array['resources'];
						}
						break;
				}
			}
		}
	
		$classone = 0;
		foreach($resources as $field => $resource) {
			foreach($resources as $fieldb => $resourceb) {
				if ($resource['serial-number']!=$resourceb['serial-number']) {
					unset($_SESSION["signed"]['signedSignature'][$resourceb['serial-number']]);
					$pass=false;
				} else {
					$classone++;
					$ret = $resource;
				}
			}
		}
	
		$twokeys = array();
		if (!empty($name))
			$twokeys['names'] = sha1(strtolower($name));
		if (!empty($email))
			$twokeys['emails'] = sha1(strtolower($email));
		if (!empty($date))
			$twokeys['dates'] = sha1(date("Y-m-d",strtotime($date)));
		$dossier = array(	'emails' 	=> constant("_PATH_PATHWAYS_EMAILS"),
				'names' 	=> constant("_PATH_PATHWAYS_NAMES"),
				'dates' 	=> constant("_PATH_PATHWAYS_DATES"));
		$classtwo = 0;
		foreach($dossier as $field => $path) {
			if (!empty($twokeys[$field])) {
				$array = $GLOBALS['io']->load($path, $twokeys[$field]);
				if (in_array($ret['serial-number'], array_keys($array))) {
					$classtwo++;
				}
			}
		}
	
		$pass = false;
		if ($classone>=2||($classone>=1&&$classtwo>=1))
			$pass = true;
	
		if ($GLOBALS['io']->file_exists(_PATH_REPO_VALIDATION, $filetitle =  $ret['serial-number'])) {
			$verifier = $GLOBALS['io']->load(_PATH_REPO_VALIDATION, $filetitle);
		}
		if (isset($verifier) && is_array($verifier)) {
			if ($needsverified==true && $pass==true) {
				if (!isset($verifier['verification']['verified'])||$verifier['verification']['verified']==false) {
					if (function_exists('http_response_code'))
						http_response_code(400);
					echo json_encode(array('success'=> false, 'error'=> 'The signature is unverified still and doesn\'t allow for Digital Ensignment to occur!', 'error-code' => '105'));
					exit(0);
				}
			}
			if (isset($verifier['verification']['expired'])&&$verifier['verification']['expired']!=false) {
				if (function_exists('http_response_code'))
					http_response_code(400);
				echo json_encode(array('success'=> false, 'error'=> 'The signature has expired and doesn\'t allow for Digital Ensignment to occur!', 'error-code' => '106'));
				exit(0);
			}
		}
		$_SESSION["signed"]['signedSignature']['current'] = $ret['serial-number'];
		$_SESSION["signed"]['signedSignature'][$ret['serial-number']]->setVar('used', time());
		return ($pass==true?$ret:false);
	}
	
	/**
	 * Version: LABSCOOP-DS v1.0.3
	 */
	function getSignatureCertificate($mode, $package = array())
	{
		static $length = 53;
		set_time_limit(9999);
		$hashing = array();
		$hashing['z'] = $this->getHash($crc = sha1($data = signedArrays::getInstance()->collapseArray($package).$this->getSalt()), 'xcp', $length);
		$hashing['7'] = $this->getHash($crc = sha1($data = signedArrays::getInstance()->collapseArray($package).$this->getSalt().signedArrays::getInstance()->collapseArray($hashing)), 'xcp', $length);
		$hashing['y'] = $this->getHash($crc = md5($crc.$data), 'xcp', $length);
		$hashing['w'] = $this->getHash($crc = md5($crc.$data.signedArrays::getInstance()->collapseArray($hashing)), 'xcp', $length);
		$hashing['s'] = $this->getHash($crc = crc32($data.$crc), 'xcp', $length);
		$hashing['K'] = $this->getHash($crc = crc32($data.$crc.signedArrays::getInstance()->collapseArray($hashing)), 'xcp', $length);
		$keysa = $keysc = $keyse = array_keys($hashing);
		$keysb = $keysd = $keysf = array_reverse(array_keys($hashing));
		shuffle($keysc);
		shuffle($keysd);
		shuffle($keyse);
		shuffle($keysf);
		$keyse = array_reverse($keyse);
		$keysf = array_reverse($keysf);
		$hash = '';
		for($i=0;$i<$length;$i++) {
			foreach($keysa as $key)
				$hash .= substr($hashing[$key], $i, 1);
			foreach($keysb as $key)
				$hash .= substr($hashing[$key], $i, 1);
			foreach($keysc as $key)
				$hash .= substr($hashing[$key], $i, 1);
			foreach($keysd as $key)
				$hash .= substr($hashing[$key], $i, 1);
			foreach($keyse as $key)
				$hash .= substr($hashing[$key], $i, 1);
			foreach($keysf as $key)
				$hash .= substr($hashing[$key], $i, 1);
		}
		$hash .= 'D'.implode('', $keysa);
		$hash .= '3'.implode('', $keysb);
		$hash .= 'R'.implode('', $keysc);
		$hash .= '0'.implode('', $keysd);
		$hash .= 'F'.implode('', $keyse);
		$hash .= 'L'.implode('', $keysf);
		$ret = array();
		$i=1;
		$ret[$i] = '';
		for($u=0; $u<=strlen($hash); $u++) {
			if (strlen($ret[$i])==strlen("-----BEGIN SIGNATURE CERTIFICATE KEY BLOCK-----")) {
				$i++;
				$ret[$i] = '';
			}
			$ret[$i] .= substr($hash, $u, 1);
		}
		$template = signedArrays::getFileContents(_PATH_PROCESSES . DIRECTORY_SEPARATOR . 'certificate.txt');
		return sprintf($template, implode("\n", $ret), $this->_security->getHostCode());
	}
	
	/**
	 *
	 * @return array
	 */
	function getHash($data = '', $type = 'md5', $length = 44, $seed = -1)
	{
		switch($type) {
			default:
			case 'md5':
				return md5($data);
				break;
			case 'sha1':
				return sha1($data);
				break;
			case 'xcp':
				include_once _PATH_ROOT . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'xcp' . DIRECTORY_SEPARATOR . 'xcp.class.php';
				$hash = new xcp($data, ($seed == -1?ord((strlen($data)>=2?substr($data, strlen($data)-2, 1):substr($data, strlen($data)-1, 1))):$seed), $length);
				return $hash->crc;
				break;
		}
		return false;
	}
	

	/**
	 *
	 * @return array
	 */
	function getSalt($keys = array())
	{
		static $ret = array();
		if (empty($keys) && count($keys)==0) {
			$keys = array_unique(array_keys(array_merge($this->_processes->getCcEmails(), $this->_processes->getBccEmails())));
		}
		if (!isset($ret[md5(signedArrays::getInstance()->collapseArray($keys))])) {
			foreach($this->_processes->getSaltsArray($keys) as $key=>$values) {
				$ret[md5(signedArrays::getInstance()->collapseArray($keys))][$key]= sha1(signedArrays::getInstance()->collapseArray($values));
			}
		}
		return implode("::", $ret[md5(signedArrays::getInstance()->collapseArray($keys))]);
	}
	
}