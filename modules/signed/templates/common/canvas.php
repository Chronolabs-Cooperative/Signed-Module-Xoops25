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

	signedCanvas::getInstance()->addStylesheet( _URL_CSS . "/signed.css" );
	signedCanvas::getInstance()->addStylesheet(  _URL_CSS . "/style.css" );
	signedCanvas::getInstance()->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js',  array("type"=>"text/javascript"));

		if (defined('_SIGNED_ERRORS') || isset($GLOBALS['error'])) { ?>
		<div id="errors">
			<h1>Error(s) that have occured:~</h1>
			<?php echo (defined('_SIGNED_ERRORS')?constant('_SIGNED_ERRORS'):isset($GLOBALS['error'])?$GLOBALS['error']:''); ?>
		</div>
		<?php } ?>
		<?php if (strlen(defined('_SIGNED_CANVAS')?constant('_SIGNED_CANVAS'):isset($GLOBALS['canvas'])?$GLOBALS['canvas']:'')>0) { ?>
		<div id="canvas">
			<div style="width: 100%; min-height: 450px;">
				<?php echo (defined('_SIGNED_CANVAS')?constant('_SIGNED_CANVAS'):isset($GLOBALS['canvas'])?$GLOBALS['canvas']:''); ?>
			</div>
			<div class="signed-canvas-footer" id="canvas-footer" style="position: relative; vertical-align: bottom;">
				<div style="position: relative; text-align: center; clear: none; margin-top: 0px; margin-bottom: 12px; vertical-align: bottom;">
					<form style="clear: none;" name="entity-reset" method="post" action="<?php echo _URL_ROOT; ?>/=reset=/">
						<input style="clear: none;  margin-top: 0px; margin-bottom: 0px;" type="submit" name="submit-reset" value="<?php echo _CONTENT_BUTTON_RESET; ?>">
					</form>
				</div>	
				<div style="width: 100%;" style="position: relative; top: 23px;">
					<div style="float: right;">
						<?php echo sprintf(_CONTENT_COMMON_START_DIV2, (constant("_SIGNED_DISCOVERABLE")==false?'<strong>':"").(constant("_SIGNED_DISCOVERABLE")==false?constant("_SIGNED_NOT_DISCOVERABLE").'&nbsp;':"") . constant("_SIGNED_IS_DISCOVERABLE").(constant("_SIGNED_DISCOVERABLE")==false?'</strong>':""), signedSecurity::getInstance()->getHostCode()); ?></strong>
					</div>
					<div style="float: Left; clear:none;">
						<?php echo sprintf(_CONTENT_COMMON_START_DIV1, constant('_SIGNED_VERSION')); ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<div id="footer">
			<strong>Copyright <?php echo defined('_SIGNED_CONFIG_COPYRIGHT')?htmlspecialchars(constant('_SIGNED_CONFIG_COPYRIGHT')):"&copy " .  date('Y') . " - Chronolabs Cooperative"; ?></strong><br/>
			<strong><a href="<?php echo XOOPS_URL; ?>/modules/signed/docs">System Documentation</a>&nbsp;|&nbsp;<a target="_blank" href="<?php echo _URL_API; ?>"><?php echo sprintf(_CONTENT_API_TITLE, _SIGNED_API_VERSION); ?></a><br/><br>
			</strong><a href="https://sourceforge.net/projects/chronolabs/files/Encryption/Digital%20Signatures/" target="_blank">Source Code for <em>Digital Self Signed</em></a> ~ This was written entirely on <a href="http://releases.ubuntu.com" target="_blank">@Ubuntu</a> fro linux!</strong>
			<br/><a href="http://www.linkedin.com/in/founderandprinciple" style="text-decoration:none;"><span style="font: 80% Arial,sans-serif; color:#0783B6;"><img src="http://s.c.dcdn.licdn.com/scds/common/u/img/webpromo/btn_in_20x15.png" width="20" height="15" alt="View Simon Roberts's LinkedIn profile" style="vertical-align:middle" border="0">View Author's Linked-in Profile</span></a>
			<br /><br/>
			<strong><a href="https://chrono.labs.coop">Chronolabs Cooperative</a></strong><br/><a href="https://web.labs.coop/public/legal/privacy-and-mooching-policy/22,3.html">Privacy & Mooching Policy</a>&nbsp;|&nbsp;<a href="https://web.labs.coop/public/legal/general-terms-and-conditions/12,3.html">Terms &amp; Conditions</a>&nbsp;|&nbsp;<a href="https://web.labs.coop/public/legal/end-user-license/11,3.html">End User License</a>
		</div>