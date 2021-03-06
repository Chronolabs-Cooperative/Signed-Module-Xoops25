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
 * @subpackage		module
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

$GLOBALS['canvas'] = ob_get_clean();
if (!defined('_SIGNED_CANVAS'))
	define('_SIGNED_CANVAS', $GLOBALS['canvas']);

if (isset($GLOBALS['errors']) && is_array($GLOBALS['errors']) && count($GLOBALS['errors'])>0) {
	$GLOBALS['error'] = "<ol><li>" . implode("</li><li>", $GLOBALS['errors']) . "</li></ol>";
	if (!defined('_SIGNED_ERRORS'))
		define('_SIGNED_ERRORS', $GLOBALS['error']);
	if (is_object($GLOBALS['logger']))
		$GLOBALS['logger']->logBytes(strlen($GLOBALS['error']), 'html-canvased');	
}
if ($GLOBALS['logger'] = signedLogger::getInstance())
	$GLOBALS['logger']->logBytes(strlen($GLOBALS['canvas']), 'html-canvased');

ob_start();
include _PATH_TEMPLATES . _DS_ . 'common' . _DS_ . 'canvas.php';
$buffer = ob_get_clean();

if ($GLOBALS['logger'] = signedLogger::getInstance())
	$GLOBALS['logger']->logBytes(strlen($buffer), 'html-buffered');
$GLOBALS['xoopsTpl']->assign('xodes', $buffer);
$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', (isset($GLOBALS['pagetitle'])&&!empty($GLOBALS['pagetitle'])?$GLOBALS['pagetitle'] . " :: " ._SIGNED_TITLE:_SIGNED_TITLE));
$GLOBALS['xoopsTpl']->display("db:signed_wrapper.html");
include $GLOBALS['xoops']->path('/footer.php');

exit(0);
?>