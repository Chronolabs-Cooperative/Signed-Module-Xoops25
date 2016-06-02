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
 * @subpackage		templates
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */
?><?php $GLOBALS['pagetitle'] = _CONTENT_HELP_ENTITY_FINISH_PT; ?>
<p style="font-size: 1.35em;"><?php echo _CONTENT_HELP_ENTITY_FINISH_P1; ?></p>
<?php 
	$serialnum = (isset($_GET['serial'])?$_GET['serial']:md5(NULL));
	if (signedStorage::getInstance()->file_exists(_PATH_REPO_VALIDATION, $serialnum)) {
		$verifier = signedStorage::getInstance()->load(_PATH_REPO_VALIDATION, $serialnum);
if (isset($verifier['verification']['emails']) && !empty($verifier['verification']['emails']) && constant("_SIGNED_VERIFY_EMAIL") == true) {
		print_r($verifier['verification']['emails']);
		
		if (count($verifier['verification']['emails'])> 0 ) {
?><h1><?php echo _CONTENT_HELP_ENTITY_FINISH_H1A; ?></h1>
<ol>
<?php 
			foreach($verifier['verification']['emails'] as $key => $email) {
if (!empty($email['to'])) { ?>	<li><?php echo sprintf(_CONTENT_HELP_ENTITY_FINISH_LIA, $email['to'], ($email['verified']==false?_CONTENT_HELP_ENTITY_STATE_1B:_CONTENT_HELP_ENTITY_STATE_1A)); ?></li>
<?php } } ?>
</ol>
<?php 
}
if (isset($verifier['verification']['mobiles']) && !empty($verifier['verification']['mobiles']) && constant("_SIGNED_VERIFY_MOBILE") == true) {
		if (count($verifier['verification']['mobiles'])> 0 ) {
?><h1><?php echo _CONTENT_HELP_ENTITY_FINISH_H1B; ?></h1>
<ol>
<?php 
			foreach($verifier['verification']['mobiles'] as $key => $mobile) {
if (!empty($mobile['number'])) {?>	<li><?php echo sprintf(_CONTENT_HELP_ENTITY_FINISH_LIB, $mobile['number']); ?></li>
<?php } } ?>
</ol>
<?php } }
	} } ?>
<?php signedPrompts::getInstance()->goReset(); ?>
