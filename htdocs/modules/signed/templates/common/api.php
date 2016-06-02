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
if ($_SESSION["signed"]['configurations']['htaccess'] && strpos($_SERVER["REQUEST_URI"], 'modules/'))
{
	header('Location: ' . _URL_API . '/index' . $_SESSION["signed"]['configurations']['htaccess_extension']);
	exit(0);
}
signedCanvas::getInstance()->addScript('', "$(function(){
// bind change event to select
  $('#languages').bind('change', function () {
      var url = $(this).val(); // get selected value
      if (url) { // require a URL
          window.location = url; // redirect
      }
      return false;
   });
});", 'languages');
?>
<?php $GLOBALS['pagetitle'] = 'Self Signing API Guide'; ?>
<?php if (count(signedProcesses::getInstance()->getLanguages())>1) { ?>
<center>
<font style="font-size: 1.45em;"><?php echo _CONTENT_COMMON_START_P2; ?><select name="languages" id="languages"><?php foreach(signedProcesses::getInstance()->getLanguages() as $key => $language) { ?><option value="<?php echo _URL_ROOT. '/api/?language=' .$key; ?>"<?php if (constant("_SIGNED_CONFIG_LANGUAGE") == $key) {?> selected="selected"<?php }?>><?php echo $language['title']; ?></option><?php } ?></select></font><br/><br/>
</center>
<?php } ?>
<h1 style="font-size: 269%; text-align: center;"><?php echo sprintf(_CONTENT_API_TITLE, _SIGNED_API_VERSION); ?></h1>
<h1 style="font-size: 189%; text-align: center;">~&nbsp;<?php echo (constant("_SIGNED_DISCOVERABLE")==false?constant("_NONE").'&nbsp;':"") . constant("_DISCOVER"); ?>&nbsp;~</h1>
<p style="font-size: 1.75em;"><?php echo _CONTENT_API_DESCRIPTION; ?></p>
<?php
 if (in_array('sign', $GLOBALS['api']->callKeys())) { 
?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_SIGNING); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_SIGNING, _URL_API . '/sign/'); ?></strong>.</p>
<h2 style="font-size: 123%;"><?php echo sprintf(_CONTENT_API_FIELDS, _CONTENT_API_SIGNING); ?></h2>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_SIGNING_REQUIRED_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_SIGNING_REQUIRED_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_SIGNING_SEMI_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_SIGNING_SEMI_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_SIGNING_PART_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_SIGNING_PART_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_SIGNING_CALLBACK_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_SIGNING_CALLBACK_FIELD; ?>
</blockquote>
</div>
<div height="19px;">&nbsp;</div>
<?php
	}
 if (in_array('verify', $GLOBALS['api']->callKeys())) { 
?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_ENSIGNMENT); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_ENSIGNMENT, _URL_API . '/verify/'); ?></strong>.</p>
<h2 style="font-size: 123%;"><?php echo sprintf(_CONTENT_API_FIELDS, _CONTENT_API_ENSIGNMENT); ?></h2>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_ENSIGNMENT_REQUIRED_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_ENSIGNMENT_REQUIRED_FIELD; ?>
</blockquote>
</div>
<div height="19px;">&nbsp;</div>
<?php }
 if (in_array('verification', $GLOBALS['api']->callKeys())) { 
?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_VERIFICATION); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_VERIFICATION, _URL_API . '/verification/'); ?></strong>.</p>
<h2 style="font-size: 123%;"><?php echo sprintf(_CONTENT_API_FIELDS, _CONTENT_API_VERIFICATION); ?></h2>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_VERIFICATION_REQUIRED_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_VERIFICATION_REQUIRED_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_VERIFICATION_PART_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_VERIFICATION_PART_FIELD; ?>
</blockquote>
</div>
<div height="19px;">&nbsp;</div>
<?php }
if (in_array('request', $GLOBALS['api']->callKeys())) {
	?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_REQUESTS); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_REQUESTS, _URL_API . '/request/'); ?></strong>.</p>
<h2 style="font-size: 123%;"><?php echo sprintf(_CONTENT_API_FIELDS, _CONTENT_API_REQUESTS); ?></h2>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_REQUESTS_REQUIRED_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_REQUESTS_REQUIRED_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_REQUESTS_SEMI_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_REQUESTS_SEMI_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_REQUESTS_PART_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_REQUESTS_PART_FIELD; ?>
</blockquote>
<p style="font-size: 1.09em;"><?php echo _CONTENT_API_REQUESTS_CALLBACK_TITLE; ?></p>
<blockquote>
	<?php echo _CONTENT_API_REQUESTS_CALLBACK_FIELD; ?>
</blockquote>
</div>
<div height="19px;">&nbsp;</div>
<?php }
 if (in_array('sites', $GLOBALS['api']->callKeys())) { 
?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_SITE_SERVICES); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_SITE_SERVICES, _URL_API . '/sites/'); ?></strong>.</p>
<p style="font-size: 1.09em;"><?php echo sprintf(_CONTENT_API_NONE_REQUIRED_TITLE, _CONTENT_API_SITE_SERVICES); ?></p>
</div>
<div height="19px;">&nbsp;</div>
<?php }
 if (in_array('languages', $GLOBALS['api']->callKeys())) {
?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_LANGUAGES); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_LANGUAGES, _URL_API . '/languages/'); ?></strong>.</p>
<p style="font-size: 1.09em;"><?php echo sprintf(_CONTENT_API_NONE_REQUIRED_TITLE, _CONTENT_API_LANGUAGES); ?></p>
</div>
<div height="19px;">&nbsp;</div>
<?php } ?>
<?php foreach(array('classes'=>_CONTENT_API_CLASSES, 'descriptions'=>_CONTENT_API_FIELD_DESCRIPTION, 'enumerators'=>_CONTENT_API_FIELD_ENUMERATION, 'fields'=>_CONTENT_API_SYSFIELDS, 
					'fieldtypes'=>_CONTENT_API_FIELD_TYPE, 'identifications'=>_CONTENT_API_IDENTIFICATION, 'prompts'=>_CONTENT_API_FIELD_PROMPT, 
					'providers'=>_CONTENT_API_PROVIDERS, 'signatures'=>_CONTENT_API_SIGNATURE_TYPES, 'validations'=>_CONTENT_API_FIELD_VALIDATION,
					'processes'=>_CONTENT_API_PROCESSES, 'language'=>_CONTENT_API_LANGAUGE_FILES, 'states'=>_CONTENT_API_STATES) as $path => $title) { 

if (in_array($path, $GLOBALS['api']->callKeys())) {
	?>
<div height="19px;">&nbsp;</div>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, $title); ?></h1>
<p style="font-size: 1.14em; font-weight: none;"><?php echo sprintf(_CONTENT_API_PATH, $title, _URL_API . '/'.$path.'/'); ?></p>
<h2 style="font-size: 123%;"><?php echo sprintf(_CONTENT_API_FIELDS, $title); ?></h2>
<p style="font-size: 1.09em;"><?php echo sprintf(_CONTENT_API_FUNCTION_REQUIRES, strtolower($title)); ?></p>
<blockquote>
	<?php echo _CONTENT_API_FUNCTION_FIELD; ?>
</blockquote>
</div>
<div height="19px;">&nbsp;</div>
<?php }
}
if (in_array('banned', $GLOBALS['api']->callKeys())) {
	?>
<div class="apihelp" id="apihelp">
<h1 style="font-size: 153%;"><?php echo sprintf(_CONTENT_API_FUNCTIONS, _CONTENT_API_BANNED); ?></h1>
<p style="font-size: 1.14em;"><?php echo sprintf(_CONTENT_API_PATH, _CONTENT_API_BANNED, _URL_API . '/banned/'); ?></strong>.</p>
<p style="font-size: 1.09em;"><?php echo sprintf(_CONTENT_API_NONE_REQUIRED_TITLE, _CONTENT_API_BANNED); ?></p>
</div>
<div height="19px;">&nbsp;</div>
<?php } ?>