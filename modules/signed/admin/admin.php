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
 * @subpackage		administration
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */
		
	include_once dirname(__FILE__) . '/admin_header.php';
	xoops_cp_header();
	
	$indexAdmin = new ModuleAdmin();
	
	echo $indexAdmin->addNavigation('admin.php');
	$signaturesHandler = xoops_getmodulehandler('signatures', 'signed');
	
	$indexAdmin->addInfoBox(_SIGNED_AM_DASHBOARD);
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD, "<infolabel>" ._SIGNED_AM_TOTAL. "</infolabel>", $signaturesHandler->getCount(new Criteria('1', '1')), 'Green');
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_PROGRESS. "</infolabel>", $signaturesHandler->getCount(new Criteria('state', 'progress')), 'Purple');
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_ACTIVE. "</infolabel>", $signaturesHandler->getCount(new Criteria('state', 'active')), 'Blue');
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_INACTIVE. "</infolabel>", $signaturesHandler->getCount(new Criteria('state', 'inactive'))."</infotext>", 'Orange');
	$criteria = new CriteriaCompo(new Criteria('state', 'active'));
	$criteria->add(new Criteria('expires', time(), '>='));
	$criteria->add(new Criteria('expires', time() + (3600 * 24 * 7), '<='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_EXPIRE_NEXT_WEEK. "</infolabel>", $signaturesHandler->getCount($criteria), 'Red');
	$criteria = new CriteriaCompo(new Criteria('state', 'active'));
	$criteria->add(new Criteria('expires', time(), '>='));
	$criteria->add(new Criteria('expires', time() + (3600 * 24 * 14), '<='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_EXPIRE_NEXT_FORTNIGHT. "</infolabel>", $signaturesHandler->getCount($criteria), 'Red');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('expires', 0, '>'));
	$criteria->add(new Criteria('expires', time(), '<='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_EXPIRED. "</infolabel>", $signaturesHandler->getCount($criteria), 'Red');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('issued', time(), '<='));
	$criteria->add(new Criteria('issued', time() - (3600 * 24 * 7), '>='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_ISSUED_LAST_WEEK. "</infolabel>", $signaturesHandler->getCount($criteria), 'Green');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('saved', time(), '<='));
	$criteria->add(new Criteria('saved', time() - (3600 * 24 * 7), '>='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_CREATED_LAST_WEEK. "</infolabel>", $signaturesHandler->getCount($criteria), 'Cyan');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('used', time(), '<='));
	$criteria->add(new Criteria('used', time() - (3600 * 24 * 7), '>='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_ACCESSED_LAST_WEEK. "</infolabel>", $signaturesHandler->getCount($criteria), 'Black');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('issued', time(), '<='));
	$criteria->add(new Criteria('issued', time() - (3600 * 24 * 14), '>='));	
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_ISSUED_LAST_FORTNIGHT. "</infolabel>", $signaturesHandler->getCount($criteria), 'Green');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('saved', time(), '<='));
	$criteria->add(new Criteria('saved', time() - (3600 * 24 * 14), '>='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_CREATED_LAST_FORTNIGHT. "</infolabel>", $signaturesHandler->getCount($criteria), 'Cyan');
	$criteria = new CriteriaCompo(new Criteria('1', '1'));
	$criteria->add(new Criteria('used', time(), '<='));
	$criteria->add(new Criteria('used', time() - (3600 * 24 * 14), '>='));
	$indexAdmin->addInfoBoxLine(_SIGNED_AM_DASHBOARD,  "<infolabel>" ._SIGNED_AM_ACCESSED_LAST_FORTNIGHT. "</infolabel>", $signaturesHandler->getCount($criteria), 'Black');
	
	echo $indexAdmin->renderIndex();
	
	include_once dirname(__FILE__) . '/admin_footer.php';

?>