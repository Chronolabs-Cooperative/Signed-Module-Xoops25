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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'cryptus' . DIRECTORY_SEPARATOR . 'cryptus.php';

class signedCryptus extends XoopsObject
{
	
	/**
	 *
	 */
	function __construct()
	{
		
	}
	
	/**
	 *
	 */
	function __destruct()
	{
	
	}
}

class signedCryptusHandler  extends XoopsPersistableObjectHandler
{

	var $cryptolibs = NULL;
	
	/**
	 *
	 */
	function __construct($db)
	{
		parent::__construct($db, "", "", "", '');
		$this->cryptolibs = signedCryptusLibraries::getInstance();
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
			$object = new signedCryptusHandler();
		return $object;
	}
		
	
	/**
	 *
	 * @return Ambigous <NULL, signedProcessess>
	 */
	static function getRandomExtension()
	{
		$keys = array_keys($_SESSION['signed']['ciphers']);
		return $_SESSION['signed']['ciphers'][$keys[mt_rand(0, count($keys)-1)]];
	}
	
	/**
	 * 
	 * @param unknown_type $reverse
	 * @param unknown_type $remove
	 */
	function getAllByTypals($reverse = false, $remove = array())
	{
		return $this->cryptolibs->getAllByTypals($reverse, $remove);
	}
	
	/**
	 * 
	 * @param unknown_type $length
	 * @param unknown_type $microtime
	 */
	static function generateSalts($length = 256, $microtime = 0)
	{
		if ($microtime == 0)
			$microtime = microtime(true);
		mt_srand(mt_rand(-$microtime, 7096*$microtime));
		mt_srand(mt_rand(-$microtime, 4096*$microtime));
		mt_srand(mt_rand(-$microtime, 5096*$microtime));
		mt_srand(mt_rand(-$microtime, 5096*$microtime));
		mt_srand(mt_rand(-$microtime, 5696*$microtime));
		mt_srand(mt_rand(-$microtime, 7096*$microtime));
		$salt = '';
		while (strlen($salt)<$length)
		{
			$salt = chr(mt_rand(57, 129)) . $salt;
		}
		return $salt;
	}
	
	/**
	 * 
	 * @param unknown_type $filename
	 */
	static function writeBlowfishSalts($filename = '', $salt = '')
	{
		if (file_exists($filename) && !empty($salt))
		{
			mt_srand(microtime(true)/mt_rand(1024, 9096*512));
			mt_srand(microtime(true)/mt_rand(1024, 9096*512));
			mt_srand(microtime(true)/mt_rand(1024, 9096*512));
			$parts = explode("\n",file_get_contents($filename));
			foreach($parts as $key => $value)
			{
				if (strpos($value, 'SIGNED_BLOWFISH_WHEN') && strpos($value, "%%%%%%%%%%%%%%%%%%%%%"))
					$parts[$key] = str_replace("%%%%%%%%%%%%%%%%%%%%%", microtime(true), $value);
				elseif (strpos($value, 'SIGNED_BLOWFISH_SALT') && strpos($value, "%%%%%%%%%%%%%%%%%%%%%"))
					$parts[$key] = str_replace("%%%%%%%%%%%%%%%%%%%%%", $salt, $value);
				elseif (strpos($value, 'SIGNED_BLOWFISH_HOST') && strpos($value, "%%%%%%%%%%%%%%%%%%%%%"))
					$parts[$key] = str_replace("%%%%%%%%%%%%%%%%%%%%%", XOOPS_URL, $value);
			}
			chmod($filename, 0777);
			unlink($filename);
			$ctt = fopen($filename, "w");
			fwrite($ctt, $buffer = implode("\n", $parts), strlen($buffer));
			fclose($ctt);
			chmod($filename, 0422);
		}
	}
}