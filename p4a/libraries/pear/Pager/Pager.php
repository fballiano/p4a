<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2003, Richard Heyes                                |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Authors: Richard Heyes <richard@phpguru.org>                          |
// |          Lorenzo Alberton <l.alberton at quipo.it>                    |
// +-----------------------------------------------------------------------+
//
// $Id: Pager.php,v 1.15 2004/09/08 16:33:06 quipo Exp $

/**
 * File Pager.php
 *
 * @package Pager
 */
/**
 * Pager - Wrapper class for [Sliding|Jumping]-window Pager
 *
 * Usage examples can be found in the doc provided
 *
 * @author  Richard Heyes <richard@phpguru.org>,
 * @author  Lorenzo Alberton <l.alberton at quipo.it>
 * @version  $Id: Pager.php,v 1.15 2004/09/08 16:33:06 quipo Exp $
 * @package Pager
 */
class Pager
{
    // {{{ Pager()

    /**
     * Constructor
     *
     * -------------------------------------------------------------------------
     * VALID options are (default values are set some lines before):
     *  - mode       (string): "Jumping" or "Sliding"  -window - It determines
     *                         pager behaviour. See the manual for more details
     *  - totalItems (int):    # of items to page.
     *  - perPage    (int):    # of items per page.
     *  - delta      (int):    # of page #s to show before and after the current
     *                         one
     *  - linkClass  (string): name of CSS class used for link styling.
     *  - append     (bool):   if true pageID is appended as GET value to the
     *                         URL - if false it is embedded in the URL
     *                         according to "fileName" specs
     *  - path       (string): complete path to the page (without the page name)
     *  - fileName   (string): name of the page, with a %d if append=true
     *  - urlVar     (string): name of pageNumber URL var, for example "pageID"
     *  - altPrev    (string): alt text to display for prev page, on prev link.
     *  - altNext    (string): alt text to display for next page, on next link.
     *  - altPage    (string): alt text to display before the page number.
     *  - prevImg    (string): sth (it can be text such as "<< PREV" or an
     *                         <img/> as well...) to display instead of "<<".
     *  - nextImg    (string): same as prevImg, used for NEXT link, instead of
     *                         the default value, which is ">>".
     *  - separator  (string): what to use to separate numbers (can be an
     *                         <img/>, a comma, an hyphen, or whatever.
     *  - spacesBeforeSeparator
     *               (int):    number of spaces before the separator.
     *  - firstPagePre (string):
     *                         string used before first page number (can be an
     *                         <img/>, a "{", an empty string, or whatever.
     *  - firstPageText (string):
     *                         string used in place of first page number
     *  - firstPagePost (string):
     *                         string used after first page number (can be an
     *                         <img/>, a "}", an empty string, or whatever.
     *  - lastPagePre (string):
     *                         similar to firstPagePre.
     *  - lastPageText (string):
     *                         similar to firstPageText.
     *  - lastPagePost (string):
     *                         similar to firstPagePost.
     *  - spacesAfterSeparator
     *               (int):    number of spaces after the separator.
     *  - firstLinkTitle (string):
     *                          string used as title in <link rel="first"> tag
     *  - lastLinkTitle (string):
     *                          string used as title in <link rel="last"> tag
     *  - prevLinkTitle (string):
     *                          string used as title in <link rel="prev"> tag
     *  - nextLinkTitle (string):
     *                          string used as title in <link rel="next"> tag
     *  - curPageLinkClassName
     *               (string): name of CSS class used for current page link.
     *  - clearIfVoid(bool):   if there's only one page, don't display pager.
     *  - extraVars (array):   additional URL vars to be added to the querystring
     *  - itemData   (array):  array of items to page.
     *  - useSessions (bool):  if true, number of items to display per page is
     *                         stored in the $_SESSION[$_sessionVar] var.
     *  - closeSession (bool): if true, the session is closed just after R/W.
     *  - sessionVar (string): name of the session var for perPage value.
     *                         A value != from default can be useful when
     *                         using more than one Pager istance in the page.
     *  - pearErrorMode (constant):
     *                         PEAR_ERROR mode for raiseError().
     *                         Default is PEAR_ERROR_RETURN.
     * -------------------------------------------------------------------------
     * REQUIRED options are:
     *  - fileName IF append==false (default is true)
     *  - itemData OR totalItems (if itemData is set, totalItems is overwritten)
     * -------------------------------------------------------------------------
     *
     * @param mixed $options    An associative array of option names and
     *                          their values.
     * @access public
     */
    function Pager($options = array())
    {
        //this check evaluates to true on 5.0.0RC-dev,
        //so i'm using another one, for now...
        //if (version_compare(phpversion(), '5.0.0') == -1) {
        if (get_class($this) == 'pager') { //php4 lowers class names
            // assign factoried method to this for PHP 4
            eval('$this = Pager::factory($options);');
        } else { //php5 is case sensitive
            $msg = 'Pager constructor is deprecated.'
                  .' You must use the "Pager::factory($params)" method'
                  .' instead of "new Pager($params)"';
            trigger_error($msg, E_USER_ERROR);
        }
    }

    // }}}
    // {{{ factory()

    /**
     * Return a pager based on $mode and $options
     *
     * @access public
     * @static
     * @param  string $options Optional parameters for the storage class
     * @return object Object   Storage object
     */
    function &factory($options = array())
    {
        $mode = (isset($options['mode']) ? ucfirst($options['mode']) : 'Jumping');
        $classname = 'Pager_' . $mode;
        $classfile = 'Pager' . DIRECTORY_SEPARATOR . $mode . '.php';
        require_once $classfile;
        $pager =& new $classname($options);
        return $pager;
    }

    // }}}
}
?>