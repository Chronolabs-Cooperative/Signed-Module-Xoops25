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
?><?php 
	
	$serial = isset($_GET['serial'])?$_GET['serial']:md5(NULL);
	$key = isset($_GET['key'])?$_GET['key']:md5(NULL);
	$hash = isset($_GET['hash'])?$_GET['hash']:'';
	
	if ($GLOBALS['io'] = signedStorage::getInstance(_SIGNED_RESOURCES_STORAGE)) {
		$array = $GLOBALS['io']->load(_PATH_REPO_VALIDATION, $serial);
		if ($array['verification']['verified'] == false) {
			if ($array['verification']['mobiles'][$key]['verified'] == false) {
				if ($array['verification']['mobiles'][$key]['key'] == $hash) {
					$array['verification']['mobiles'][$key]['verified'] = true;
					$array['verification']['mobiles'][$key]['verified-when'] = microtime(true);
					$array['verification']['mobiles'][$key]['verified-ip'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/".signedSecurity::getInstance()->getIP(true)."/json.api"), true);
					?><h1>Mobile Verified</h1><p style="font-size:1.376em;">The Mobile address: <strong><?php echo $array['verification']['mobiles'][$key]['to']?></strong> has been verified!</p><?php
					redirect_header(XOOPS_URL . '/modules/signed/', 10, 'The Mobile address: <strong>'. $array['verification']['mobiles'][$key]['to'] .'</strong> has been verified!');
				}	
			} else {
				$GLOBALS['errors']['mobile-done'] = "The mobile number of '".$array['verification']['mobiles'][$key]['number']."' in this Signature corresponding to the serial number:~ " . $serial . ' - has already been verified!';
				redirect_header(XOOPS_URL . '/modules/signed/', 10, $GLOBALS['errors']['mobile-done']);
			}
			$verified = true;
			if (is_array($array['verification']['mobiles']) && isset($array['verification']['mobiles']))
				foreach($array['verification']['mobiles'] as $mobile) {
					if ($mobile['verified']==false)
						$verified = false;
				}
			if (is_array($array['verification']['mobiles']) && isset($array['verification']['mobiles']))
				foreach($array['verification']['mobiles'] as $Mobile) {
					if ($Mobile['verified']==false)
						$verified = false;
				}
			$array['verification']['verified'] = $verified;
			
			$GLOBALS['io']->save($array, _PATH_REPO_VALIDATION, $serial);
			
			if ($verified==true) {
				echo "<div style=\"font-size: 1.89711em; text-align: center; margin 15px; color: rgb(90,210,197);\">Sending Signature via Mobile!</div>";
				signedPackages::getInstance()->sendSignatureMobile($serial);
				redirect_header(XOOPS_URL . '/modules/signed/', 10, 'Sending Signature via Mobile!');
			}
		} else {
			redirect_header(XOOPS_URL . '/modules/signed/', 10, "This Signature corresponding to the serial number:~ " . $serial . ' - has been completely verified!');
		}
	} else {
		$GLOBALS['errors']['no-serial'] = "There is no Signature corresponding to the serial number:~ " . $serial;
		redirect_header(XOOPS_URL . '/modules/signed/', 10, $GLOBALS['errors']['no-serial']);
	}
?>
