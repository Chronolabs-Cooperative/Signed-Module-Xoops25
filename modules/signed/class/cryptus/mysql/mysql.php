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

if (!class_exists("signedCryptusLibraries"))
	die('Signed Cryptus Library handler need to be loaded first - Restricted Access!');

class signedCryptusMysql extends signedCryptusLibraries
{
	
	/**
	 *
	 * @var string
	 */
	var $filename = array("library"=>0,"mode"=>1, "bit" => 2);
	
	/**
	 *
	 * @var string
	 */
	var $seperator = '-==-';
	
	/**
	 * 
	 * @var string
	 */
	var $encryptsql = "SELECT %s(\"%s\", \"%s\") as `encrypt`";
	
	/**
	 *
	 * @var string
	 */
	var $decryptsql = "SELECT %s(\"%s\", \"%s\") as `decrypt`";
	
	/**
	 *
	 * @var string
	 */
	var $name = "PHP MySQLi Lib";
		
	/**
	 *
	 * @var string
	 */
	var $phplibry = array("mysqli");
	
	/**
	 *
	 * @var string
	 */
	var $phpfuncs = array("");
	
	
	/**
	 *
	 * @var string
	 */
	var $filetype = '';
	
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
	
	/**
	 *
	 * @return Ambigous <NULL, signedCryptusAesctr>
	 */
	static function getInstance()
	{
		ini_set('display_errors', true);
		error_reporting(E_ERROR);
	
		static $object = NULL;
		if (!is_object($object))
			$object = new signedCryptusMysql();
		return $object;
	}
	
	/**
	 * 
	 * @return multitype:string
	 */
	function getAlgorithms()
	{
		return array(basename(__DIR__)	=>	array(	'encode'=>array("encrypt"=>"ENCODE","decrypt"=>"DECODE")));
	}
	
	/**
	 * 
	 * @return multitype:multitype:number string
	 */
	function getFileExtensions()
	{
		return array(		basename(__DIR__) . '.encode' => array("keyen"=>128, "salt" => SIGNED_BLOWFISH_SALT));
	}
	
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	function setFiletype($bitz = 0, $cipher = '',$mode = '')
	{
		if (!empty($bitz) && !empty($cipher))
			foreach($this->getFileExtensions() as $filetype => $values)
				if ($bitz == $values["keyen"])
					return $this->filetype = $filetype;
		return $this->filetype;
	}
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	private function getMode($bitz = 128)
	{
		foreach($this->getFileExtensions() as $ext => $values)
			if ($values['keyen'] == $bitz)
				return str_replace(dirname(__DIR__) . '.', '', $ext);
		return false;
	}
	
	/**
	 *
	 * @return multitype:multitype:number string
	 */
	private function getKeyBitz($cipher = '',$mode = '')
	{
		return parent::getKeyBitz($this->getFileExtensions());
	}	
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $key
	 * @param unknown_type $bitz
	 * @return boolean
	 */
	function crypt($data = '', $key = '', $cipher = '',$mode = '')
	{
		if (($bitz = parent::getKeiyeLength($key, array_key($this->getKeyBitz($cipher, $mode))))>0)
		{
			$algorithms = $this->getAlgorithms();
			if ($result = $GLOBALS['xoopsDB']->queryF(sprintf($encryptsql, $algorithms[dirname(__DIR__)][$this->getMode($bitz)]["encrypt"], $GLOBALS['xoopsDB']->escape($data), $GLOBALS['xoopsDB']->escape($key))))
			{
				list($return) = $GLOBALS['xoopsDB']->fetchRow($result);	
				setFiletype($bitz, $cipher, $mode);
				return $return;	
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $key
	 * @param unknown_type $bitz
	 * @return boolean
	 */
	function decrypt($data = '', $key = '', $cipher = '',$mode = '')
	{
		if (($bitz = parent::getKeiyeLength($key, array_key($this->getKeyBitz($cipher, $mode))))>0)
		{
			$algorithms = $this->getAlgorithms();
			if ($result = $GLOBALS['xoopsDB']->queryF(sprintf($decryptsql, $algorithms[dirname(__DIR__)][$this->getMode($bitz)]["decrypt"], $GLOBALS['xoopsDB']->escape($data), $GLOBALS['xoopsDB']->escape($key))))
			{
				list($return) = $GLOBALS['xoopsDB']->fetchRow($result);	
				setFiletype($bitz, $cipher, $mode);
				return $return;	
			}
		}
		return false;
	}
}

?>