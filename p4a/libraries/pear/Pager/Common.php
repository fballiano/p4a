<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2005, Richard Heyes, Lorenzo Alberton              |
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
// $Id: Common.php,v 1.29 2005/01/17 13:51:25 quipo Exp $

/**
 * File Common.php
 *
 * @package Pager
 */
/**
 * Two constants used to guess the path- and file-name of the page
 * when the user doesn't set any pther value
 */
if (substr($_SERVER['PHP_SELF'], -1) == '/') {
    define('CURRENT_FILENAME', '');
    define('CURRENT_PATHNAME', str_replace('\\', '/', $_SERVER['PHP_SELF']));
} else {
    define('CURRENT_FILENAME', basename($_SERVER['PHP_SELF']));
    define('CURRENT_PATHNAME', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
}
/**
 * Error codes
 */
define('PAGER_OK',                         0);
define('ERROR_PAGER',                     -1);
define('ERROR_PAGER_INVALID',             -2);
define('ERROR_PAGER_INVALID_PLACEHOLDER', -3);
define('ERROR_PAGER_INVALID_USAGE',       -4);
define('ERROR_PAGER_NOT_IMPLEMENTED',     -5);
/**
 * Pager_Common - Common base class for [Sliding|Jumping] Window Pager
 *
 * Usage examples can be found in the doc provided
 *
 * @author Richard Heyes <richard@phpguru.org>,
 * @author Lorenzo Alberton <l.alberton at quipo.it>
 * @version  $Id: Common.php,v 1.29 2005/01/17 13:51:25 quipo Exp $
 * @package Pager
 */
class Pager_Common
{

    // {{{ private class vars

    /**
     * @var integer number of items
     * @access private
     */
    var $_totalItems;

    /**
     * @var integer number of items per page
     * @access private
     */
    var $_perPage     = 10;

    /**
     * @var integer number of page links for each window
     * @access private
     */
    var $_delta       = 10;

    /**
     * @var integer current page number
     * @access private
     */
    var $_currentPage = 1;

    /**
     * @var integer total pages number
     * @access private
     */
    var $_totalPages  = 1;

    /**
     * @var string CSS class for links
     * @access private
     */
    var $_linkClass   = '';

    /**
     * @var string wrapper for CSS class name
     * @access private
     */
    var $_classString = '';

    /**
     * @var string path name
     * @access private
     */
    var $_path        = CURRENT_PATHNAME;

    /**
     * @var string file name
     * @access private
     */
    var $_fileName    = CURRENT_FILENAME;

    /**
     * @var boolean you have to use FALSE with mod_rewrite
     * @access private
     */
    var $_append      = true;

    /**
     * @var string name of the querystring var for pageID
     * @access private
     */
    var $_urlVar      = 'pageID';

    /**
     * @var string name of the url without the pageID number
     * @access private
     */
    var $_url         = '';

    /**
     * @var array additional URL vars
     * @access private
     */
    var $_extraVars   = array();

    /**
     * @var boolean TRUE => expanded mode (for Pager_Sliding)
     * @access private
     */
    var $_expanded    = true;

    /**
     * @var string alt text for "previous page"
     * @access private
     */
    var $_altPrev     = 'previous page';

    /**
     * @var string alt text for "next page"
     * @access private
     */
    var $_altNext     = 'next page';

    /**
     * @var string alt text for "page"
     * @access private
     */
    var $_altPage     = 'page';

    /**
     * @var string image/text to use as "prev" link
     * @access private
     */
    var $_prevImg     = '&lt;&lt; Back';

    /**
     * @var string image/text to use as "next" link
     * @access private
     */
    var $_nextImg     = 'Next &gt;&gt;';

    /**
     * @var string link separator
     * @access private
     */
    var $_separator   = '';

    /**
     * @var integer number of spaces before separator
     * @access private
     */
    var $_spacesBeforeSeparator = 0;

    /**
     * @var integer number of spaces after separator
     * @access private
     */
    var $_spacesAfterSeparator  = 1;

    /**
     * @var string CSS class name for current page link
     * @access private
     */
    var $_curPageLinkClassName  = '';

    /**
     * @var string Text before current page link
     * @access private
     */
    var $_curPageSpanPre        = '';

    /**
     * @var string Text after current page link
     * @access private
     */
    var $_curPageSpanPost       = '';

    /**
     * @var string Text before first page link
     * @access private
     */
    var $_firstPagePre  = '[';

    /**
     * @var string Text to be used for first page link
     * @access private
     */
    var $_firstPageText = '';

    /**
     * @var string Text after first page link
     * @access private
     */
    var $_firstPagePost = ']';

    /**
     * @var string Text before last page link
     * @access private
     */
    var $_lastPagePre   = '[';

    /**
     * @var string Text to be used for last page link
     * @access private
     */
    var $_lastPageText  = '';

    /**
     * @var string Text after last page link
     * @access private
     */
    var $_lastPagePost  = ']';

    /**
     * @var string Will contain the HTML code for the spaces
     * @access private
     */
    var $_spacesBefore  = '';

    /**
     * @var string Will contain the HTML code for the spaces
     * @access private
     */
    var $_spacesAfter   = '';

    /**
     * @var string $_firstLinkTitle
     * @access private
     */
    var $_firstLinkTitle = 'first page';

    /**
     * @var string $_nextLinkTitle
     * @access private
     */
    var $_nextLinkTitle = 'next page';

    /**
     * @var string $_prevLinkTitle
     * @access private
     */
    var $_prevLinkTitle = 'previous page';

    /**
     * @var string $_lastLinkTitle
     * @access private
     */
    var $_lastLinkTitle = 'last page';

    /**
     * @var string Text to be used for the 'show all' option in the select box
     * @access private
     */
    var $_showAllText   = '';

    /**
     * @var array data to be paged
     * @access private
     */
    var $_itemData      = null;

    /**
     * @var boolean If TRUE and there's only one page, links aren't shown
     * @access private
     */
    var $_clearIfVoid   = true;

    /**
     * @var boolean Use session for storing the number of items per page
     * @access private
     */
    var $_useSessions   = false;

    /**
     * @var boolean Close the session when finished reading/writing data
     * @access private
     */
    var $_closeSession  = false;

    /**
     * @var string name of the session var for number of items per page
     * @access private
     */
    var $_sessionVar    = 'setPerPage';

    /**
     * Pear error mode (when raiseError is called)
     * (see PEAR doc)
     *
     * @var int $_pearErrorMode
     * @access private
     */
    var $_pearErrorMode = null;

    // }}}
    // {{{ public vars

    /**
     * @var string Complete set of links
     * @access public
     */
    var $links = '';

    /**
     * @var string Complete set of link tags
     * @access public
     */
    var $linkTags = '';

    /**
     * @var array Array with a key => value pair representing
     *            page# => bool value (true if key==currentPageNumber).
     *            can be used for extreme customization.
     * @access public
     */
    var $range = array();

    // }}}
    // {{{ getPageData()

    /**
     * Returns an array of current pages data
     *
     * @param $pageID Desired page ID (optional)
     * @return array Page data
     * @access public
     */
    function getPageData($pageID = null)
    {
        $pageID = empty($pageID) ? $this->_currentPage : $pageID;


        if (!isset($this->_pageData)) {
            $this->_generatePageData();
        }

        if (!empty($this->_pageData[$pageID])) {
            return $this->_pageData[$pageID];
        }
        return false;
    }

    // }}}
    // {{{ getPageIdByOffset()

    /**
     * Returns pageID for given offset
     *
     * @param $index Offset to get pageID for
     * @return int PageID for given offset
     */
    function getPageIdByOffset($index)
    {
        $msg = '<b>PEAR::Pager Error:</b>'
              .' function "getPageIdByOffset()" not implemented.';
        return $this->raiseError($msg, ERROR_PAGER_NOT_IMPLEMENTED);
    }

    // }}}
    // {{{ getOffsetByPageId()

    /**
     * Returns offsets for given pageID. Eg, if you
     * pass it pageID one and your perPage limit is 10
     * it will return (1, 10). PageID of 2 would
     * give you (11, 20).
     *
     * @param integer PageID to get offsets for
     * @return array  First and last offsets
     * @access public
     */
    function getOffsetByPageId($pageid = null)
    {
        $pageid = isset($pageid) ? $pageid : $this->_currentPage;
        if (!isset($this->_pageData)) {
            $this->_generatePageData();
        }

        if (isset($this->_pageData[$pageid]) || is_null($this->_itemData)) {
            return array(
                        max(($this->_perPage * ($pageid - 1)) + 1, 1),
                        min($this->_totalItems, $this->_perPage * $pageid)
                   );
        } else {
            return array(0, 0);
        }
    }

    // }}}
    // {{{ getPageRangeByPageId()

    /**
     * @param integer PageID to get offsets for
     * @return array  First and last offsets
     */
    function getPageRangeByPageId($pageID)
    {
        $msg = '<b>PEAR::Pager Error:</b>'
              .' function "getPageRangeByPageId()" not implemented.';
        return $this->raiseError($msg, ERROR_PAGER_NOT_IMPLEMENTED);
    }

    // }}}
    // {{{ getLinks()

    /**
     * Returns back/next/first/last and page links,
     * both as ordered and associative array.
     *
     * NB: in original PEAR::Pager this method accepted two parameters,
     * $back_html and $next_html. Now the only parameter accepted is
     * an integer ($pageID), since the html text for prev/next links can
     * be set in the factory. If a second parameter is provided, then
     * the method act as it previously did. This hack was done to mantain
     * backward compatibility only.
     *
     * @param integer $pageID Optional pageID. If specified, links
     *                for that page are provided instead of current one.  [ADDED IN NEW PAGER VERSION]
     * @param  string $next_html HTML to put inside the next link [deprecated: use the factory instead]
     * @return array back/next/first/last and page links
     */
    function getLinks($pageID=null, $next_html='')
    {
        $msg = '<b>PEAR::Pager Error:</b>'
              .' function "getLinks()" not implemented.';
        return $this->raiseError($msg, ERROR_PAGER_NOT_IMPLEMENTED);
    }

    // }}}
    // {{{ getCurrentPageID()

    /**
     * Returns ID of current page
     *
     * @return integer ID of current page
     */
    function getCurrentPageID()
    {
        return $this->_currentPage;
    }

    // }}}
    // {{{ getNextPageID()

    /**
     * Returns next page ID. If current page is last page
	 * this function returns FALSE
	 *
	 * @return mixed Next page ID
     */
	function getNextPageID()
	{
		return ($this->getCurrentPageID() == $this->numPages() ? false : $this->getCurrentPageID() + 1);
	}

	// }}}
    // {{{ getPreviousPageID()

    /**
     * Returns previous page ID. If current page is first page
	 * this function returns FALSE
	 *
	 * @return mixed Previous pages' ID
     */
	function getPreviousPageID()
	{
		return $this->isFirstPage() ? false : $this->getCurrentPageID() - 1;
	}

    // }}}
    // {{{ numItems()

    /**
     * Returns number of items
     *
     * @return int Number of items
     */
    function numItems()
    {
        return $this->_totalItems;
    }

    // }}}
    // {{{ numPages()

    /**
     * Returns number of pages
     *
     * @return int Number of pages
     */
    function numPages()
    {
        return (int)$this->_totalPages;
    }

    // }}}
    // {{{ isFirstPage()

    /**
     * Returns whether current page is first page
     *
     * @return bool First page or not
     */
    function isFirstPage()
    {
        return ($this->_currentPage < 2);
    }

    // }}}
    // {{{ isLastPage()

    /**
     * Returns whether current page is last page
     *
     * @return bool Last page or not
     */
    function isLastPage()
    {
        return ($this->_currentPage == $this->_totalPages);
    }

    // }}}
    // {{{ isLastPageComplete()

    /**
     * Returns whether last page is complete
     *
     * @return bool Last age complete or not
     */
    function isLastPageComplete()
    {
        return !($this->_totalItems % $this->_perPage);
    }

    // }}}
    // {{{ _generatePageData()

    /**
     * Calculates all page data
     * @access private
     */
    function _generatePageData()
    {
        // Been supplied an array of data?
        if (!is_null($this->_itemData)) {
            $this->_totalItems = count($this->_itemData);
        }
        $this->_totalPages = ceil((float)$this->_totalItems / (float)$this->_perPage);
        $i = 1;
        if (!empty($this->_itemData)) {
            foreach ($this->_itemData as $key => $value) {
                $this->_pageData[$i][$key] = $value;
                if (count($this->_pageData[$i]) >= $this->_perPage) {
                    $i++;
                }
            }
        } else {
            $this->_pageData = array();
        }

        //prevent URL modification
        $this->_currentPage = min($this->_currentPage, $this->_totalPages);
    }

    // }}}
    // {{{ _getLinksUrl()

    /**
     * Returns the correct link for the back/pages/next links
     *
     * @return string Url
     * @access private
     */
    function _getLinksUrl()
    {
        // Sort out query string to prevent messy urls
        $querystring = array();
        $qs = array();
        $arrays = array();
        if (!empty($_SERVER['QUERY_STRING'])) {
            $qs = explode('&', str_replace('&amp;', '&', $_SERVER['QUERY_STRING']));
            for ($i = 0, $cnt = count($qs); $i < $cnt; $i++) {
                if (strstr($qs[$i], '=') !== false){ // check first if exist a pair
                    list($name, $value) = explode('=', $qs[$i]);
                    if ($name != $this->_urlVar) {
                        $name = rawurldecode($name);
                        //check for arrays in parameters: site.php?foo[]=1&foo[]=2&foo[]=3
                        if ((strpos($name, '[') !== false) &&
                            (strpos($name, ']') !== false)
                        ) {
                            $arrays[] = $qs[$i];
                        } else {
                            $qs[$name] = $value;
                        }
                    }
                } else {
                    $qs[$qs[$i]] = '';
                }
                unset($qs[$i]);
            }
        }
        if (is_array($this->_extraVars)) {
            foreach ($this->_extraVars as $name => $value) {
                $qs[$name] = $value; //eventually overwrite duplicate vars
            }
        }

        foreach ($qs as $name => $value) {
            $querystring[] = $name . '=' . $value;
        }
        $querystring = array_merge($querystring, array_unique($arrays));
        $querystring = array_map('htmlentities', $querystring);

        return '?' . implode('&amp;', $querystring) . (!empty($querystring) ? '&amp;' : '') . $this->_urlVar .'=';
    }

    // }}}
    // {{{ _getBackLink()

    /**
     * Returns back link
     *
     * @param $url  URL to use in the link  [deprecated: use the factory instead]
     * @param $link HTML to use as the link [deprecated: use the factory instead]
     * @return string The link
     * @access private
     */
    function _getBackLink($url='', $link='')
    {
        //legacy settings... the preferred way to set an option
        //now is passing it to the factory
        if (!empty($url)) {
            $this->_path = $url;
        }
        if (!empty($link)) {
            $this->_prevImg = $link;
        }
        $back = '';
        if ($this->_currentPage > 1) {
            $back = sprintf('<a href="%s" %s title="%s">%s</a>',
                            ( $this->_append ? $this->_url.$this->getPreviousPageID() :
                                    $this->_url.sprintf($this->_fileName, $this->getPreviousPageID()) ),
                            $this->_classString,
                            $this->_altPrev,
                            $this->_prevImg)
                  . $this->_spacesBefore . $this->_spacesAfter;
        }
        return $back;
    }

    // }}}
    // {{{ _getPageLinks()

    /**
     * Returns pages link
     *
     * @param $url  URL to use in the link [deprecated: use the factory instead]
     * @return string Links
     * @access private
     */
    function _getPageLinks($url='')
    {
        $msg = '<b>PEAR::Pager Error:</b>'
              .' function "getOffsetByPageId()" not implemented.';
        return $this->raiseError($msg, ERROR_PAGER_NOT_IMPLEMENTED);

    }

    // }}}
    // {{{ _getNextLink()

    /**
     * Returns next link
     *
     * @param $url  URL to use in the link  [deprecated: use the factory instead]
     * @param $link HTML to use as the link [deprecated: use the factory instead]
     * @return string The link
     * @access private
     */
    function _getNextLink($url='', $link='')
    {
        //legacy settings... the preferred way to set an option
        //now is passing it to the factory
        if (!empty($url)) {
            $this->_path = $url;
        }
        if (!empty($link)) {
            $this->_nextImg = $link;
        }
        $next = '';
        if ($this->_currentPage < $this->_totalPages) {
            $next = $this->_spacesAfter
                 . sprintf('<a href="%s" %s title="%s">%s</a>',
                            ($this->_append ? $this->_url.$this->getNextPageID() :
                                $this->_url.sprintf($this->_fileName, $this->getNextPageID())),
                            $this->_classString,
                            $this->_altNext,
                            $this->_nextImg)
                 . $this->_spacesBefore . $this->_spacesAfter;
        }
        return $next;
    }


    // }}}
    // {{{ _getPrevLinkTag()

    /**
     * Returns previous link tag
     *
     * @return string the link tag
     * @access private
     */
    function _getPrevLinkTag()
    {
        $prevLinkTag = '';
        if ($this->_currentPage > 1) {
            $prevLinkTag = sprintf('<link rel="previous" href="%s" title="%s" />'."\n",
                                   ($this->_append ? $this->_url.$this->getPreviousPageID() :
                                        $this->_url.sprintf($this->_fileName, $this->getPreviousPageID())),
                                   $this->_prevLinkTitle);
        }
        return $prevLinkTag;
    }

    // }}}
    // {{{ _getNextLinkTag()

    /**
     * Returns next link tag
     *
     * @return string the link tag
     * @access private
     */
    function _getNextLinkTag()
    {
        $nextLinktag = '';
        if ($this->_currentPage < $this->_totalPages) {
            $nextLinktag = sprintf('<link rel="next" href="%s" title="%s" />'."\n",
                                   ($this->_append ? $this->_url.$this->getNextPageID() :
                                        $this->_url.sprintf($this->_fileName, $this->getNextPageID())),
                                   $this->_nextLinkTitle);
        }
        return $nextLinktag;
    }

    // }}}
    // {{{ _getFirstLinkTag()

    /**
     * @return string
     * @access private
     */
    function _getFirstLinkTag()
    {
        if ($this->isFirstPage()) {
            return '';
        } else {
            return sprintf('<link rel="first" href="%s" title="%s" />'."\n",
                           ($this->_append ? $this->_url.'1' :
                                $this->_url.sprintf($this->_fileName, 1)),
                           $this->_firstLinkTitle);
        }
    }

    // }}}
    // {{{ _getLastLinkTag()

    /**
     * @return string the link tag
     * @access private
     */
    function _getLastLinkTag()
    {
        if ($this->isLastPage()) {
            return '';
        } else {
            return sprintf('<link rel="last" href="%s" title="%s" />'."\n",
                           ($this->_append ? $this->_url.$this->_totalPages :
                                $this->_url.sprintf($this->_fileName, $this->_totalPages)),
                           $this->_lastLinkTitle);
        }
    }

    // }}}
    // {{{ getPerPageSelectBox()

    /**
     * Returns a string with a XHTML SELECT menu,
     * useful for letting the user choose how many items per page should be
     * displayed. If parameter useSessions is TRUE, this value is stored in
     * a session var. The string isn't echoed right now so you can use it
     * with template engines.
     *
     * @param integer $start
     * @param integer $end
     * @param integer $step
     * @param boolean $showAllData If true, perPage is set equal to totalItems.
     * @param array   (or string $optionText for BC reasons)
     *                - 'optionText': text to show in each option.
     *                  Use '%d' where you want to see the number of pages selected.
     *                - 'attributes': (html attributes) Tag attributes or
     *                  HTML attributes id="foo" pairs, will be inserted in the
     *                  <select> tag
     * @return string xhtml select box
     * @access public
     */
    function getPerPageSelectBox($start=5, $end=30, $step=5, $showAllData=false, $extraParams=array())
    {
        $optionText = '%d';
        $attributes = '';
        if (is_string($extraParams)) {
            //old behavior, BC maintained
            $optionText = $extraParams;
        } else {
            if (array_key_exists('optionText', $extraParams)) {
                $optionText = $extraParams['optionText'];
            }
            if (array_key_exists('attributes', $extraParams)) {
                $attributes = $extraParams['attributes'];
            }
        }

        if (!strstr($optionText, '%d')) {
            return $this->raiseError(
                $this->errorMessage(ERROR_PAGER_INVALID_PLACEHOLDER),
                ERROR_PAGER_INVALID_PLACEHOLDER
            );
        }
        $start = (int)$start;
        $end   = (int)$end;
        $step  = (int)$step;
        if (!empty($_SESSION[$this->_sessionVar])) {
            $selected = (int)$_SESSION[$this->_sessionVar];
        } else {
            $selected = $this->_perPage;
        }

        $tmp = '<select name="'.$this->_sessionVar.'"';
        if (!empty($attributes)) {
            $tmp .= ' '.$attributes;
        }
        $tmp .= '>';
        for ($i=$start; $i<=$end; $i+=$step) {
            $tmp .= '<option value="'.$i.'"';
            if ($i == $selected) {
                $tmp .= ' selected="selected"';
            }
            $tmp .= '>'.sprintf($optionText, $i).'</option>';
        }
        if ($showAllData && $end < $this->_totalItems) {
            $tmp .= '<option value="'.$this->_totalItems.'"';
            if ($this->_totalItems == $selected) {
                $tmp .= ' selected="selected"';
            }
            $tmp .= '>';
            if (empty($this->_showAllText)) {
                $tmp .= str_replace('%d', $this->_totalItems, $optionText);
            } else {
                $tmp .= $this->_showAllText;
            }
            $tmp .= '</option>';
        }
        $tmp .= '</select>';
        return $tmp;
    }

    // }}}
    // {{{ _printFirstPage()

    /**
     * Print [1]
     *
     * @return string String with link to 1st page,
     *                or empty string if this is the 1st page.
     * @access private
     */
    function _printFirstPage()
    {
        if ($this->isFirstPage()) {
            return '';
        } else {
            return sprintf('<a href="%s" %s title="%s">%s%s%s</a>',
                            ( $this->_append ? $this->_url.'1' : $this->_url.sprintf($this->_fileName, 1) ),
                            $this->_classString,
                            $this->_altPage.' 1',
                            $this->_firstPagePre,
                            $this->_firstPageText,
                            $this->_firstPagePost)
                 . $this->_spacesBefore . $this->_spacesAfter;

        }
    }

    // }}}
    // {{{ _printLastPage()

    /**
     * Print [numPages()]
     *
     * @return string String with link to last page,
     *                or empty string if this is the 1st page.
     * @access private
     */
    function _printLastPage()
    {
        if ($this->isLastPage()) {
            return '';
        } else {
            return sprintf('<a href="%s" %s title="%s">%s%s%s</a>',
                            ( $this->_append ? $this->_url.$this->_totalPages : $this->_url.sprintf($this->_fileName, $this->_totalPages) ),
                            $this->_classString,
                            $this->_altPage.' '.$this->_totalPages,
                            $this->_lastPagePre,
                            $this->_lastPageText,
                            $this->_lastPagePost);
        }
    }

    // }}}
    // {{{ _setFirstLastText()

    /**
     * sets the private _firstPageText, _lastPageText variables
     * based on whether they were set in the options
     *
     * @access private
     */
    function _setFirstLastText()
    {
        if ($this->_firstPageText == '') {
            $this->_firstPageText = '1';
        }

        if ($this->_lastPageText == '') {
            $this->_lastPageText = $this->_totalPages;
        }
    }

    // }}}
    // {{{ raiseError()

    /**
     * conditionally includes PEAR base class and raise an error
     *
     * @param string $msg  Error message
     * @param int    $code Error code
     * @access private
     */
    function raiseError($msg, $code)
    {
        include_once 'PEAR.php';
        if (empty($this->_pearErrorMode)) {
            $this->_pearErrorMode = PEAR_ERROR_RETURN;
        }
        return PEAR::raiseError($msg, $code, $this->_pearErrorMode);
    }

    // }}}
    // {{{ _setOptions()

    /**
     * Set and sanitize options
     *
     * @param mixed $options    An associative array of option names and
     *                          their values.
     * @return integer error code (PAGER_OK on success)
     * @access private
     */
    function _setOptions($options)
    {
        $allowed_options = array(
            'totalItems',
            'perPage',
            'delta',
            'linkClass',
            'path',
            'fileName',
            'append',
            'urlVar',
            'altPrev',
            'altNext',
            'altPage',
            'prevImg',
            'nextImg',
            'expanded',
            'separator',
            'spacesBeforeSeparator',
            'spacesAfterSeparator',
            'curPageLinkClassName',
            'curPageSpanPre',
            'curPageSpanPost',
            'firstPagePre',
            'firstPageText',
            'firstPagePost',
            'lastPagePre',
            'lastPageText',
            'lastPagePost',
            'firstLinkTitle',
            'nextLinkTitle',
            'prevLinkTitle',
            'lastLinkTitle',
            'showAllText',
            'itemData',
            'clearIfVoid',
            'useSessions',
            'closeSession',
            'sessionVar',
            'pearErrorMode',
            'extraVars',
            'currentPage',
        );

        foreach ($options as $key => $value) {
            if (in_array($key, $allowed_options) && ($value !== null)) {
                $this->{'_' . $key} = $value;
            }
        }

        $this->_fileName = ltrim($this->_fileName, '/');  //strip leading slash
        $this->_path     = rtrim($this->_path, '/');      //strip trailing slash

        if ($this->_append) {
            $this->_fileName = CURRENT_FILENAME; //avoid easy-verified user error;
            $this->_url = $this->_path.'/'.$this->_fileName.$this->_getLinksUrl();
        } else {
            $this->_url = $this->_path;
            if (strncasecmp($this->_fileName, 'javascript', 10) != 0) {
                $this->_url.'/';
            }
            if (!strstr($this->_fileName, '%d')) {
                trigger_error($this->errorMessage(ERROR_PAGER_INVALID_USAGE), E_USER_WARNING);
            }
        }

        if (strlen($this->_linkClass)) {
            $this->_classString = 'class="'.$this->_linkClass.'"';
        } else {
            $this->_classString = '';
        }

        if (strlen($this->_curPageLinkClassName)) {
            $this->_curPageSpanPre  = '<span class="'.$this->_curPageLinkClassName.'">';
            $this->_curPageSpanPost = '</span>';
        }

        if ($this->_perPage < 1) {   //avoid possible user errors
            $this->_perPage = 1;
        }

        if ($this->_useSessions && !isset($_SESSION)) {
            session_start();
        }
        if (!empty($_REQUEST[$this->_sessionVar])) {
            $this->_perPage = max(1, (int)$_REQUEST[$this->_sessionVar]);

            if ($this->_useSessions) {
                $_SESSION[$this->_sessionVar] = $this->_perPage;
            }
        }

        if (!empty($_SESSION[$this->_sessionVar])) {
             $this->_perPage = $_SESSION[$this->_sessionVar];
        }

        if ($this->_closeSession) {
            session_write_close();
        }

        for ($i=0; $i<$this->_spacesBeforeSeparator; $i++) {
            $this->_spacesBefore .= '&nbsp;';
        }

        for ($i=0; $i<$this->_spacesAfterSeparator; $i++) {
            $this->_spacesAfter .= '&nbsp;';
        }

        if (isset($_REQUEST[$this->_urlVar])) {
            $this->_currentPage = (int)$_REQUEST[$this->_urlVar];
        }
        $this->_currentPage = max($this->_currentPage, 1);

        return PAGER_OK;
    }

    // }}}
    // {{{ errorMessage()

    /**
     * Return a textual error message for a PAGER error code
     *
     * @param   int     $code error code
     * @return  string  error message
     * @access public
     */
    function errorMessage($code)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                ERROR_PAGER                     => 'unknown error',
                ERROR_PAGER_INVALID             => 'invalid',
                ERROR_PAGER_INVALID_PLACEHOLDER => 'invalid format - use "%d" as placeholder.',
                ERROR_PAGER_INVALID_USAGE       => 'if $options[\'append\'] is set to false, '
                                                  .' $options[\'fileName\'] MUST contain the "%d" placeholder.',
                ERROR_PAGER_NOT_IMPLEMENTED     => 'can not create'
            );
        }

        return '<b>PEAR::Pager error:</b> '. (isset($errorMessages[$code]) ?
            $errorMessages[$code] : $errorMessages[ERROR_PAGER]);
    }

    // }}}
}
?>