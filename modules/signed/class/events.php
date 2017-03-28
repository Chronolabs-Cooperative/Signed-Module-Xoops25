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


class SignedEvents extends XoopsObject
{
    /**
     *
     */
    function __construct()
    {
        $this->initVar('eventid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('system', XOBJ_DTYPE_TXTBOX);
        $this->initVar('type', XOBJ_DTYPE_TXTBOX);
        $this->initVar('comment', XOBJ_DTYPE_OTHER);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('began', XOBJ_DTYPE_INT);
        $this->initVar('micro', XOBJ_DTYPE_INT);
        $this->initVar('log_storage', XOBJ_DTYPE_TXTBOX);
        $this->initVar('log_path', XOBJ_DTYPE_TXTBOX);
        $this->initVar('log_file', XOBJ_DTYPE_TXTBOX);
    }


    function getNumberSibblings()
    {
    	 $linksHandler = xoops_getmodulehandler('event_links', 'signed');
    	 $links = $linksHandler->getObjects(new Criteria('eventid', $this->getVar('eventid')), false);
    	 if (is_object($links[0]))
    	 {
    	 	return $linksHandler->getCount(new Criteria('group', $links[0]->getVar('group')));
    	 }
    	 return '0';
    }
    
    function getAdminTableItem()
    {
    	static $users = array(); 
    	$memberHandler = xoops_gethandler('member');
    	$result = self::getValues(array('eventid', 'system', 'type', 'comment', 'uid', 'began', 'micro'));
    	$result['kbs'] = floor($result['bytes'] / 1024);
    	$result['whom'] = $result['entity'] . (!empty($result['entity'])?" :: ":"") . $result['name'];
    	if ($result['began']==0)
    		$result['began'] = constant("_SIGNED_AM_ISSUED_ZERO");
    	else
    		$result['began'] = date('Y-m-d H:i', $result['began']);
    	if ($result['uid']==0)
    		$result['uid'] = constant("_SIGNED_AM_USER_NONE");
    	else {
    		if (!isset($user[$result['uid']]))
    			$user[$result['uid']] = $memberHandler->getUser($result['uid']);
    		if (is_object($user[$result['uid']]))
    			$result['uid'] = "<a href='" . XOOPS_URL . "/userinfo.php?uid=" . $result['uid'] ."'>" . $user[$result['uid']]->getVar('uname') . "</a>";
    		else
    			$result['uid'] = "unknown user: " . $result['uid'];
    	}
    	$result['sibblings'] = $this->getNumberSibblings();
    	return $result;
    }
}

/**
 * 
 * @author sire
 *
 */
class SignedEventsHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|object $db
     */
    function __construct(&$db)
    {
    	if (!isset($GLOBALS['signedBoot']))
    		$GLOBALS['signedBoot'] = microtime(true);
    	
        parent::__construct($db, "signed_events", "SignedEvents", "eventid", 'serial');
        if (!isset($_SESSION["signed"]['eventgroup'])||empty($_SESSION["signed"]['eventgroup']))
        {
        	$_SESSION["signed"]['eventgroup'] = sha1(session_id().$_SERVER["REMOTE_ADDR"].microtime(false));
        }
        $GLOBALS['eventCurrent'] = $this->create();
    }
    
    function create()
    {
    	$ret = parent::create();
    	$ret->setVar('begun', $GLOBALS['signedBoot']);
    	if (defined('_SIGNED_EVENT_SYSTEM'))
    		$ret->setVar('system', constant('_SIGNED_EVENT_SYSTEM'));
    	if (defined('_SIGNED_EVENT_TYPE'))
    		$ret->setVar('type', constant('_SIGNED_EVENT_TYPE'));
    	if (is_object($GLOBALS['xoopsUser']))
    		$ret->setVar('type', $GLOBALS['xoopsUser']->getVar('uid'));
    	return $ret;
    }
    
    function insert($object, $force = true)
    {
    	$object->setVar('micro', microtime(true) - $GLOBALS['signedBoot']*1000);
    	$eventid = parent::insert($object, $force);
    	$elinks = xoops_getmodulehandler('event_links', 'signed');
    	$link = $elinks->create();
    	$link->setVar('eventid', $eventid);
    	$link->setVar('group', $_SESSION["signed"]['eventgroup']);
    	$link->setVar('when', time());
    	if (isset($_SESSION["signed"]['signedSignature'][$_SESSION["signed"]['signedSignature']['current']]))
    		$link->setVar('signid', $_SESSION["signed"]['signedSignature'][$_SESSION["signed"]['signedSignature']['current']]->getVar('signid'));
    	$elinks->insert($link, true);
    	$GLOBALS['eventCurrent'] = $eventsHandler->create();
    	return $eventid;
    }
    

    function getAdminTabled($start = 0, $limit= 42)
    {
    	$criteria = new Criteria('began', '0', '>');
    	$criteria->setLimit($limit);
    	$criteria->setStart($start);
    	$criteria->setSort('`began`');
    	$criteria->setOrder('DESC');
    	 
    	$result = array();
    	foreach(self::getObjects($criteria, true) as $key => $object)
    	{
    		$result[$key] = $object->getAdminTableItem();
    	}
    	return $result;
    }
}
