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
 * @subpackage		mailer
 * @description		Digital Signature Generation & API Services (Psuedo-legal correct binding measure)
 * @link			Farming Digital Fingerprint Signatures: https://signed.ringwould.com.au
 * @link			Heavy Hash-info Digital Fingerprint Signature: http://signed.hempembassy.net
 * @link			XOOPS SVN: https://sourceforge.net/p/xoops/svn/HEAD/tree/XoopsModules/signed/
 * @see				Release Article: http://cipher.labs.coop/portfolio/signed-identification-validations-and-signer-for-xoops/
 * @filesource
 *
 */


xoops_load('XoopsMailer');

class signedMultiMailer extends xoopsMultiMailer
{
    /**
     * 'from' address
     *
     * @var string
     * @access private
     */
    var $From = '';

    /**
     * 'from' name
     *
     * @var string
     * @access private
     */
    var $FromName = '';

    // can be 'smtp', 'sendmail', or 'mail'
    /**
     * Method to be used when sending the mail.
     *
     * This can be:
     * <li>mail (standard PHP function 'mail()') (default)
     * <li>smtp    (send through any SMTP server, SMTPAuth is supported.
     * You must set {@link $Host}, for SMTPAuth also {@link $SMTPAuth},
     * {@link $Username}, and {@link $Password}.)
     * <li>sendmail (manually set the path to your sendmail program
     * to something different than 'mail()' uses in {@link $Sendmail})
     *
     * @var string
     * @access private
     */
    var $Mailer = 'mail';

    /**
     * set if $Mailer is 'sendmail'
     *
     * Only used if {@link $Mailer} is set to 'sendmail'.
     * Contains the full path to your sendmail program or replacement.
     *
     * @var string
     * @access private
     */
    var $Sendmail = '/usr/sbin/sendmail';

    /**
     * SMTP Host.
     *
     * Only used if {@link $Mailer} is set to 'smtp'
     *
     * @var string
     * @access private
     */
    var $Host = '';

    /**
     * Does your SMTP host require SMTPAuth authentication?
     *
     * @var boolean
     * @access private
     */
    var $SMTPAuth = false;

    /**
     * Username for authentication with your SMTP host.
     *
     * Only used if {@link $Mailer} is 'smtp' and {@link $SMTPAuth} is TRUE
     *
     * @var string
     * @access private
     */
    var $Username = '';

    /**
     * Password for SMTPAuth.
     *
     * Only used if {@link $Mailer} is 'smtp' and {@link $SMTPAuth} is TRUE
     *
     * @var string
     * @access private
     */
    var $Password = '';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    function signedMultiMailer($from, $fromName)
    {
        $this->From = (empty($from)?_SIGNED_EMAIL_FROMADDR:$from);
        $this->FromName = (empty($fromName)?_SIGNED_EMAIL_FROMNAME:$fromName);
        $this->Mailer = _SIGNED_EMAIL_METHOD;
        $this->Host = _SIGNED_EMAIL_SMTP_HOSTNAME;
        $this->Username = _SIGNED_EMAIL_SMTP_USERNAME;
        $this->Password = _SIGNED_EMAIL_SMTP_PASSWORD;
        $this->Sendmail = _SIGNED_EMAIL_SENDMAIL;
        if ($this->Mailer == 'smtp' && strlen($this->Username)>0 && strlen($this->Password)>0)
        	$this->SMTPAuth = true;
        $this->CharSet = strtolower('iso-8859-1');
        $this->PluginDir = dirname(__FILE__) . '/phpmailer/';
    }

    /**
     * Formats an address correctly. This overrides the default addr_format method which does not seem to encode $FromName correctly
     *
     * @access private
     * @return string
     */
    function AddrFormat($addr)
    {
        if (empty($addr[1])) {
            $formatted = $addr[0];
        } else {
            $formatted = sprintf('%s <%s>', '=?' . $this->CharSet . '?B?' . base64_encode($addr[1]) . '?=', $addr[0]);
        }
        return $formatted;
    }
}

?>