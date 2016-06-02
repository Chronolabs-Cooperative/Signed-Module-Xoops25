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
 */
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedobject.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'xmlarray.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'xmlwrapper.php';

/**
 * 
 * @author Simon Roberts <simon@labs.coop>
 * @author Simon Antony Roberts (Aus Passport: M8747409) <wishcraft@users.sourceforge.net>
 *
 */
class signedStorage extends signedObject
{
	
	/**
	 * 
	 * @var unknown
	 */
	var $_method ='';
	
	/**
	 *
	 * @var unknown
	 */
	var $_root = 'signed';
		
	/**
	 *
	 * @var unknown
	 */
	var $_methods ='';
	
	/**
	 *
	 * @var unknown
	 */
	var $_extensions = array();

	/**
	 *
	 * @var unknown
	 */
	var $fingerprints = array();
	
	/**
	 * 
	 * @param string $method
	 */
	function __construct($method='json')
	{
		$this->_method = $method;
		$this->_methods = array('json'=>'parseJSON', 'xml'=>'parseXML', 'serial'=>'parseSerialisation');
		$this->_extensions = array('json'=>'json', 'xml'=>'xml', 'serial'=>'serial');
	}
	
	static function getInstance($method = 'json')
	{
		static $object = array();
		if (!isset($object[$method]))
			$object[$method] = new signedStorage($method);
		$object[$method]->intialise();
		return $object[$method];
	}

	/**
	 *
	 * @param string $path
	 * @param string $filetitle
	 * @return multitype:
	 */
	function save($data = array(), $path = '', $filetitle = '', $root = 'signed', $method = '')
	{
		if (empty($method)||!in_array($method, $this->_methods))
			$method = $this->_method;
		if (empty($root))
			$root = $this->_root;
		if (isset($data['fingerprint']))
			unset($data['fingerprint']);
		$this->fingerprints[$method][md5($path.DIRECTORY_SEPARATOR.$filetitle)] = $data['fingerprint'] = signedCiphers::getInstance()->fingerprint($data, 'sha1');
		$content = $this->parse($data, $root, $method);
		$this->deleteFiles($path, $filetitle);
		if (!empty($content)) {
			if (!is_dir($path))
				mkdir($path, 0777, true);
			return $this->saveFile($content, $filename = $path . DIRECTORY_SEPARATOR . $filetitle . '.' . $this->_extensions[$method]);
		}
		if(isset($_SESSION["signed"]['signedSignature'][$filetitle]) && !empty($_SESSION["signed"]['signedSignature']))
		{
			switch ($path)
			{
				case constant("_PATH_REPO_SIGNATURES"):
					$_SESSION["signed"]['signedSignature'][$filetitle]->setVar('bytes', filesize($filename));
					$_SESSION["signed"]['signedSignature'][$filetitle]->setVar('saved', time());
					break;
				case constant("_PATH_REPO_VALIDATION"):
					if ($content['verification']['expired'] != false)
					{
						$_SESSION["signed"]['signedSignature'][$filetitle]->setVar('expired', time());
						$_SESSION["signed"]['signedSignature'][$filetitle]->setVar('state', 'inactive');
					}
					break;
				case constant("_PATH_REPO_SIGNED"):
					if (isset($content['signid']) && !empty($content['signid']))
					{				
						$GLOBALS['eventCurrent']->setVar('comment', $content['pathway']['ip'] . ' signed --- document identity: ' . $content['pathway']['document']['identity'] . ' document title: ' . $content['pathway']['document']['identity']);
						$GLOBALS['eventHandler']->insert($GLOBALS['eventCurrent'], true);
					}
					break;
					
			}
		}
		return true;
	}
	
	/**
	 * 
	 * @param string $path
	 * @param string $filetitle
	 * @return multitype:
	 */
	function load($path = '', $filetitle = '', $root = 'signed') 
	{
		$data = array();
		$method = $this->_method;
		if (empty($root))
			$root = $this->_root;
		foreach($this->_methods as $extension => $function)
		{
			if ($filename = $this->file_exists($path, $filetitle)) {
				$function = $this->_methods[$method = $extension];
				$data = $this->convert($this->readFile($filename), $root, $extension);
			}
		}
		if (!isset($data['fingerprint']))
			$this->fingerprints[$method][md5($path.DIRECTORY_SEPARATOR.$filetitle)] = $data['fingerprint'] = signedCiphers::getInstance()->fingerprint($data, 'sha1');
		else
			$this->fingerprints[$method][md5($path.DIRECTORY_SEPARATOR.$filetitle)] = $data['fingerprint'];
		if(isset($data['signid']) && !empty($data['signid']))
		{
			$signaturesHandler = xoops_getmodulehandler('signatures', 'signed');
			if (!is_object($_SESSION["signed"]['signedSignature'][$filetitle]))
			{
				$_SESSION["signed"]['signedSignature'][$filetitle] = $signaturesHandler->get($data['signid']);
			}
		} 
		return $data;
	}
	
	function file_exists($path = '', $filetitle = '')
	{
		foreach($this->_methods as $extension => $function)
		{
			if (file_exists($file = $path . DIRECTORY_SEPARATOR . $filetitle . '.' . $extension))
				return $file;
				
		}
		return false;
	}

	/**
	 *
	 */
	private function modifyNumericKeys($array = '', $convert = false, $spatial = '___')
	{
		if (!$convert) {
			$changed = false;
			$values = array();
			foreach(array_reverse(array_keys($array)) as $key)
			{
				if (is_numeric($key))
				{
					$changed = true;
					$newkey = $spatial . $key . $spatial;
					if (is_array($values[$key]))
						$values[$newkey] = $this->modifyNumericKeys($array[$key], $convert);
					else
						$values[$newkey] = $array[$key];
					unset($array[$key]);
				}
			}
			if ($changed == true) {
				foreach(array_reverse(array_keys($values)) as $key)
				{
					$array[$key] = $values[$key];
				}
			}
		} else {
			$changed = false;
			$values = array();
			foreach(array_reverse(array_keys($array)) as $key)
			{
				if (substr($key, 0, strlen($spatial)) == $spatial && substr($key, strlen($key) - strlen($spatial), strlen($spatial)) == $spatial )
				{
					$changed = true;
					$newkey = substr($key, strlen($spatial), strlen($key) - strlen($spatial) - strlen($spatial));
					if (is_array($values[$key]))
						$values[$newkey] = $this->modifyNumericKeys($array[$key], $convert);
					else
						$values[$newkey] = $array[$key];
					unset($array[$key]);
				}
			}
			if ($changed == true) {
				foreach(array_reverse(array_keys($values)) as $key)
				{
					$array[$key] = $values[$key];
				}
			}
		}
		return $array;
	}
	
	/**
	 *
	 * @param string $filename
	 * @return boolean|string
	 */
	private function parse($array = array(), $root = 'signed', $method = '')
	{
		if (empty($method)||!in_array($method, $this->_methods))
			$method = $this->_method;
		if (empty($root))
			$root = $this->_root;
		$function = $this->_methods[$method];
		return $this->$function((!empty($root)?array($root => $array):$array));
	}
	
	/**
	 *
	 * @param string $filename
	 * @return boolean|string
	 */
	private function convert($content = '', $root = 'signed', $method = '')
	{
		if (empty($method)||!in_array($method, $this->_methods))
			$method = $this->_method;
		if (empty($root))
			$root = $this->_root;
		$function = $this->_methods[$method];
		$ret = $this->$function($content);
		return (isset($ret[$root])?$ret[$root]:$ret);
	}
	
	/**
	 * 
	 * @param string $mixed
	 * @param string $action
	 * @return string|mixed|boolean
	 */
	private function parseJSON($mixed = '', $action = '')
	{
		if (empty($action)) 
		{
			if (is_array($mixed))
				$action = 'pack';
			else 
				$action = 'unpack';
		}
		switch ($action)
		{
			case "pack":
				return json_encode($mixed);
				break;
			case "unpack":
				return json_decode($mixed, true);
				break;
		}
		return false;
	}
	

	/**
	 *
	 * @param string $mixed
	 * @param string $action
	 * @return string|mixed|boolean
	 */
	private function parseXML($mixed = '', $action = '')
	{
		if (empty($action))
		{
			if (is_array($mixed))
				$action = 'pack';
			else
				$action = 'unpack';
		}
		switch ($action)
		{
			case "pack":
				$dom = new XmlDomConstruct('1.0', 'utf-8');
				$dom->fromMixed($this->modifyNumericKeys($array, false));
				return $dom->saveXML();
				break;
			case "unpack":
				return $this->modifyNumericKeys(XML2Array::createArray($mixed), true);
				break;
		}
		return false;
	}
	

	/**
	 *
	 * @param string $mixed
	 * @param string $action
	 * @return string|mixed|boolean
	 */
	private function parseSerialisation($mixed = '', $action = '')
	{
		if (empty($action))
		{
			if (is_array($mixed))
				$action = 'pack';
			else
				$action = 'unpack';
		}
		switch ($action)
		{
			case "pack":
				return serialize($mixed);
				break;
			case "unpack":
				return unserialize($mixed);
				break;
		}
		return false;
	}
	/**
	 *
	 * @param string $filename
	 * @return boolean|string
	 */
	private function deleteFiles($path = '', $filetitle = '')
	{
		foreach($this->_methods as $extension => $function)
		{
			if ($file = $this->file_exists($path, $filetitle))
				unlink($file);
			
		}
		return true;
	}

	
	/**
	 *
	 * @param string $filename
	 * @return boolean|string
	 */
	private function readFile($filename = '')
	{
		if (!file_exists($filename))
			return false;
	
		$data = file_get_contents($filename);
		$file = basename($filename);
		$path = str_replace(array($file, XOOPS_VAR_PATH), "", $filename);
		$keiye_handler = xoops_getmodulehandler('keiyes', 'signed');
		$results = $keiye_handler->retieveKeiye($file, $path);
		$extension = $results['algorithm'].'.'.$results['cipher'];
		if (!empty($extension) && $extension != '.')
		{
			$cryptus_handler = xoops_getmodulehandler('cryptus', 'signed');
			$data = $cryptus_handler->cryptolibs->decrypt($extension, $data, $results['key']);
		}
	
		if ($GLOBALS['logger'] = signedLogger::getInstance())
			$GLOBALS['logger']->logBytes(strlen($data), 'io-read');
		return $data;
	}
	
	/**
	 *
	 * @param string $content
	 * @param string $filename
	 * @return boolean
	 */
	private function saveFile($content = '', $filename = '')
	{
		if (file_exists($filename))
			unlink($filename);
	
		if (empty($content))
			return false;
	
		$file = basename($filename);
		$path = str_replace(array($file, XOOPS_VAR_PATH), "", $filename);
		$keiye_handler = xoops_getmodulehandler('keiyes', 'signed');
		$algorithm = '';
		$cipher = '';
		$key = '';
		$sealmd5 = $openmd5 = md5($content);
		if ($_SESSION['signed']['encryption'])
		{
			$cryptus_handler = xoops_getmodulehandler('cryptus', 'signed');
			$parts = explode(".", $ext = $cryptus_handler->getRandomExtension());
			$bitz = $cryptus_handler->cryptolibs->getKeysBitz();
			$keyfunc = $cryptus_handler->cryptolibs->kieyeFunc();
			$key = $cryptus_handler->cryptolibs->$keyfunc(sha1($content).md5($content), SIGNED_BLOWFISH_SALT, $bitz[$ext], true);
			$algorithm = $parts[0];
			unset($parts[0]);
			$cipher = implode('.', $parts);
			$openmd5 = md5($content);
			$sealmd5 = md5($content = $cryptus_handler->cryptolibs->encrypt($ext, $content, $key));
		}
	
		if (file_put_contents($filename, $content, false))
		{
			$keiye_handler->lodgeKey($file, $path, $algoritm, $cipher, $key, $sealmd5, $openmd5, filesize($filename));
			if ($GLOBALS['logger'] = signedLogger::getInstance())
				$GLOBALS['logger']->logBytes(strlen($content), 'io-write');
		}
		return $filename;
	}
	
	
	/** function getURL()
	 *
	 * 	cURL Routine
	 *  @return 		string()
	 */
	public static function getURL($uri = '', $timeout = 17, $connectout = 28, $post_data = array(), $getheaders = false)
	{
		if (!function_exists("curl_init"))
		{
			return file_get_contents($uri);
		}
		if (!$uiol = curl_init($uri)) {
			return false;
		}
		curl_setopt($uiol, CURLOPT_POST, (count($post_data)==0?false:true));
		if (count($post_data)!=0)
			curl_setopt($uiol, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($uiol, CURLOPT_CONNECTTIMEOUT, $connectout);
		curl_setopt($uiol, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($uiol, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($uiol, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($uiol, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($uiol, CURLOPT_VERBOSE, $getheaders);
		curl_setopt($uiol, CURLOPT_HEADER, $getheaders);
	
		/**
		 * Execute Curl Call
		 * @var string
		 */
		$response = curl_exec($uiol);
		if ($getheaders==true) {
			$infos = curl_getinfo($uiol);
			$header = substr($response, 0, curl_getinfo($uiol, CURLINFO_HEADER_SIZE));
			$data = substr($response, curl_getinfo($uiol, CURLINFO_HEADER_SIZE));
			curl_close($uiol);
			return array('info'=>$infos, 'header' =>$header, 'data' => $data);
		}
		curl_close($uiol);
		return $response;
	}
}