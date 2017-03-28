<?php
/**
 * signed form element
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The signed Project http://sourceforge.net/projects/signed/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         module
 * @subpackage      form
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.signed.org/
 * @version         $Id: formselectcountry.php 8066 2011-11-06 05:09:33Z beckmi $
 */

defined('_PATH_ROOT') or die('Restricted access');

include_once _PATH_ROOT . _DS_ . 'class' . _DS_ . 'signedlists.php';

/**
 * A select field with countries
 */
class signedFormSelectCountry extends xoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param mixed $value Pre-selected value (or array of them).
     *                                    Legal are all 2-letter country codes (in capitals).
     * @param int $size Number or rows. "1" makes a drop-down-list
     */
    function signedFormSelectCountry($caption, $name, $value = null, $size = 1)
    {
        $this->xoopsFormSelect($caption, $name, $value, $size);
        $this->addOptionArray(signedLists::getCountryList());
    }
}

?>