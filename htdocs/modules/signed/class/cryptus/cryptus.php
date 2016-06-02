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
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'signedlists.php';

class signedCryptusLibraries extends signedCryptus
{

	
	/**
	 * @var array
	 */
	var $algoritms = array();
	
	/**
	 * @var array
	 */
	var $extensions = array();
	
	/**
	 * @var array
	 */
	var $cipherobjs = array();
	
	
	/**
	 *
	 */
	function __construct()
	{
		foreach(signedLists::getDirListAsArray(__DIR__) as $key => $folder)
		{
			if (file_exists($cipherlib = __DIR__ . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . ".php"))
			{
				$pass = true;
				require_once $cipherlib;
				$class_name = "signedCryptus" . ucfirst(str_replace(array(" "), "", ucwords(str_replace(array("-", ".", "_"), " ", $folder))));
				if (class_exists($class_name))
				{
					$this->cipherobjs[$folder] = new $class_name();
					if (count($this->cipherobjs[$folder]->phplibry)>0)
					{
						foreach($this->cipherobjs[$folder]->phplibry as $extension)
							if (!empty($extension))
								if (!extension_loaded($extension))
									$pass = false;
					}
					if (count($this->cipherobjs[$folder]->phpfuncs)>0)
					{
						foreach($this->cipherobjs[$folder]->phpfuncs as $function)
							if (!empty($function))
								if (!function_exists($function))
									$pass = false;
					}
					if ($pass==true)
					{
						$this->algoritms[$folder] = @$this->cipherobjs[$folder]->getAlgorithms();
						$this->extensions[$folder] = @$this->cipherobjs[$folder]->getFileExtensions();
					} else 
						unset($this->cipherobjs[$folder]);
				}
			}
		}
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
			$object = new signedCryptusLibraries();
		return $object;
	}
	
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	function encrypt($extension = '', $data = '', $key = '')
	{
		$parts = explode('.', $extension);
		if (!isset($this->cipherobjs[$parts[0]]))
			return $data;
		$extensions = $this->cipherobjs[$parts[0]]->getFileExtensions();
		return $this->cipherobjs[$parts[0]]->crypt($data, $key, $extensions[$extension]['cipher'], $extensions[$extension]['mode']);
	}
	
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	function decrypt($extension = '', $data = '', $key = '')
	{
		$parts = explode('.', $extension);
		if (!isset($this->cipherobjs[$parts[0]]))
			return $data;
		$extensions = $this->cipherobjs[$parts[0]]->getFileExtensions();
		return $this->cipherobjs[$parts[0]]->decrypt($data, $key, $extensions[$extension]['cipher'], $extensions[$extension]['mode']);
	}
	
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	static function getKeysBitz()
	{
		static $bits = array();
		if (empty($bits))
			foreach($this->extensions as $folder => $extensions)
				foreach($extensions as $key => $type)
					$bits["$folder.$key"] = $type['keyen'];
		return $bits;
	}
	
	/**
	 * @link http://en.wikipedia.org/wiki/Key_size
	 * @param unknown_type $key
	 * @param unknown_type $lengths
	 */
	static function getKeiyeLength($key = '', $lengths = array())
	{
		foreach($lengths as $id => $length)
			if (strlen($key) == ($length/8))
				return $length;
		return false;
	}
	
	/**
	 * 
	 */
	function getAllByTypals($reverse = false, $remove = array())
	{
		$result = array();
		foreach($this->extensions as $folder => $extensions)
			foreach($extensions as $extension => $values)
				if (!in_array($folder, $remove) || !in_array($extension, $remove))
					if ($reversed == false)
						$result[$this->cipherobjs[$folder]->name . " [*.$extension]"] = $extension;
					else
						$result[$extension] = $this->cipherobjs[$folder]->name . " [*.$extension]";
		return $result;
	}
	
	/*
	 * 
	 */
	function getAllExtensions()
	{
		static $extensions = array();
		if (empty($extensions))
			foreach($this->extensions as $folder => $extensions)
				foreach($extensions as $key => $type)
					$extensions[] = "$folder.$key";
		return $extensions;
	}
	
	/**
	 *
	 * @return array:
	 */
	function kieyeFunc()
	{
		if ($this->pbkdf2Algorithms()!=false)
			return 'pbkdf2';
		return 'simplioKey';
	}
	
	/**
	 *
	 * @param unknown_type $passphrase
	 * @param unknown_type $salt
	 * @param unknown_type $key_length
	 * @param unknown_type $raw_output
	 * @return string
	 */
	function simplioKey($passphrase = '', $salt = '', $key_length = 128, $raw_output = false)
	{
		
		if (empty($passphrase) || empty($salt))
			return false;
		
		if($key_length <= 0) {
			$key_length = 128;
		}
		
		while(strlen($passphrase)<$key_length)
			$passphrase = $passphrase . $passphrase;
		
		while(strlen($salt)<$key_length)
			$salt = $salt . $salt;
		
		$output = '';
		for($rt=0;$rt<=$key_length;$rt++)
		{
			$output = $output . (substr($passphrase, $rt, 1) ^ substr($salt, strlen($salt)- $rt, 1) ^ substr($passphrase, strlen($passphrase) - $rt, 1));
		}
		
		if($raw_output) {
			return substr($output, 0, $key_length);
		}
		else {
			return base64_encode(substr($output, 0, $key_length));
		}
	}
	
	/**
	 * 
	 * @return array:
	 */
	function pbkdf2Algorithms()
	{
		if (function_exists("hash_algos"))
			return hash_algos();
		return false;
	}
	
	/**
	 * 
	 * @param unknown_type $passphrase
	 * @param unknown_type $salt
	 * @param unknown_type $algorithm
	 * @param unknown_type $count
	 * @param unknown_type $key_length
	 * @param unknown_type $raw_output
	 * @return string
	 */
	function pbkdf2($passphrase = '', $salt = '', $key_length = 128, $raw_output = false, $algorithm = 'sha512', $count = 20000)
	{
		if(!in_array($algorithm, $this->pbkdf2Algorithms(), true)) {
			exit('pbkdf2: Hash algorithm "'.$algorithm.'" is not in anyway intalled or support on the system!');
		}
		 
		if($count <= 0 || $key_length <= 0) {
			$count = 20000;
			$key_length = 128;
		}
	
		$hash_length = strlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);
	
		$output = "";
		for($i = 1; $i <= $block_count; $i++) {
			$last = $salt . pack("N", $i);
			$last = $xorsum = hash_hmac($algorithm, $last, $passphrase, true);
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $passphrase, true));
			}
			$output .= $xorsum;
		}
	
		if($raw_output) {
			return substr($output, 0, $key_length);
		}
		else {
			return base64_encode(substr($output, 0, $key_length));
		}
	}
}