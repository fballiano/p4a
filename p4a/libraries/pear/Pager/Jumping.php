<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2003, Richard Heyes, Lorenzo Alberton              |
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
// $Id: Jumping.php,v 1.5 2004/01/16 10:29:57 quipo Exp $

require_once 'Pager/Common.php';

/**
 * File Jumping.php
 *
 * @package Pager
 */
/**
 * Pager_Jumping - Jumping Window Pager
 *
 * Handles paging a set of data. For usage see the example.php provided.
 *
 * @author Richard Heyes <richard@phpguru.org>,
 * @author Lorenzo Alberton <l.alberton at quipo.it>
 *
 * @version  $Id: Jumping.php,v 1.5 2004/01/16 10:29:57 quipo Exp $
 * @package Pager
 */
class Pager_Jumping extends Pager_Common
{

    // {{{ Pager_Jumping()

    /**
     * Constructor
     *
     * @param mixed $options    An associative array of option names
     *                          and their values
     * @access public
     */
    function Pager_Jumping($options = array())
    {
        $err = $this->_setOptions($options);
        if ($err !== PAGER_OK) {
            return $this->raiseError($this->errorMessage($err), $err);
        }
        $this->_generatePageData();
        $this->_setFirstLastText();

        $this->links .= $this->_getBackLink();
        $this->links .= $this->_getPageLinks();
        $this->links .= $this->_getNextLink();

        $this->linkTags .= $this->_getFirstLinkTag();
        $this->linkTags .= $this->_getPrevLinkTag();
        $this->linkTags .= $this->_getNextLinkTag();
        $this->linkTags .= $this->_getLastLinkTag();
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
        if (!isset($this->_pageData)) {
            $this->_generatePageData();
        }

        if (($index % $this->_perPage) > 0) {
            $pageID = ceil((float)$index / (float)$this->_perPage);
        } else {
            $pageID = $index / $this->_perPage;
        }
        return $pageID;
    }

    // }}}
    // {{{ getPageRangeByPageId()

    /**
     * Given a PageId, it returns the limits of the range of pages displayed.
     * While getOffsetByPageId() returns the offset of the data within the
     * current page, this method returns the offsets of the page numbers interval.
     * E.g., if you have pageId=3 and delta=10, it will return (1, 10).
     * PageID of 8 would give you (1, 10) as well, because 1 <= 8 <= 10.
     * PageID of 11 would give you (11, 20).
     * If the method is called without parameter, pageID is set to currentPage#.
     *
     * @param integer PageID to get offsets for
     * @return array  First and last offsets
     * @access public
     */
    function getPageRangeByPageId($pageid = null)
    {
        $pageid = isset($pageid) ? (int)$pageid : $this->_currentPage;
        if (isset($this->_pageData[$pageid]) || is_null($this->_itemData)) {
            // I'm sure I'm missing something here, but this formula works
            // so I'm using it until I find something simpler.
            $start = ((($pageid + (($this->_delta - ($pageid % $this->_delta))) % $this->_delta) / $this->_delta) - 1) * $this->_delta +1;
            return array(
                max($start, 1),
                min($start+$this->_delta-1, $this->_totalPages)
            );
        } else {
            return array(0, 0);
        }
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
     * be set in the constructor. If a second parameter is provided, then
     * the method act as it previously did. This hack's only purpose is to
     * mantain backward compatibility.
     *
     * @param integer $pageID Optional pageID. If specified, links
     *                for that page are provided instead of current one.
     *                [ADDED IN NEW PAGER VERSION]
     * @param  string $next_html HTML to put inside the next link
     *                [deprecated: use the constructor instead]
     * @return array Back/pages/next links
     */
    function getLinks($pageID=null, $next_html='')
    {
        //BC hack
        if (!empty($next_html)) {
            $back_html = $pageID;
            $pageID = null;
        }

        if ($pageID != null) {
            $_sav = $this->_currentPage;
            $this->_currentPage = $pageID;

            $this->links = '';
            if ($this->_totalPages > $this->_delta) {
                $this->links .= $this->_printFirstPage();
            }
            $this->links .= $this->_getBackLink('', $back_html);
            $this->links .= $this->_getPageLinks();
            $this->links .= $this->_getNextLink('', $next_html);
            if ($this->_totalPages > $this->_delta) {
                $this->links .= $this->_printLastPage();
            }
        }

        $back  = str_replace('&nbsp;', '', $this->_getBackLink());
        $next  = str_replace('&nbsp;', '', $this->_getNextLink());
        $pages = $this->_getPageLinks();
        $first = $this->_printFirstPage();
        $last  = $this->_printLastPage();
        $all   = $this->links;
        $linkTags = $this->linkTags;

        if ($pageID != null) {
            $this->_currentPage = $_sav;
        }

        return array(
                    $back,
                    $pages,
                    trim($next),
                    $first,
                    $last,
                    $all,
                    $linkTags,
                    'back'  => $back,
                    'pages' => $pages,
                    'next'  => $next,
                    'first' => $first,
                    'last'  => $last,
                    'all'   => $all,
                    'linktags' => $linkTags
                );
    }

    // }}}
    // {{{ _getPageLinks()

    /**
     * Returns pages link
     *
     * @param $url  URL to use in the link
     *              [deprecated: use the constructor instead]
     * @return string Links
     * @access private
     */
    function _getPageLinks($url='')
    {
        //legacy setting... the preferred way to set an option now
        //is adding it to the constuctor
        if (!empty($url)) {
            $this->_path = $url;
        }

        $links = '';

        $limits = $this->getPageRangeByPageId($this->_currentPage);

        for ($i=$limits[0]; $i<=min($limits[1], $this->_totalPages); $i++) {
            if ($i != $this->_currentPage) {
                $this->range[$i] = false;
                $links .= sprintf('<a href="%s" %s title="%s">%d</a>',
                                ( $this->_append ? $this->_url.$i : $this->_url.sprintf($this->_fileName, $i) ),
                                $this->_classString,
                                $this->_altPage.' '.$i,
                                $i);
            } else {
                $this->range[$i] = true;
                $links .= $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
            }
            $links .= $this->_spacesBefore
                   . (($i != $this->_totalPages) ? $this->_separator.$this->_spacesAfter : '');
        }

        if ($this->_clearIfVoid) {
            //If there's only one page, don't display links
            if ($this->_totalPages < 2) $links = '';
        }

        return $links;
    }

    // }}}
    // }}}
}
?>