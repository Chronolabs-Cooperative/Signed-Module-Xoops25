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
 * @subpackage		language
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

	// Table headers
	define('_SIGNED_AM_STATE', 'State');
	define('_SIGNED_AM_TYPE', 'Type');
	define('_SIGNED_AM_SERIAL', 'Serial number');
	define('_SIGNED_AM_WHOM', 'Owner');
	define('_SIGNED_AM_KILOBYTES', 'Size (kbs)');
	define('_SIGNED_AM_SAVED', 'When saved');
	define('_SIGNED_AM_ISSUED', 'When issued');
	define('_SIGNED_AM_EXPIRES', 'When expires');
	define('_SIGNED_AM_EXPIRING', 'When expired');
	define('_SIGNED_AM_EXPIRED_ZERO', 'Still hasn\'t');
	define('_SIGNED_AM_EXPIRES_ZERO', 'Never expires');
	define('_SIGNED_AM_USED_ZERO', 'No signing yet!');
	define('_SIGNED_AM_ISSUED_ZERO', 'Tasks remaining!');
	define('_SIGNED_AM_IDENTITY', 'Event ID');
	define('_SIGNED_AM_EVENT_TYPE', 'Event Type');
	define('_SIGNED_AM_BEGAN', 'Event began');
	define('_SIGNED_AM_RANFORMS', 'Event ran (ms)');
	define('_SIGNED_AM_USER', 'Event User');
	define('_SIGNED_AM_USER_NONE', 'Anonymous');
	define('_SIGNED_AM_SIBBLINGS', 'Events in Group');
	define('_SIGNED_AM_COMMENT', 'Event comment');
	define('_SIGNED_AM_SYSTEM', 'System Type');
	
	// Dashboard
	define('_SIGNED_AM_DASHBOARD', 'Signature(s) Binding Totals');
	define('_SIGNED_AM_PROGRESS', 'Signatures to go active: %s');
	define('_SIGNED_AM_ACTIVE', 'Signatures are active: %s');
	define('_SIGNED_AM_INACTIVE', 'Expired Signatures: %s');
	define('_SIGNED_AM_EXPIRE_NEXT_WEEK', 'Expiring next week: %s');
	define('_SIGNED_AM_EXPIRE_NEXT_FORTNIGHT', 'Expiring next 14 days: %s');
	define('_SIGNED_AM_EXPIRED', 'Expired in total: %s');
	define('_SIGNED_AM_ISSUED_LAST_WEEK', 'Issued last (7 days): %s');
	define('_SIGNED_AM_ISSUED_LAST_FORTNIGHT', 'Issued last (14 days): %s');
	define('_SIGNED_AM_CREATED_LAST_WEEK', 'Created last (7 days): %s');
	define('_SIGNED_AM_CREATED_LAST_FORTNIGHT', 'Created last (14 days): %s');
	define('_SIGNED_AM_ACCESSED_LAST_WEEK', 'Used last (7 days): %s');
	define('_SIGNED_AM_ACCESSED_LAST_FORTNIGHT', 'Used last (14 days): %s');
	define('_SIGNED_AM_TOTAL', 'Total Signatures: %s');
	
	//Footer
	define("_SIGNED_AM_ADMIN_FOOTER", '<div style="text-align: center; font-size: 145%; margin-top: 9px; padding-top: 13px; text-shadow: 2px 1px 1px rgb(193, 104, 233); border-top: 3px dashed #000;">This module is maintained by the <a target="_blank" class="tooltip" rel="external" href="http://chrono.labs.coop/" title="Visit Chronolabs Landing Page">Chronolabs Cooperative</a> inclusive with the <a target="_blank" class="tooltip" rel="external" href="http://xoops.org/" title="Visit XOOPS Community">XOOPS Community</a><br/><a href="http://www.xoops.org" rel="external"><img style="margin-top: 23px;"src="http://xoops.org/images/logo.png" alt="XOOPS" title="XOOPS"></a><br/><a href="http://labs.coop" rel="external"><img style="margin-top: 19px;"src="http://web.labs.coop/image/logo.png" alt="Chronolabs" title="Chronolabs cooperative"></a></div>');
	
	
	
	// Additions in version 2.2
	// Module Preferences
	define('_SIGNED_AM_FORM_SALTY_SALTSFOUND', 'Previous Salt found and going to recovery options, your url and email matched one on store!');
	define('_SIGNED_AM_FORM_SALTY_TRANSFIXED', 'Previous Salt transfixed in file store and recovered, your pin, url and email matched one on store!');
	define('_SIGNED_AM_FORM_SALTY_RECOVERY_FAILED', 'Previous Salt recovery failed, your pin didn\'t match the one used to encrypt the salt on store!');
	define('_SIGNED_AM_FORM_SALTY_START_BEGINNING', 'Satrting from Begining againg of Salt recovery and generation, try new email address to make a new salt!');
	define('_SIGNED_AM_FORM_SSEARCH_H1', 'Searching API\'s for existing salt for these details');
	define('_SIGNED_AM_FORM_SSEARCH_P1', 'This may take a few minutes!');
	define('_SIGNED_AM_FORM_SALPHA_H1', 'Generate and Save Blowfish Salt!');
	define('_SIGNED_AM_FORM_SALPHA_P1', 'This will generate and save the blowfish salt for your digitially signed to multified and offer honed sigularity to your encryption in signed!');
	define('_SIGNED_AM_FORM_SALPHA_H2', 'Blow Fish Salt Is Not Set');
	define('_SIGNED_AM_FORM_SALPHA_P2', 'If you ever loose this information in the file store. You will be able to recover it from the salty api in the token search of your email address and pin code which you will need to know to recover your Blowfish Salt\'s!');
	define('_SIGNED_AM_FORM_EMAIL', 'Recovery Email Address to Store Salt');
	define('_SIGNED_AM_FORM_NAME', 'Name against Salt');
	define('_SIGNED_AM_FORM_URL', 'Recovery URL Address for Store Salt');
	define('_SIGNED_AM_FORM_PIN', 'Recovery PIN Code (4 - 12 Number Only!)');
	define('_SIGNED_AM_FORM_SRECOVERY_H1', 'Recovery a discovered similar salt!');
	define('_SIGNED_AM_FORM_SRECOVERY_P1', 'A similar URL or Email came up and you have to the option if you know the pin for the blowfish salt to recover it if you are retoring data!');
	define('_SIGNED_AM_FORM_SRECOVERY_P2', 'Reoverable Blowfish Salts!');
	
?>