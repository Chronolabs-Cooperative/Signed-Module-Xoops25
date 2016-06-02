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
 * @subpackage		functions
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

require_once _PATH_ROOT . _DS_ . 'class' . _DS_ . 'signedformloader.php';
require_once _PATH_ROOT . _DS_ . 'class' . _DS_ . 'wideimage' . _DS_ . 'WideImage.php';

if (!class_exists('signed_form_object')) 
{

	class signed_form_object
	{

		function getForm($mode = '', $form = '', $name = '', $title = '', $action = '', $summary = '') 
		{
			
			signedCanvas::getInstance()->addScript(_URL_JS. '/json.validation.js', '', 'json.validation.js');
			signedCanvas::getInstance()->addScript( '', 'function ValidateLengthOperations(form) {
	var params = new Array();
	$.getJSON("'.XOOPS_URL.'/modules/signed/dojsonoperations.php?" + $(\'#\'+form).serialize(), params, refreshform);
}', 'ValidateLengthOperations', array( 'type' => 'text/javascript' ) );

			$fields = signedArrays::getInstance()->returnKeyed($mode, 'getFieldsArray');
			switch($mode) {
				case 'identification':
					$identifications = signedArrays::getInstance()->returnKeyed(_SIGNATURE_MODE, 'getIdentificationsArray');
					if (count($identifications[$_SESSION["signed"]['step']]['fields'])>0) {
						foreach(array_keys($fields) as $key) {
							if (!in_array($key, $identifications[$_SESSION["signed"]['step']]['fields'])) {
								unset($fields[$key]);
							}
						}
					}
					break;
				default:
						
			}
			
			$new = false;
			if (!isset($_SESSION["signed"]['form-step-action']) || $_SESSION["signed"]['form-step-action'] != $_SESSION["signed"]['step'].'::'.$_SESSION["signed"]['action']) {
				$new = true;
				$_SESSION["signed"]['form-step-action'] = $_SESSION["signed"]['step'].'::'.$_SESSION["signed"]['action'];
			}
			
			$descriptions = signedProcesses::getInstance()->getFieldDescriptions();
			$formation = array();
			$form = new signedThemeForm($title, $name, $_SERVER['REQUEST_URI'], 'POST', true, $summary = '');
			$form->setExtra('enctype="multipart/form-data"');
			$element = array();
			foreach($fields as $name => $field) {
				$xtradesc = "";
				if (!isset($formation[$fields[$name]['type']]))
					$formation[$fields[$name]['type']] = 1;
				else
					$formation[$fields[$name]['type']]++;
				switch($fields[$name]['type'])
				{
					case 'countries':
						$element[$name] = new signedFormSelectCountry($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'dates':
						$element[$name] = new signedFormTextDateSelect($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', 15, ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'emails':
						$element[$name] = new signedFormText($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', 35, 255, ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'enumerators':
						$element[$name] = new signedFormSelectEnumerator($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''), $name);
						break;
					case 'images':
						$element[$name] = new signedFormFile($fields[$name]['title'], $mode . '-' . $name, 1024 * 1024 * 1024 * 2048);
						break;
					case 'photos':
						$element[$name] = new signedFormFile($fields[$name]['title'], $mode . '-' . $name, 1024 * 1024 * 1024 * 2048);
						$xtradesc = "<br/>Minimal Dimenions " . _SIGNED_PHOTO_WIDTH . 'x' . _SIGNED_PHOTO_HEIGHT;
						break;
					case 'logos':
						$element[$name] = new signedFormFile($fields[$name]['title'], $mode . '-' . $name, 1024 * 1024 * 1024 * 2048);
						$xtradesc = "<br/>Minimal Dimenions " . _SIGNED_LOGO_WIDTH . 'x' . _SIGNED_LOGO_HEIGHT;
						break;
					case 'months':
						$element[$name] = new signedFormSelectMonths($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'numeric':
						$element[$name] = new signedFormText($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', 35, 255, ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'strings':
						$element[$name] = new signedFormText($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', 35, 255, ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'urls':
						$element[$name] = new signedFormText($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', 35, 255, ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
					case 'years':
						$element[$name] = new signedFormSelectYears($fields[$name]['title'], $mode . '[' . $fields[$name]['name'] . ']', ($new != true && isset($_POST[$mode][$name])?$_POST[$mode][$name]:''));
						break;
				}
				
				if (isset($descriptions[$fields[$name]['name']]))
					$element[$name]->setDescription($descriptions[$fields[$name]['name']].$xtradesc);
				
				$element[$name]->setExtra(" onclick=\"javascript:ValidateLengthOperations('".$name."')\" onchange=\"javascript:ValidateLengthOperations('".$name."')\" ");
				
				if (is_object($element[$name])) {
					$form->addElement($element[$name], $fields[$name]['required']);
					if ($fields[$name]['required'] == true && !in_array($fields[$name]['type'], array("images", "photos", "logos"))) {
						$form->addElement(new signedFormHidden('fields-required['.$fields[$name]['name'].']', $fields[$name]['name']));
					} elseif ($fields[$name]['required'] == true && in_array($fields[$name]['type'], array("images", "photos", "logos"))) {
						$form->addElement(new signedFormHidden('upload-fields-required['.$fields[$name]['name'].']', $fields[$name]['name']));
					}
					$form->addElement(new signedFormHidden('fields['.$fields[$name]['name'].']', $fields[$name]['name']));
				}
			}
			$form->addElement(new signedFormHidden('fields-typal', $mode));
			$form->addElement(new signedFormHidden('prompt', $_SESSION["signed"]['action']));
			$form->addElement(new signedFormHidden('step', $_SESSION["signed"]['step']));
			$form->addElement(new signedFormHidden('stepsleft', implode(',', $_SESSION["signed"]['stepstogo'])));
			$form->addElement(new signedFormButton('', 'submit-next', 'Next Step -->', 'submit'));
			if (isset($formation['urls']))
				$form->addElement(new signedFormHidden('protector-uris-toallow', $formation['urls']));
			foreach($formation as $type => $number)
				$form->addElement(new signedFormHidden('former-number['.$type.']', $number));
			return $form->render();
		}
		
		function verify($mode = '', $variables = array(), $step = '')
		{
			
			if (isset($_POST['fields-typal']) && !empty($_POST['fields-typal']))
				$typal = $_POST['fields-typal'];
			else
				$typal = $mode;

			$fields = signedArrays::getInstance()->returnKeyed($typal, 'getFieldsArray');
			switch(typal) {
				case 'identification':
					$identifications = signedArrays::getInstance()->returnKeyed($typal, 'getIdentificationsArray');
					if (count($identifications[$_SESSION["signed"]['step']]['fields'])>0) {
						foreach(array_keys($fields) as $key) {
							if (!in_array($key, $identifications[$_SESSION["signed"]['step']]['fields'])) {
								unset($fields[$key]);
							}
						}
					}
					break;
				default:
						
			}
			
			if (isset($variables['fields-required']))
			{
				foreach($variables['fields-required'] as $key => $field)
				{
					if (!in_array($fields[$field]['type'], array('images', 'logos', 'photos')))
						if (empty($variables[$typal][$key]) || strlen($variables[$typal][$key]) == 0) {
						$GLOBALS['errors'][] = "The field titled: <em><strong>" . $fields[$key]['title'] . '</strong></em> ~ is required to continue to the next step!';
					}
				}
			}
			if (isset($variables['upload-fields-required']))
			{
				foreach($variables['upload-fields-required'] as $key => $field)
				{
					if (empty($_FILES[$typal . '-' . $key]['tmp_name']) && $_FILES[$typal . '-' . $key]['size'] == 0) {
						$GLOBALS['errors'][] = "The image/file upload field titled: <em><strong>" . $fields[$key]['title'] . '</strong></em> ~ is required to continue to the next step!';
					}
				}
			}
			
			$validations = signedArrays::getInstance()->returnKeyed($typal, "getValidationsArray");
			foreach($variables['fields'] as $key => $field)
			{
				if (isset($validations['email']) && in_array($field, $validations['email']['validation']['fields']) && !empty($variables[$typal][$field])) {
					if (!checkEmail($variables[$typal][$field])) {
						$GLOBALS['errors'][] = "The field titled: <em><strong>" . $fields[$typal][$field]['title'] . '</strong></em> ~ is required to have a valid email address, the current data in this field doesn\'t validate as proper internet email address!';
					}
					if (in_array($field, $validations['banning']['validation']['fields'])) {
						if (!signedProcesses::getInstance()->checkForBans($variables[$typal][$field], 'email')) {
							$GLOBALS['errors'][] = "The field titled: <em><strong>" . $fields[$typal][$field]['title'] . '</strong></em> ~ email address has linkages to either a domain or IP address that is banned on this system, you must change this to proceed to the next step!';
						}
					}
				} elseif (isset($validations['url']) && in_array($field, $validations['url']['validation']['fields']) && !empty($variables[$typal][$field])) {
					$variables[$typal][$field] = formatURL($variables[$typal][$field]);
					if (in_array($field, $validations['banning']['validation']['fields'])) {
						if (!signedProcesses::getInstance()->checkForBans($variables[$typal][$field], 'url')) {
							$GLOBALS['errors'][] = "The field titled: <em><strong>" . $fields[$typal][$field]['title'] . '</strong></em> ~ internet URL has linkages to either a domain or IP address that is banned on this system, you must change this to proceed to the next step!';
						}
					}	
				}
			}
			
			$fields = signedArrays::getInstance()->returnKeyed($typal, 'getFieldsArray');	
			$package = array();
			foreach($fields as $name => $field) {
				if (isset($variables[$typal][$name])) {
					if (strlen(trim($variables[$typal][$name]))) {
						switch($fields[$name]['type'])
						{
							case 'countries':
								$package[$name] = $variables[$typal][$name];
								break;
							case 'dates':
								$package[$name] = $variables[$typal][$name];
								break;
							case 'emails':
								if (($email=checkEmail($variables[$typal][$name], false)) == false) {
									$GLOBALS['errors'][$fields[$name]['name']] = "The field:~ <em>".$fields[$name]['title']."<em> has an invalid email address in it, it requires a valid email address!";
								} else {
									$package[$name] = $email;
								}
								break;
							case 'enumerators':
								$package[$name] = $variables[$typal][$name];
								break;
							case 'months':
								$package[$name] = $variables[$typal][$name];
								break;
							case 'numeric':
								if (is_numeric($variables[$typal][$name]) == false) {
									$GLOBALS['errors'][$fields[$name]['name']] = "The field:~ <em>".$fields[$name]['title']."<em> is required to be numeric, only!";
								} else {
									$package[$name] = $variables[$typal][$name];
								}
								break;
							case 'strings':
								$package[$name] = $variables[$typal][$name];
								break;
							case 'urls':
								$package[$name] = formatURL($variables[$typal][$name]);
								break;
							case 'years':
								$package[$name] = $variables[$typal][$name];
								break;
							
						}
					}
				}
			}
			
			$fields = signedArrays::getInstance()->returnKeyed($typal, 'getFieldsArray');
			foreach($fields as $name => $field) {
				switch($fields[$name]['type'])
				{
					case 'images':
					case 'photos':
						$identifications = signedArrays::getInstance()->returnKeyed($typal, 'getIdentificationsArray');
						$dimensions = signedProcesses::getInstance()->getDimensionsArray();
						$minimal = array();
						$pass=false;
						if (!empty($_FILES[$_POST['signed_upload_file'][$typal . '-' . $name]]['tmp_name']) && $_FILES[$_POST['signed_upload_file'][$typal . '-' . $name]]['size'] > 0) {
							try {
								$img = signedCiphers::getInstance()->watermarkOriginalImage(WideImage::loadFromUpload($_POST['signed_upload_file'][$typal . '-' . $name]));			
								$width = $img->getWidth();
								$height = $img->getWidth();
								foreach($dimensions['upload'] as $scape => $values) {
									foreach($values as $state => $data) {
										if ($pass==false) {
											if ($width>=$data['width'] && $height>=$data['height']) {
												$resizescape = $scape;
												$resizestate = $state;
												$pass=true;
											} else {
												if (($data['width']<$minimal[$scape]['width'] || !isset($minimal[$scape]['width']) && ($data['height']<$minimal[$scape]['height'] || !isset($minimal[$scape]['height']))))
												{
													$minimal[$scape]['width'] = $data['width'];
													$minimal[$scape]['height'] = $data['height'];
													$minimal[$scape]['display'] = $data['width'].'x'.$data['height'];
												}
											}
										}
									}
								}
								if ($pass==true) {
									$resizedimg = $img->resize($dimensions['resize'][$resizescape][$resizestate]['width'], $dimensions['resize'][$resizescape][$resizestate]['height']);
									$tmpstore = array();
									$tmpstore['identification']['data-mimetype'] = 'image/png';
									$tmpstore['identification']['data-pack'] = 'base64';
									$tmpstore['identification']['data'] = base64_encode($resizedimg->asString('png'));
									$tmpstore['identification']['md5'] = md5($package['identification']['data']);
									$tmpstore['identification']['width'] = $resizedimg->getWidth();
									$tmpstore['identification']['height'] = $resizedimg->getHeight();
									$tmpstore['identification']['points'] = $identifications[constant('_SIGNATURE_MODE')][$step]['points'];
									$tmpstore['identification']['title'] = $identifications[constant('_SIGNATURE_MODE')][$step]['title'];
									$package = "cached:" . $identity = sha1($name . json_encode($tmpstore));
									signedCache::getInstance()->write('data_'.$identity, $tmpstore, 3600 * 96);
									if (is_object($GLOBALS['logger']))
										$GLOBALS['logger']->logBytes($_FILES[$_POST['signed_upload_file'][$name]]['size'], 'uploaded');
									
									if ((isset($variables['expiry-month']) && isset($variables['expiry-year'])) && (!empty($variables['expiry-month']) && !empty($variables['expiry-year']))) 
									{
										$package['identification']['expires'] = strtotime($variables['expiry-year'].'-'.$variables['expiry-month'].'-01 00:00:01');
									} else {
										$package['identification']['expires'] = 'never';
									}
								} else {
									$GLOBALS['errors'][$fields[$name]['name']] = "The field:~ <em>".$fields[$name]['title']."<em> was the incorrect dimenions the supported minimal sized dimensions for Landscape are: ".$minimal['landscape']['display'] . " as well as for Portrait: " . $minimal['portrait']['display'] . " please rescan your image to a higher resolution and re-submit!";
								}
							}
							catch (Exception $e) {
								$GLOBALS['errors'][] = "Image upload error with the following exception: $e";
							}
						}
						break;
				
					case 'logos':
						if (!empty($_FILES[$_POST['signed_upload_file'][$typal . '-' . $name]]['tmp_name']) && $_FILES[$_POST['signed_upload_file'][$typal . '-' . $name]]['size'] > 0) {
							try {
								$img = WideImage::loadFromUpload($_POST['signed_upload_file'][$typal . '-' . $name]);			
								$width = $img->getWidth();
								$height = $img->getWidth();
			
								if ($width<_SIGNED_LOGO_WIDTH && $height<_SIGNED_LOGO_HEIGHT) {
									$GLOBALS['errors'][$fields[$name]['name']] = "The field:~ <em>".$fields[$name]['title']."<em> was the incorrect dimenions the supported minimal sized dimensions for are: "._SIGNED_LOGO_WIDTH .'x'._SIGNED_LOGO_HEIGHT . " please rescan or take your image to a higher resolution and re-submit!";
									return false;
								} 
								$resizedimg = $img->resize(_SIGNED_LOGO_WIDTH, _SIGNED_LOGO_HEIGHT);
								$tmpstore = array();
								$tmpstore['data-mimetype'] = 'image/png';
								$tmpstore['data-pack'] = 'base64';
								$tmpstore['data'] = base64_encode($resizedimg->asString('png'));
								$tmpstore['md5'] = md5($package['data']);
								$tmpstore['width'] = $resizedimg->getWidth();
								$tmpstore['height'] = $resizedimg->getHeight();
								$package = "cached:" . $identity = sha1($name . json_encode($tmpstore));
								signedCache::getInstance()->write('data_'.$identity, $tmpstore, 3600 * 96);
								if (is_object($GLOBALS['logger']))
									$GLOBALS['logger']->logBytes($_FILES[$_POST['signed_upload_file'][$name]]['size'], 'uploaded');
							}
							catch (Exception $e) {
								$GLOBALS['errors'][] = "Image upload error with the following exception: $e";
							}
						}
						break;
					
					}
			}	
			return $package;
		}
	}
}

?>
