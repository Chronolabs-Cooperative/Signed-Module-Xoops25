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
 * @subpackage		module
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

/**
 * CronJob/Scheduled Task is Run Once A Day!
 */
define('_SIGNED_CRON_EXECUTING', microtime(true));
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'common.php';

$GLOBALS['io'] = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE);

if (is_dir($dir = _PATH_CALENDAR_EXPIRY . _DS_ . date('Y') . _DS_ . date('m'))) {
	foreach(signedLists::getFileListAsArray($dir) as $file) {
		$expires = $GLOBALS['io']->load($dir, str_replace($GLOBALS['io']->_extensions, "", $file));
		if (isset($expires['expiry'])) {

			// Expire Permantly
			$verification = $GLOBALS['io']->load(_PATH_REPO_VALIDATION, $expires['expires']['serial-number']);
			$verification['verification']['expired'] == true;
			$GLOBALS['io']->save($verification, _PATH_REPO_VALIDATION, $expires['expires']['serial-number']);			
			signed_lodgeCallbackSessions($expires['expires']['serial-number'], 'expired');
			
			$resource  = $GLOBALS['io']->load(_PATH_REPO_SIGNATURES, $expires['expires']['serial-number']);
			$identifications = returnKey($resource['resources']['signature']['signature']['type'], 'signed_getIdentificationsArray');
			
			$mailer = signed_getMailer();
			$mailer->setTemplateDir(_PATH_ROOT . _DS_ . 'language' . _DS_ . _SIGNED_CONFIG_LANGUAGE  . _DS_ . 'mail_template');
			
			switch($resource['resources']['signature']['signature']['type']) {
				case 'personal':
					$to 	= 	array(	'name'	=>	$resource['resources']['signature']['personal']['name'],
					'email'	=>	$resource['resources']['signature']['personal']['email']);
					break;
				case 'entity':
					$to 	= 	array(	0 =>	array(	'name'	=>	$resource['resources']['signature']['personal']['name'],
					'email'	=>	$resource['resources']['signature']['personal']['email']),
					1 =>	array(	'name'	=>	$resource['resources']['signature']['personal']['name'],
					'email'	=>	$resource['resources']['signature']['entity']['entity-email']));
					break;
			}
			
			if ($array['resources']['signature']['signature']['expiry']['when']!='never')
				$data['SIGNED_EXPIRED'] = date('Y-m-d H:i:s', $array['resources']['signature']['signature']['expiry']['when']);
			else 
				$data['SIGNED_EXPIRED'] = 'Never Expires!';
			$data['SERIAL_NUMBER'] = $expires['expiry']['serial-number'];
			$data['PERSON_FOR'] = $resource['resources']['signature']['signature']['signer']['name'];
			$data['PERSON_BY'] = $resource['resources']['signature']['signature']['signee']['name'];
			
			$body = $mailer->getBodyFromTemplate('expired-signature', $data, true);
			
			if ($mailer->sendMail($to, array(), array(), 'Expired Digital Signature for '.$resource['resources']['signature']['signature']['signer']['name'], $body['body'], array(), array(), $body['isHTML'])) {						
				unlink($dir . _DS_ . $file);
			}
		}
	}
}
?>