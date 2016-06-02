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
 * @subpackage		mailer
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */

defined('_PATH_ROOT') or die('Restricted access');

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signedstorage.php';

class signedLogger {

	/**
	 * 
	 */
	var $_io = '';
	
	/**
	 *
	 * @var unknown
	 */
	var $_last_asset_id = '';
	
	/**
	 * 
	 * @var unknown
	 */
	var $_log_files = array();

	/**
	 *
	 * @var unknown
	 */
	var $_base_node = 'signed';
	
	/**
	 *
	 */
	var $_scope = array(	'delivery'	=>	array(		'default' => array('path' => '_PATH_LOGS_DELIVERY', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d')),
							'emails'		=>	array(	'default' => array('path' => '_PATH_LOGS_EMAILS', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d'),
														'reminders' => array('path' => '_PATH_LOGS_EMAILS_REMINDERS', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d'),
														'notices' => array('path' => '_PATH_LOGS_EMAILS_NOTICES', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d'),
														'delivery' => array('path' => '_PATH_LOGS_EMAILS_DELIVERY', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d'),
														'verify' => array('path' => '_PATH_LOGS_EMAILS_VERIFY', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d')),
							'errors'	=>	array(		'default' => array('path' => '_PATH_LOGS_ERRORS', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d')),
							'execution'=>	array(		'default' => array('path' => '_PATH_LOGS_EXECUTION', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d')),
							'polling'	=>	array(		'default' => array('path' => '_PATH_LOGS_POLLING', 'filename'=> 'signed-log--%s.json', 'dating'=>'Y/m/d')));
	
	/**
	 * 
	 */
	var $_statistics = array();
	
	/**
	 *
	 * @var unknown
	 */
	var $_file_paths = array();


	/**
	 *
	 */
	var $_logs = array();
	
	/**
	 * 
	 */
	var $_packages_fingerprint = array();
	
	/**
	 * 
	 */
	var $_instance_number = 1;

	/**
	 *
	 */
	var $_file_error_log = '';
	
	/**
	 * 
	 */
	function __construct()
	{
		$this->_io = signedStorage::getInstance(_SIGNED_LOGS_STORAGE);
		$this->preparePaths();
		$this->prepareErrorReporting();
		$this->prepareFilenames();
		$this->prepareStatistics();
		$this->prepareLogs();
		$this->prepareTracking();
	}

	/**
	 * 
	 */
	static function getInstance()
	{
		ini_set('display_errors', true);
		error_reporting(E_ERROR);
		
		static $object = "";
		if (!isset($object))
			$object = new signedLogger();
		return $object;
	}
	/**
	 * 
	 */
	private function prepareErrorReporting()
	{
		if (!is_dir($this->_file_paths['errors']['default']))
			mkdir($this->_file_paths['errors']['default'], 0777, true);
		ini_set('error_log', $this->_file_error_log = $this->_file_paths['errors']['default'] . DIRECTORY_SEPARATOR . date('H-Y-m-d'). '--error-log.txt');
		ini_set('log_errors', _SIGNED_ERRORS_LOGGED);
		ini_set('display_errors', _SIGNED_ERRORS_DISPLAYED);
		error_reporting(_SIGNED_ERRORS_REPORTING);
	}

	/**
	 *
	 */
	private function getIPData($key = "", $ip = '')
	{
		static $ret = array();
		if (empty($ip))
			$ip = signedSecurity::getInstance()->getIP(true);
		if (empty($ret[$ip])) {
			if (!isset($_SESSION["signed"]['ip-data'][$ip]) || empty($_SESSION["signed"]['ip-data'][$ip])) {
				$_SESSION["signed"]['ip-data'][$ip]['ip'] = $ip;
				$_SESSION["signed"]['ip-data'][$ip]['netbios'] = gethostbyaddr($ip);
				$_SESSION["signed"]['ip-data'][$ip]['lookups'] = json_decode(signedArrays::getFileContents("http://lookups.labs.coop/v1/country/$ip/json.api"), true);
				$_SESSION["signed"]['ip-data'][$ip]['country'] = (empty($_SESSION["signed"]['ip-data'][$ip]['lookups']['country']['name'])?"unknown":$_SESSION["signed"]['ip-data'][$ip]['lookups']['country']['name']);
				$_SESSION["signed"]['ip-data'][$ip]['state'] = (empty($_SESSION["signed"]['ip-data'][$ip]['lookups']['location']['region'])?"unknown":$_SESSION["signed"]['ip-data'][$ip]['lookups']['location']['region']);
				if (strlen(trim($_SESSION["signed"]['ip-data'][$ip]['country']))<5)
					$_SESSION["signed"]['ip-data'][$ip]['country'] = 'unknown';
				if (strlen(trim($_SESSION["signed"]['ip-data'][$ip]['state']))<5)
					$_SESSION["signed"]['ip-data'][$ip]['state'] = 'unknown';
			} else {
				$ret[$ip] = $_SESSION["signed"]['ip-data'][$ip];
			}
		}
		$result = (isset($_SESSION["signed"]['ip-data'][$ip])?$_SESSION["signed"]['ip-data'][$ip]:(isset($ret[$ip])?$ret[$ip]:array()));
		if (!empty($key) && isset($result[$key]))
			return $result[$key];
		return $result;
	}
	
	/**
	 * 
	 */
	private function prepareLogs()
	{
		foreach ($this->_scope as $type => $groups) {
			foreach($groups as $mode => $values) {
				$this->_logs[$type][$mode] = $this->_io->load($this->_log_files[$type][$mode]['path'], $this->_log_files[$type][$mode]['filetitle']);
			}
		}
	}

	/**
	 *
	 */
	private function dumpLogs()
	{
		foreach ($this->_logs as $type => $groups) {
			foreach($groups as $mode => $values) {
				if (!empty($values)) {
					$this->_io->save($values, $filepath = $this->_log_files[$type][$mode]['path'], $file = $this->_log_files[$type][$mode]['filetitle'], $method = $this->_base_node);
		
				}
			}
		}
		$GLOBALS['eventCurrent']->setVar('log_storage', $method);
		$GLOBALS['eventCurrent']->setVar('log_file', $file);
		$GLOBALS['eventCurrent']->setVar('log_path', $path);
		$GLOBALS['eventHandler']->insert($GLOBALS['eventCurrent'], true);
		$this->_io->save($this->_statistics, _PATH_LOGS, 'statistics', $this->_base_node);
	}
	
	/**
	 * 
	 */
	private function prepareTracking() {
		if (!isset($this->_logs['execution']['default']['executions']))
			$this->_logs['execution']['default']['executions'] = 1;
		else 
			++$this->_logs['execution']['default']['executions'];
		if (isset($this->_logs['execution']['default']['clients'][$netbios = str_replace(".", "---", $this->getIPData('netbios'))]['created'])) {
				
			$this->_logs['execution']['default']['last']['networking'] = $this->getIPData();
			++$this->_logs['execution']['default']['clients'][$netbios]['executions'];
		} else {
			$this->_logs['execution']['default']['clients'][$netbios] = array('created' => time(), 'ended' => time(), 'executions' => 1, array('last'=> array('session-id' => session_id(true), 'request-url' => $_SERVER['REQUEST_URI'], 'request-method' => $_SERVER['REQUEST_METHOD'], 'remote-port' => $_SERVER['REMOTE_PORT'], 'query-string' => $_SERVER['QUERY_STRING'], 'networking'=>$this->getIPData())));
			$this->_logs['execution']['default']['executions']++;
		}
		if (isset($this->_logs['execution']['default']['log'][date('Y')][date('m')][date('d')])) {
			$this->_logs['execution']['default']['log'][date('Y')][date('m')][date('d')][] = array('when'=>microtime(true), 'networking'=>$this->getIPData(), 'server' => $_SERVER, 'submission' => $this->getSubmissionData());
		} else {
			$this->_logs['execution']['default']['log'][date('Y')][date('m')][date('d')][] = array('when'=>microtime(true), 'networking'=>$this->getIPData(), 'server' => $_SERVER, 'submission' => $this->getSubmissionData());
		}
		$this->_instance_number = count($this->_logs['execution']['default']['log'][date('Y')][date('m')][date('d')]);
	}

	/**
	 * 
	 * @param unknown $type
	 * @param unknown $mode
	 * @param unknown $time
	 */
	private function getFullLogPath($type = 'execution', $mode = 'default', $time = 0)
	{
		static $files = array();
		if (!isset($files[$type][$mode][date('H')]))
		{
			if ($time == 0)
				$time = time();
			if ($type == 'emails' && !in_array($mode, array('reminders', 'notices', 'delivery', 'verify')))
				$mode = 'default';
			else 
				$mode = 'default';
			return $files[$type][$mode][date('H')] = array('path' => $this->_file_paths[$type][$mode], 'filetitle' => sprintf($this->_scope[$type][$mode]['filename'], date('HYm-d-M', $time)));
		} 
		return $files[$type][$mode][date('H')];
	}
	
	/**
	 * 
	 */
	private function preparePaths()
	{
		foreach ($this->_scope as $type => $groups) {
			foreach($groups as $mode => $values) {
				$this->_file_paths[$type][$mode] = (defined($values['path'])?constant($values['path']):$values['path']) . (isset($values['dating']) && !empty($values['dating']) ? DIRECTORY_SEPARATOR . str_replace(array("-", "\\", "/"), DIRECTORY_SEPARATOR, date($values['dating'])):"");
			}
		}		
	}

	/**
	 * 
	 */
	private function prepareFilenames() {
		foreach ($this->_scope as $type => $groups) {
			foreach($groups as $mode => $values) {
				$this->_log_files[$type][$mode] = $this->getFullLogPath($type, $mode, time());
			}
		}
		return true;
	}
	
	/**
	 * 
	 */
	private function prepareStatistics()
	{
	
		$this->_statistics = $this->_io->load(_PATH_LOGS, 'statistics');
		
		// Counts Day Count Physicality
		if (!isset($this->_statistics['day-count']['years'][date('Y')][date('m')][date('d')]))
			$this->_statistics['day-count']['years'][date('Y')][date('m')][date('d')] = date('d');
				
		// Counts Page Impression Physicality
		if (!isset($this->_statistics['impressions']['count']['hourly'][date('Y')][date('m')][date('d')][date('H')]))
			$this->_statistics['impressions']['count']['hourly'][date('Y')][date('m')][date('d')][date('H')] = 0;
		if (!isset($this->_statistics['impressions']['count']['daily'][date('Y')][date('m')][date('d')]))
			$this->_statistics['impressions']['count']['daily'][date('Y')][date('m')][date('d')] = 0;
		if (!isset($this->_statistics['impressions']['count']['monthly'][date('Y')][date('m')]))
			$this->_statistics['impressions']['count']['monthly'][date('Y')][date('m')] = 0;
		if (!isset($this->_statistics['impressions']['count']['yearly'][date('Y')]))
			$this->_statistics['impressions']['count']['yearly'][date('Y')] = 0;
		if (!isset($this->_statistics['impressions']['count']['total']))
			$this->_statistics['impressions']['count']['total'] = 0;
		if (!isset($this->_statistics['impressions']['count']['hourly'][date('Y')][date('m')][date('d')][date('H')]))
			$this->_statistics['impressions']['count']['hourly'][date('Y')][date('m')][date('d')][date('H')] = 0;
		$this->_statistics['impressions']['count']['hourly'][date('Y')][date('m')][date('d')][date('H')]++;
		$this->_statistics['impressions']['count']['daily'][date('Y')][date('m')][date('d')]++;
		$this->_statistics['impressions']['count']['monthly'][date('Y')][date('m')]++;
		$this->_statistics['impressions']['count']['yearly'][date('Y')]++;
		$this->_statistics['impressions']['count']['total']++;
		if (!isset($this->_statistics['countries'][date('Y')][$this->getIPData('country')]))
			$this->_statistics['countries'][date('Y')][$this->getIPData('country')] = 1;
		else
			$this->_statistics['countries'][date('Y')][$this->getIPData('country')]++;
		if (!isset($this->_statistics['states'][date('Y')][$this->getIPData('country')][$this->getIPData('state')]))
			$this->_statistics['states'][date('Y')][$this->getIPData('country')][$this->getIPData('state')] = 1;
		else
			$this->_statistics['states'][date('Y')][$this->getIPData('country')][$this->getIPData('state')]++;
		return true;
	}
	
	/**
	 * 
	 * @param unknown $type
	 * @param unknown $function
	 * @param unknown $data
	 */
	function logPolling($type = 'default', $function = '', $data = array()) {
	
		if (empty($data)&&!is_array($data))
			$data = array();
		
		$this->_last_asset_id = _SIGNED_CONFIG_LANGUAGE.'-'.sha1(__FUNCTION__.__CLASS__.$function.json_encode($data));
	
		if (!isset($this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE]['number-polled']))
			$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE]['number-polled'] = 1;
		else
			$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE]['number-polled'] = $this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE]['number-polled'] + 1;
	
		$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['when'] = microtime(true);
		$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['function'] = $function;
		$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['data'] = $data;
		$this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['networking'] = $this->getIPData();
		$this->_logs['polling'][$type]['package-fingerprint'][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id] = signedCiphers::getInstance()->fingerprint(($this->_logs['polling'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]));
		$GLOBALS['eventCurrent']->setVar('comment', $GLOBALS['eventCurrent']->getVar('comment') . " ::: " . date('Y-m-d H:i:s') . ': Self signed API was polled');
		
	}
	
	/**
	 * 
	 * @param unknown $type
	 * @param unknown $method
	 * @param unknown $what
	 */
	function logDelivery($type = 'default', $method = array(), $what = array()) {
		
		$this->_last_asset_id = _SIGNED_CONFIG_LANGUAGE.'-'.sha1(__FUNCTION__.__CLASS__.json_encode($method).json_encode($what));
		
		if (!isset($this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent']))
			$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] = 1;
		else
			$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] = $this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] + 1;
	
		$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['when'] = microtime(true);
		$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['method'] = $method;
		$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['method'] = $what;
		$this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['networking'] = $this->getIPData();
		$this->_logs['delivery'][$type]['package-fingerprint'][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id] =signedCiphers::getInstance()->fingerprint(($this->_logs['delivery'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]));
		$GLOBALS['eventCurrent']->setVar('comment', $GLOBALS['eventCurrent']->getVar('comment') . " ::: " . date('Y-m-d H:i:s') . ': Delivery of the package was made by Self signed!');
		
	}
	
	/**
	 * 
	 * @param unknown $type
	 * @param unknown $to
	 * @param unknown $cc
	 * @param unknown $bcc
	 * @param unknown $fromname
	 * @param unknown $from
	 * @param unknown $subject
	 * @param unknown $body
	 * @param unknown $isHTML
	 * @param unknown $template
	 * @param unknown $templatedir
	 * @param unknown $data
	 * @param unknown $attachments
	 */
	function logEmail($type = 'default', $to = array(), $cc = array(), $bcc = array(), $fromname = '', $from = '', $subject = '', $body = '', $isHTML = false, $template = '', $templatedir = '', $data = array(), $attachments = array()) {
		
		$this->_last_asset_id = _SIGNED_CONFIG_LANGUAGE.'-'.sha1(__FUNCTION__.__CLASS__.json_encode($to).json_encode($cc).json_encode($bcc).$fromname.$from.$subject.$body.$isHTML.$template.$templatedir.json_encode($data).json_encode($attachments));
		
		if (!in_array($type, array('reminders', 'notices', 'delivery', 'verify')))	
			$type = 'default';
		
		if (!isset($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent']))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] = 1;
		else 
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] = $this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'] + 1;
		
		$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['sent'] = microtime(true);
		$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['which'] = $this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE]['number-sent'];
		$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['networking'] = $this->getIPData();
		if (!empty($data))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['data'] = $data;
		if (!empty($attachments))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['attachments'] = $attachments;
		if (!empty($templatedir) && !empty($template)) {
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['template']['path'] = $templatedir;
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['template']['file'] = $template;
		}
		if (!empty($subject))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['subject'] = $subject;
		if (!empty($body)) {
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['body']['fingerprint'] =signedCiphers::getInstance()->fingerprint($body);
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['body']['length'] = strlen($body);
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['body']['html'] = $isHTML;
		}
		if (!empty($from))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['from']['address'] = $from;
		if (!empty($fromname))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['from']['name'] = $from;
		if (!empty($subject))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['subject']['data'] = $subject;
		$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['subject']['length'] = strlen($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['subject']['data']);
		if (!empty($to))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['to'] = $to;
		if (!empty($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['to']['recipient']))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['recipients']['to'] = count($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['to']);
		if (!empty($cc))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['cc'] = $cc;
		if (!empty($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['cc']['recipient']))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['recipients']['cc'] = count($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['cc']);
		if (!empty($bcc))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['bcc'] = $bcc;
		if (!empty($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['bcc']))
			$this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['recipients']['bcc'] = count($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]['addresses']['bcc']);
		$this->_logs['emails'][$type]['package-fingerprint'][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id] =signedCiphers::getInstance()->fingerprint(($this->_logs['emails'][$type][_SIGNED_CONFIG_LANGUAGE][$this->_last_asset_id]));
		$GLOBALS['eventCurrent']->setVar('comment', $GLOBALS['eventCurrent']->getVar('comment') . " ::: " . date('Y-m-d H:i:s') . ': Email was sent!');
		
	}


	/**
	 *
	 * @param string $state
	 */
	function logBannedIntrusion($state = 'hits')
	{
		if (!isset($this->_statistics['banning'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')]))
			$this->_statistics['banning'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')] = 1;
		else
			$this->_statistics['banning'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')]++;
		if (!isset($this->_statistics['banning'][$state]['daily'][date('Y')][date('m')][date('d')]))
			$this->_statistics['banning'][$state]['daily'][date('Y')][date('m')][date('d')] = 1;
		else
			$this->_statistics['banning'][$state]['daily'][date('Y')][date('m')][date('d')]++;
		if (!isset($this->_statistics['banning'][$state]['monthly'][date('Y')][date('m')]))
			$this->_statistics['banning'][$state]['monthly'][date('Y')][date('m')] = 1;
		else
			$this->_statistics['banning'][$state]['monthly'][date('Y')][date('m')]++;
		if (!isset($this->_statistics['banning'][$state]['yearly'][date('Y')]))
			$this->_statistics['banning'][$state]['yearly'][date('Y')] = 1;
		else
			$this->_statistics['banning'][$state]['yearly'][date('Y')]++;
		if (!isset($this->_statistics['banning'][$state]['total']))
			$this->_statistics['banning'][$state]['total'] = 1;
		else
			$this->_statistics['banning'][$state]['total']++;
		$this->_logs['execution']['default']['banning'][$state][date('Y')][date('m')][date('d')][] = array('when'=>microtime(true), 'networking'=>$this->getIPData(), 'server' => $_SERVER, 'submission' => $this->getSubmissionData());
	}
	

	/**
	 *
	 * @param number $bytes
	 * @param string $state
	 */
	function logBytes($bytes = 0, $state = 'written')
	{
		if (!isset($this->_statistics['bytes'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')]))
			$this->_statistics['bytes'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')] = $bytes;
		else
			$this->_statistics['bytes'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')] = $this->_statistics['bytes'][$state]['hourly'][date('Y')][date('m')][date('d')][date('H')] + $bytes;
		if (!isset($this->_statistics['bytes'][$state]['daily'][date('Y')][date('m')][date('d')]))
			$this->_statistics['bytes'][$state]['daily'][date('Y')][date('m')][date('d')] = $bytes;
		else
			$this->_statistics['bytes'][$state]['daily'][date('Y')][date('m')][date('d')] = $this->_statistics['bytes'][$state]['daily'][date('Y')][date('m')][date('d')] + $bytes;
		if (!isset($this->_statistics['bytes'][$state]['monthly'][date('Y')][date('m')]))
			$this->_statistics['bytes'][$state]['monthly'][date('Y')][date('m')] = $bytes;
		else
			$this->_statistics['bytes'][$state]['monthly'][date('Y')][date('m')] = $this->_statistics['bytes'][$state]['monthly'][date('Y')][date('m')] + $bytes;
		if (!isset($this->_statistics['bytes'][$state]['yearly'][date('Y')]))
			$this->_statistics['bytes'][$state]['yearly'][date('Y')] = $bytes;
		else
			$this->_statistics['bytes'][$state]['yearly'][date('Y')] = $this->_statistics['bytes'][$state]['yearly'][date('Y')] + $bytes;
		if (!isset($this->_statistics['bytes'][$state]['total']))
			$this->_statistics['bytes'][$state]['total'] = $bytes;
		else
			$this->_statistics['bytes'][$state]['total'] = $this->_statistics['bytes'][$state]['total'] + $bytes;
	}
	
	/**
	 * 
	 * @return number
	 */
	private function getLogUnit()
	{
		$log = array();
		$log['server'] = $_SERVER;
		$log['area'] = $this->getArea();
		$log['function'] = $this->getAreaFunction();
		$log['step'] = $this->getAreaStep();
		$log['php'] = $this->getPHPData();
		$log['files'] = $this->getUploadingFileData();
		$log['submission'] = $this->getSubmissionData();
		$log['session'] = $this->getSessionData();
		$log['cookies'] = $this->getCookieData();
		$log['execution']['signedBoot'] = $GLOBALS['signedBoot'];
		$log['execution']['end'] = microtime(true);
		$log['execution']['took'] = microtime(true) - $GLOBALS['signedBoot'];
		foreach($log as $key => $values)
			if (empty($values))
				unset($log[$key]);
		return $log;
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:, multitype:multitype: number string mixed >
	 */
	private function getSessionData()
	{
		$session = array();
		$session['session-id'] = session_id();
		$session['session-name'] = session_name();
		$session['cookie-domain'] = _COOKIE_DOMAIN;
		$session['session-status'] = session_status();
		$session['session-when']['start'] = (!isset($_SESSION["signed"]['session-started'])||empty($_SESSION["signed"]['session-started'])?$_SESSION["signed"]['session-started']=microtime(true):$_SESSION["signed"]['session-started']);
		$session['session-when']['till'] = microtime(true);
		$session['session-when']['exist'] = microtime(true) - $session['session-when']['start'];
		$session['session-bytes'] = $this->getBytesStored($_SESSION["signed"]);
		$session['session-core-keys'] = array_keys($_SESSION["signed"]);
		foreach($session['session-core-keys'] as $key) {
			$session['session-data'][$key] = array('keys'=>(is_array($_SESSION["signed"][$key])?array_keys($_SESSION["signed"][$key]):array()), 'number'=> (is_array($_SESSION["signed"][$key])?count($_SESSION["signed"][$key]):0), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_SESSION["signed"][$key])), 'data' => $_SESSION["signed"][$key], 'bytes'=> $this->getBytesStored($_SESSION["signed"][$key]));
		}
		return $session;
	}
	
	/**
	 *
	 */
	private function getArea()
	{
		if (defined("_SIGNED_CRON_EXECUTING"))
			return 'cronjob';
		$uri = explode("/", $_SERVER["REQUEST_URI"]);
		if (!empty($uri[0]))
			switch($uri[0])
			{
				case 'api':
					return $uri[0];
				default:
					return str_replace("=", "", $uri[0]); 
			}
		return 'unknown';
	}
	

	/**
	 *
	 */
	private function getAreaStep()
	{
		if (!isset($_SESSION["signed"]['step'])||empty($_SESSION["signed"]['step']))
			return 'idle';
		return $_SESSION["signed"]['step'];
	}
	
	/**
	 *
	 */
	private function getAreaFunction()
	{
		if (defined("_SIGNED_CRON_EXECUTING"))
			return 'module';
		$uri = explode("/", $_SERVER["REQUEST_URI"]);
		if (!empty($uri[0]))
			switch($uri[0])
			{
				case 'api':
					if (empty($uri[1]))
						return 'help';
					else 
						return $uri[1];
				default:
					return $_SESSION["signed"]['action'];
			}
		return 'unknown';
	}


	/**
	 *
	 */
	private function getPHPData()
	{
		$inisettings = array(	'precision', 'serialize_precision', 'disable_functions', 'disable_classes', 'max_execution_time',
								'max_input_time', 'memory_limit', 'error_reporting', 'display_errors', 'log_errors', 'report_memleaks',
								'track_errors', 'error_log', 'register_argc_argv', 'auto_globals_jit', 'post_max_size', 'default_mimetype',
								'default_charset', 'enable_dl', 'file_uploads', 'upload_max_filesize', 'max_file_uploads', 'allow_url_fopen',
								'allow_url_include', 'user_agent');
		$settings = array();
		foreach($inisettings as $inivar)
			$setting['ini'][str_replace("_", '-', strtolower($inivar))] = ini_get($inivar);
		$setting['version'] = PHP_VERSION;
		$setting['version-id'] = PHP_VERSION_ID;
		$setting['extensions'] = get_loaded_extensions();
		$setting['date-zone'] = date_default_timezone_get();
		if (file_exists($this->_file_error_log))
			$setting['errors-logged'] = count(signedArrays::getFile($this->_file_error_log));
		else 
			$setting['errors-logged'] = 0;
		return $setting;
	}
	
	/**
	 * 
	 */
	private function getBytesStored($array = array())
	{
		$bytes = 0;
		if (is_array($array)) {
			foreach($array as $key => $values)
			{
				if (is_array($values) && !empty($values))
				{
					$bytes = $bytes + $this->getBytesStored($values);
				} elseif (!empty($values)) {
					$bytes = $bytes + strlen($values);
				}
			}
		} elseif (!empty($array)) {
			$bytes = $bytes + strlen($array);
		}
		return $bytes;
	}

	/**
	 *
	 */
	private function getCookieData()
	{
		if ((isset($_COOKIE) && !empty($_COOKIE)))
			return array('cookie'=>array('keys'=>array_keys($_COOKIE), 'number'=>count($_COOKIE), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_COOKIE)), 'data'=>$_COOKIE, 'data-size' => $this->getBytesStored($_COOKIE)));
		return array();
	}
	
	/**
	 *
	 */
	private function getUploadingFileData()
	{
		if ((isset($_FILE) && !empty($_FILE)))
			return array('file'=>array('keys'=>array_keys($_FILE), 'number'=>count($_FILE), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_FILE)), 'data'=>$_FILE, 'data-size' => $this->getBytesStored($_FILE)));
		return array();
	}
	
	/**
	 *
	 */
	private function getSubmissionData()
	{
		if ((isset($_POST) && !empty($_POST)) && (isset($_GET) && !empty($_GET)))
			return array('post'=>array('keys'=>array_keys($_POST), 'number'=>count($_POST), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_POST)), 'data'=>$_POST, 'data-size' => $this->getBytesStored($_POST)), 'get'=>array('keys'=>array_keys($_GET), 'number'=>count($_GET), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_GET)), 'data'=>$_GET, 'data-size' => $this->getBytesStored($_GET)), 'method' => strtolower($_SERVER['REQUEST_METHOD']));
		elseif ((isset($_POST) && !empty($_POST)))
			return array('post'=>array('keys'=>array_keys($_POST), 'number'=>count($_POST), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_POST)), 'data'=>$_POST, 'data-size' => $this->getBytesStored($_POST)), 'method' => strtolower($_SERVER['REQUEST_METHOD']));
		elseif ((isset($_GET) && !empty($_GET)))
			return array('get'=>array('keys'=>array_keys($_GET), 'number'=>count($_GET), 'fingerprint'=>signedCiphers::getInstance()->fingerprint(($_GET)), 'data'=>$_GET, 'data-size' => $this->getBytesStored($_GET)), 'method' => strtolower($_SERVER['REQUEST_METHOD']));
		return array();		
	}
	
	/**
	 * 
	 * @return multitype:unknown
	 */
	private function getAllLoggingFileStores()
	{
		$paths = array();
		foreach($this->_file_paths as $key => $groups) {
			foreach($groups as $id => $path) {
				if (!in_array($path, $paths))
					$paths[] = $path;
			}
		}
		return $paths;
	}

	/**
	 *
	 */
	function __destruct()
	{
		if (isset($_SESSION["signed"]['signedSignature']) && !empty($_SESSION["signed"]['signedSignature']))
		{
			$signatureHandler = xoops_getmodulehandler('signatures', 'signed');
			foreach($_SESSION["signed"]['signedSignature'] as $key => $signature)
			{
				if($key!='current')
					if ($signature->isDirty())
					{
						$signatureHandler->insert($signature, true);
					}
					else
					{
						unset($_SESSION["signed"]['signedSignature'][$key]);
					}
			}
		}			
		
		
		$this->_logs['execution']['default']['log'][date('Y')][date('m')][date('d')][$this->_instance_number] = $this->getLogUnit();
		$this->_logs['execution']['default']['last']['signedBoot'] = $GLOBALS['signedBoot'];
		$this->_logs['execution']['default']['last']['ended'] = microtime(true);
		if (!isset($this->_logs['execution']['default']['run-time']))
			$this->_logs['execution']['default']['run-time'] = ($seconds = microtime(true) - $GLOBALS['signedBoot']);
		else
			$this->_logs['execution']['default']['run-time'] = $this->_logs['execution']['default']['run-time'] + ($seconds = microtime(true) - $GLOBALS['signedBoot']); 
		if (!isset($this->_statistics['run-time']['seconds']['hourly'][date('Y')][date('m')][date('d')][date('H')]))
			$this->_statistics['run-time']['seconds']['hourly'][date('Y')][date('m')][date('d')][date('H')] = $seconds;
		else
			$this->_statistics['run-time']['seconds']['hourly'][date('Y')][date('m')][date('d')][date('H')] = $this->_statistics['run-time']['seconds']['hourly'][date('Y')][date('m')][date('d')][date('H')] + $seconds;
		if (!isset($this->_statistics['run-time']['seconds']['daily'][date('Y')][date('m')][date('d')]))
			$this->_statistics['run-time']['seconds']['daily'][date('Y')][date('m')][date('d')] = $seconds;
		else
			$this->_statistics['run-time']['seconds']['daily'][date('Y')][date('m')][date('d')] = $this->_statistics['run-time']['seconds']['daily'][date('Y')][date('m')][date('d')] + $seconds;
		if (!isset($this->_statistics['run-time']['seconds']['monthly'][date('Y')][date('m')]))
			$this->_statistics['run-time']['seconds']['monthly'][date('Y')][date('m')] = $seconds;
		else
			$this->_statistics['run-time']['seconds']['monthly'][date('Y')][date('m')] = $this->_statistics['run-time']['seconds']['monthly'][date('Y')][date('m')] + $seconds;
		if (!isset($this->_statistics['run-time']['seconds']['yearly'][date('Y')]))
			$this->_statistics['run-time']['seconds']['yearly'][date('Y')] = $seconds;
		else
			$this->_statistics['run-time']['seconds']['yearly'][date('Y')] = $this->_statistics['run-time']['seconds']['yearly'][date('Y')] + $seconds;
		if (!isset($this->_statistics['run-time']['seconds']['total']))
			$this->_statistics['run-time']['seconds']['total'] = $seconds;
		else
			$this->_statistics['run-time']['seconds']['total'] = $this->_statistics['run-time']['seconds']['total'] + $seconds;
		foreach($this->getAllLoggingFileStores() as $path)
			if (is_dir($path))
				if (count(signedLists::getFileListAsArray($path))==0)
					rmdir($path);
		$this->dumpLogs();
		unset($GLOBALS['logger']);
	}
	
}