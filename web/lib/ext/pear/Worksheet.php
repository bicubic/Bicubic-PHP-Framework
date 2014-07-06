<?php

/*
 *  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
 *
 *  The majority of this is _NOT_ my code.  I simply ported it from the
 *  PERL Spreadsheet::WriteExcel module.
 *
 *  The author of the Spreadsheet::WriteExcel module is John McNamara
 *  <jmcnamara@cpan.org>
 *
 *  I _DO_ maintain this code, and John McNamara has nothing to do with the
 *  porting of this code to PHP.  Any questions directly related to this
 *  class library should be directed to me.
 *
 *  License Information:
 *
 *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
 *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 2.1 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library; if not, write to the Free Software
 *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once 'Parser.php';
require_once 'BIFFwriter.php';

/**
 * Class for generating Excel Spreadsheets
 *
 * @author   Xavier Noguer <xnoguer@rezebra.com>
 * @category FileFormats
 * @package  Spreadsheet_Excel_Writer
 */
class Spreadsheet_Excel_Writer_Worksheet extends Spreadsheet_Excel_Writer_BIFFwriter {

    /**
     * Name of the Worksheet
     * @var string
     */
    var $name;
    /**
     * Index for the Worksheet
     * @var integer
     */
    var $index;
    /**
     * Reference to the (default) Format object for URLs
     * @var object Format
     */
    var $_url_format;
    /**
     * Reference to the parser used for parsing formulas
     * @var object Format
     */
    var $_parser;
    /**
     * Filehandle to the temporary file for storing data
     * @var resource
     */
    var $_filehandle;
    /**
     * Boolean indicating if we are using a temporary file for storing data
     * @var bool
     */
    var $_using_tmpfile;
    /**
     * Maximum number of rows for an Excel spreadsheet (BIFF5)
     * @var integer
     */
    var $_xls_rowmax;
    /**
     * Maximum number of columns for an Excel spreadsheet (BIFF5)
     * @var integer
     */
    var $_xls_colmax;
    /**
     * Maximum number of characters for a string (LABEL record in BIFF5)
     * @var integer
     */
    var $_xls_strmax;
    /**
     * First row for the DIMENSIONS record
     * @var integer
     * @see _storeDimensions()
     */
    var $_dim_rowmin;
    /**
     * Last row for the DIMENSIONS record
     * @var integer
     * @see _storeDimensions()
     */
    var $_dim_rowmax;
    /**
     * First column for the DIMENSIONS record
     * @var integer
     * @see _storeDimensions()
     */
    var $_dim_colmin;
    /**
     * Last column for the DIMENSIONS record
     * @var integer
     * @see _storeDimensions()
     */
    var $_dim_colmax;
    /**
     * Array containing format information for columns
     * @var array
     */
    var $_colinfo;
    /**
     * Array containing the selected area for the worksheet
     * @var array
     */
    var $_selection;
    /**
     * Array containing the panes for the worksheet
     * @var array
     */
    var $_panes;
    /**
     * The active pane for the worksheet
     * @var integer
     */
    var $_active_pane;
    /**
     * Bit specifying if panes are frozen
     * @var integer
     */
    var $_frozen;
    /**
     * Bit specifying if the worksheet is selected
     * @var integer
     */
    var $selected;
    /**
     * The paper size (for printing) (DOCUMENT!!!)
     * @var integer
     */
    var $_paper_size;
    /**
     * Bit specifying paper orientation (for printing). 0 => landscape, 1 => portrait
     * @var integer
     */
    var $_orientation;
    /**
     * The page header caption
     * @var string
     */
    var $_header;
    /**
     * The page footer caption
     * @var string
     */
    var $_footer;
    /**
     * The horizontal centering value for the page
     * @var integer
     */
    var $_hcenter;
    /**
     * The vertical centering value for the page
     * @var integer
     */
    var $_vcenter;
    /**
     * The margin for the header
     * @var float
     */
    var $_margin_head;
    /**
     * The margin for the footer
     * @var float
     */
    var $_margin_foot;
    /**
     * The left margin for the worksheet in inches
     * @var float
     */
    var $_margin_left;
    /**
     * The right margin for the worksheet in inches
     * @var float
     */
    var $_margin_right;
    /**
     * The top margin for the worksheet in inches
     * @var float
     */
    var $_margin_top;
    /**
     * The bottom margin for the worksheet in inches
     * @var float
     */
    var $_margin_bottom;
    /**
     * First row to reapeat on each printed page
     * @var integer
     */
    var $title_rowmin;
    /**
     * Last row to reapeat on each printed page
     * @var integer
     */
    var $title_rowmax;
    /**
     * First column to reapeat on each printed page
     * @var integer
     */
    var $title_colmin;
    /**
     * First row of the area to print
     * @var integer
     */
    var $print_rowmin;
    /**
     * Last row to of the area to print
     * @var integer
     */
    var $print_rowmax;
    /**
     * First column of the area to print
     * @var integer
     */
    var $print_colmin;
    /**
     * Last column of the area to print
     * @var integer
     */
    var $print_colmax;
    /**
     * Whether to use outline.
     * @var integer
     */
    var $_outline_on;
    /**
     * Auto outline styles.
     * @var bool
     */
    var $_outline_style;
    /**
     * Whether to have outline summary below.
     * @var bool
     */
    var $_outline_below;
    /**
     * Whether to have outline summary at the right.
     * @var bool
     */
    var $_outline_right;
    /**
     * Outline row level.
     * @var integer
     */
    var $_outline_row_level;
    /**
     * Whether to fit to page when printing or not.
     * @var bool
     */
    var $_fit_page;
    /**
     * Number of pages to fit wide
     * @var integer
     */
    var $_fit_width;
    /**
     * Number of pages to fit high
     * @var integer
     */
    var $_fit_height;
    /**
     * Reference to the total number of strings in the workbook
     * @var integer
     */
    var $_str_total;
    /**
     * Reference to the number of unique strings in the workbook
     * @var integer
     */
    var $_str_unique;
    /**
     * Reference to the array containing all the unique strings in the workbook
     * @var array
     */
    var $_str_table;
    /**
     * Merged cell ranges
     * @var array
     */
    var $_merged_ranges;
    /**
     * Charset encoding currently used when calling writeString()
     * @var string
     */
    var $_input_encoding;

    /**
     * Constructor
     *
     * @param string  $name         The name of the new worksheet
     * @param integer $index        The index of the new worksheet
     * @param mixed   &$activesheet The current activesheet of the workbook we belong to
     * @param mixed   &$firstsheet  The first worksheet in the workbook we belong to
     * @param mixed   &$url_format  The default format for hyperlinks
     * @param mixed   &$parser      The formula parser created for the Workbook
     * @param string  $tmp_dir      The path to the directory for temporary files
     * @access private
     */
    function Spreadsheet_Excel_Writer_Worksheet($BIFF_version, $name, $index, &$activesheet, &$firstsheet, &$str_total, &$str_unique, &$str_table, &$url_format, &$parser, $tmp_dir) {
        // It needs to call its parent's constructor explicitly
        $this->Spreadsheet_Excel_Writer_BIFFwriter();
        $this->_BIFF_version = $BIFF_version;
        $rowmax = 65536; // 16384 in Excel 5
        $colmax = 256;

        $this->name = $name;
        $this->index = $index;
        $this->activesheet = &$activesheet;
        $this->firstsheet = &$firstsheet;
        $this->_str_total = &$str_total;
        $this->_str_unique = &$str_unique;
        $this->_str_table = &$str_table;
        $this->_url_format = &$url_format;
        $this->_parser = &$parser;

        //$this->ext_sheets      = array();
        $this->_filehandle = '';
        $this->_using_tmpfile = true;
        //$this->fileclosed      = 0;
        //$this->offset          = 0;
        $this->_xls_rowmax = $rowmax;
        $this->_xls_colmax = $colmax;
        $this->_xls_strmax = 255;
        $this->_dim_rowmin = $rowmax + 1;
        $this->_dim_rowmax = 0;
        $this->_dim_colmin = $colmax + 1;
        $this->_dim_colmax = 0;
        $this->_colinfo = array();
        $this->_selection = array(0, 0, 0, 0);
        $this->_panes = array();
        $this->_active_pane = 3;
        $this->_frozen = 0;
        $this->selected = 0;

        $this->_paper_size = 0x0;
        $this->_orientation = 0x1;
        $this->_header = '';
        $this->_footer = '';
        $this->_hcenter = 0;
        $this->_vcenter = 0;
        $this->_margin_head = 0.50;
        $this->_margin_foot = 0.50;
        $this->_margin_left = 0.75;
        $this->_margin_right = 0.75;
        $this->_margin_top = 1.00;
        $this->_margin_bottom = 1.00;

        $this->title_rowmin = null;
        $this->title_rowmax = null;
        $this->title_colmin = null;
        $this->title_colmax = null;
        $this->print_rowmin = null;
        $this->print_rowmax = null;
        $this->print_colmin = null;
        $this->print_colmax = null;

        $this->_print_gridlines = 1;
        $this->_screen_gridlines = 1;
        $this->_print_headers = 0;

        $this->_fit_page = 0;
        $this->_fit_width = 0;
        $this->_fit_height = 0;

        $this->_hbreaks = array();
        $this->_vbreaks = array();

        $this->_protect = 0;
        $this->_password = null;

        $this->col_sizes = array();
        $this->_row_sizes = array();

        $this->_zoom = 100;
        $this->_print_scale = 100;

        $this->_outline_row_level = 0;
        $this->_outline_style = 0;
        $this->_outline_below = 1;
        $this->_outline_right = 1;
        $this->_outline_on = 1;

        $this->_merged_ranges = array();

        $this->_input_encoding = '';

        $this->_dv = array();

        $this->_tmp_dir = $tmp_dir;

        $this->_initialize();
    }

    /**
     * Open a tmp file to store the majority of the Worksheet data. If this fails,
     * for example due to write permissions, store the data in memory. This can be
     * slow for large files.
     *
     * @access private
     */
    function _initialize() {
        if ($this->_using_tmpfile == false) {
            return;
        }

        if ($this->_tmp_dir === '' && ini_get('open_basedir') === false) {
            // open_basedir restriction in effect - store data in memory
            // ToDo: Let the error actually have an effect somewhere
            $this->_using_tmpfile = false;
            return new PEAR_Error('Temp file could not be opened since open_basedir restriction in effect - please use setTmpDir() - using memory storage instead');
        }

        // Open tmp file for storing Worksheet data
        if ($this->_tmp_dir === '') {
            $fh = tmpfile();
        } else {
            // For people with open base dir restriction
            $tmpfilename = tempnam($this->_tmp_dir, "Spreadsheet_Excel_Writer");
            $fh = @fopen($tmpfilename, "w+b");
        }

        if ($fh === false) {
            // If tmpfile() fails store data in memory
            $this->_using_tmpfile = false;
        } else {
            // Store filehandle
            $this->_filehandle = $fh;
        }
    }

    /**
     * Add data to the beginning of the workbook (note the reverse order)
     * and to the end of the workbook.
     *
     * @access public
     * @see Spreadsheet_Excel_Writer_Workbook::storeWorkbook()
     * @param array $sheetnames The array of sheetnames from the Workbook this
     *                          worksheet belongs to
     */
    function close($sheetnames) {
        $num_sheets = count($sheetnames);

        /*         * *********************************************
         * Prepend in reverse order!!
         */

        // Prepend the sheet dimensions
        $this->_storeDimensions();

        // Prepend the sheet password
        $this->_storePassword();

        // Prepend the sheet protection
        $this->_storeProtect();

        // Prepend the page setup
        $this->_storeSetup();

        /* : margins are actually appended */
        // Prepend the bottom margin
        $this->_storeMarginBottom();

        // Prepend the top margin
        $this->_storeMarginTop();

        // Prepend the right margin
        $this->_storeMarginRight();

        // Prepend the left margin
        $this->_storeMarginLeft();

        // Prepend the page vertical centering
        $this->_storeVcenter();

        // Prepend the page horizontal centering
        $this->_storeHcenter();

        // Prepend the page footer
        $this->_storeFooter();

        // Prepend the page header
        $this->_storeHeader();

        // Prepend the vertical page breaks
        $this->_storeVbreak();

        // Prepend the horizontal page breaks
        $this->_storeHbreak();

        // Prepend WSBOOL
        $this->_storeWsbool();

        // Prepend GRIDSET
        $this->_storeGridset();

        //  Prepend GUTS
        if ($this->_BIFF_version == 0x0500) {
            $this->_storeGuts();
        }

        // Prepend PRINTGRIDLINES
        $this->_storePrintGridlines();

        // Prepend PRINTHEADERS
        $this->_storePrintHeaders();

        // Prepend EXTERNSHEET references
        if ($this->_BIFF_version == 0x0500) {
            for ($i = $num_sheets; $i > 0; $i--) {
                $sheetname = $sheetnames[$i - 1];
                $this->_storeExternsheet($sheetname);
            }
        }

        // Prepend the EXTERNCOUNT of external references.
        if ($this->_BIFF_version == 0x0500) {
            $this->_storeExterncount($num_sheets);
        }

        // Prepend the COLINFO records if they exist
        if (!empty($this->_colinfo)) {
            $colcount = count($this->_colinfo);
            for ($i = 0; $i < $colcount; $i++) {
                $this->_storeColinfo($this->_colinfo[$i]);
            }
            $this->_storeDefcol();
        }

        // Prepend the BOF record
        $this->_storeBof(0x0010);

        /*
         * End of prepend. Read upwards from here.
         * ********************************************* */

        // Append
        $this->_storeWindow2();
        $this->_storeZoom();
        if (!empty($this->_panes)) {
            $this->_storePanes($this->_panes);
        }
        $this->_storeSelection($this->_selection);
        $this->_storeMergedCells();
        /* : add data validity */
        /* if ($this->_BIFF_version == 0x0600) {
          $this->_storeDataValidity();
          } */
        $this->_storeEof();
    }

    /**
     * Retrieve the worksheet name.
     * This is usefull when creating worksheets without a name.
     *
     * @access public
     * @return string The worksheet's name
     */
    function getName() {
        return $this->name;
    }

    /**
     * Retrieves data from memory in one chunk, or from disk in $buffer
     * sized chunks.
     *
     * @return string The data
     */
    function getData() {
        $buffer = 4096;

        // Return data stored in memory
        if (isset($this->_data)) {
            $tmp = $this->_data;
            unset($this->_data);
            $fh = $this->_filehandle;
            if ($this->_using_tmpfile) {
                fseek($fh, 0);
            }
            return $tmp;
        }
        // Return data stored on disk
        if ($this->_using_tmpfile) {
            if ($tmp = fread($this->_filehandle, $buffer)) {
                return $tmp;
            }
        }

        // No data to return
        return '';
    }

    /**
     * Sets a merged cell range
     *
     * @access public
     * @param integer $first_row First row of the area to merge
     * @param integer $first_col First column of the area to merge
     * @param integer $last_row  Last row of the area to merge
     * @param integer $last_col  Last column of the area to merge
     */
    function setMerge($first_row, $first_col, $last_row, $last_col) {
        if (($last_row < $first_row) || ($last_col < $first_col)) {
            return;
        }
        // don't check rowmin, rowmax, etc... because we don't know when this
        // is going to be called
        $this->_merged_ranges[] = array($first_row, $first_col, $last_row, $last_col);
    }

    /**
     * Set this worksheet as a selected worksheet,
     * i.e. the worksheet has its tab highlighted.
     *
     * @access public
     */
    function select() {
        $this->selected = 1;
    }

    /**
     * Set this worksheet as the active worksheet,
     * i.e. the worksheet that is displayed when the workbook is opened.
     * Also set it as selected.
     *
     * @access public
     */
    function activate() {
        $this->selected = 1;
        $this->activesheet = $this->index;
    }

    /**
     * Set this worksheet as the first visible sheet.
     * This is necessary when there are a large number of worksheets and the
     * activated worksheet is not visible on the screen.
     *
     * @access public
     */
    function setFirstSheet() {
        $this->firstsheet = $this->index;
    }

    /**
     * Set the worksheet protection flag
     * to prevent accidental modification and to
     * hide formulas if the locked and hidden format properties have been set.
     *
     * @access public
     * @param string $password The password to use for protecting the sheet.
     */
    function protect($password) {
        $this->_protect = 1;
        $this->_password = $this->_encodePassword($password);
    }

    /**
     * Set the width of a single column or a range of columns.
     *
     * @access public
     * @param integer $firstcol first column on the range
     * @param integer $lastcol  last column on the range
     * @param integer $width    width to set
     * @param mixed   $format   The optional XF format to apply to the columns
     * @param integer $hidden   The optional hidden atribute
     * @param integer $level    The optional outline level
     */
    function setColumn($firstcol, $lastcol, $width, $format = null, $hidden = 0, $level = 0) {
        $this->_colinfo[] = array($firstcol, $lastcol, $width, &$format, $hidden, $level);

        // Set width to zero if column is hidden
        $width = ($hidden) ? 0 : $width;

        for ($col = $firstcol; $col <= $lastcol; $col++) {
            $this->col_sizes[$col] = $width;
        }
    }

    /**
     * Set which cell or cells are selected in a worksheet
     *
     * @access public
     * @param integer $first_row    first row in the selected quadrant
     * @param integer $first_column first column in the selected quadrant
     * @param integer $last_row     last row in the selected quadrant
     * @param integer $last_column  last column in the selected quadrant
     */
    function setSelection($first_row, $first_column, $last_row, $last_column) {
        $this->_selection = array($first_row, $first_column, $last_row, $last_column);
    }

    /**
     * Set panes and mark them as frozen.
     *
     * @access public
     * @param array $panes This is the only parameter received and is composed of the following:
     *                     0 => Vertical split position,
     *                     1 => Horizontal split position
     *                     2 => Top row visible
     *                     3 => Leftmost column visible
     *                     4 => Active pane
     */
    function freezePanes($panes) {
        $this->_frozen = 1;
        $this->_panes = $panes;
    }

    /**
     * Set panes and mark them as unfrozen.
     *
     * @access public
     * @param array $panes This is the only parameter received and is composed of the following:
     *                     0 => Vertical split position,
     *                     1 => Horizontal split position
     *                     2 => Top row visible
     *                     3 => Leftmost column visible
     *                     4 => Active pane
     */
    function thawPanes($panes) {
        $this->_frozen = 0;
        $this->_panes = $panes;
    }

    /**
     * Set the page orientation as portrait.
     *
     * @access public
     */
    function setPortrait() {
        $this->_orientation = 1;
    }

    /**
     * Set the page orientation as landscape.
     *
     * @access public
     */
    function setLandscape() {
        $this->_orientation = 0;
    }

    /**
     * Set the paper type. Ex. 1 = US Letter, 9 = A4
     *
     * @access public
     * @param integer $size The type of paper size to use
     */
    function setPaper($size = 0) {
        $this->_paper_size = $size;
    }

    /**
     * Set the page header caption and optional margin.
     *
     * @access public
     * @param string $string The header text
     * @param float  $margin optional head margin in inches.
     */
    function setHeader($string, $margin = 0.50) {
        if (strlen($string) >= 255) {
            //carp 'Header string must be less than 255 characters';
            return;
        }
        $this->_header = $string;
        $this->_margin_head = $margin;
    }

    /**
     * Set the page footer caption and optional margin.
     *
     * @access public
     * @param string $string The footer text
     * @param float  $margin optional foot margin in inches.
     */
    function setFooter($string, $margin = 0.50) {
        if (strlen($string) >= 255) {
            //carp 'Footer string must be less than 255 characters';
            return;
        }
        $this->_footer = $string;
        $this->_margin_foot = $margin;
    }

    /**
     * Center the page horinzontally.
     *
     * @access public
     * @param integer $center the optional value for centering. Defaults to 1 (center).
     */
    function centerHorizontally($center = 1) {
        $this->_hcenter = $center;
    }

    /**
     * Center the page vertically.
     *
     * @access public
     * @param integer $center the optional value for centering. Defaults to 1 (center).
     */
    function centerVertically($center = 1) {
        $this->_vcenter = $center;
    }

    /**
     * Set all the page margins to the same value in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMargins($margin) {
        $this->setMarginLeft($margin);
        $this->setMarginRight($margin);
        $this->setMarginTop($margin);
        $this->setMarginBottom($margin);
    }

    /**
     * Set the left and right margins to the same value in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMargins_LR($margin) {
        $this->setMarginLeft($margin);
        $this->setMarginRight($margin);
    }

    /**
     * Set the top and bottom margins to the same value in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMargins_TB($margin) {
        $this->setMarginTop($margin);
        $this->setMarginBottom($margin);
    }

    /**
     * Set the left margin in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMarginLeft($margin = 0.75) {
        $this->_margin_left = $margin;
    }

    /**
     * Set the right margin in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMarginRight($margin = 0.75) {
        $this->_margin_right = $margin;
    }

    /**
     * Set the top margin in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMarginTop($margin = 1.00) {
        $this->_margin_top = $margin;
    }

    /**
     * Set the bottom margin in inches.
     *
     * @access public
     * @param float $margin The margin to set in inches
     */
    function setMarginBottom($margin = 1.00) {
        $this->_margin_bottom = $margin;
    }

    /**
     * Set the rows to repeat at the top of each printed page.
     *
     * @access public
     * @param integer $first_row First row to repeat
     * @param integer $last_row  Last row to repeat. Optional.
     */
    function repeatRows($first_row, $last_row = null) {
        $this->title_rowmin = $first_row;
        if (isset($last_row)) { //Second row is optional
            $this->title_rowmax = $last_row;
        } else {
            $this->title_rowmax = $first_row;
        }
    }

    /**
     * Set the columns to repeat at the left hand side of each printed page.
     *
     * @access public
     * @param integer $first_col First column to repeat
     * @param integer $last_col  Last column to repeat. Optional.
     */
    function repeatColumns($first_col, $last_col = null) {
        $this->title_colmin = $first_col;
        if (isset($last_col)) { // Second col is optional
            $this->title_colmax = $last_col;
        } else {
            $this->title_colmax = $first_col;
        }
    }

    /**
     * Set the area of each worksheet that will be printed.
     *
     * @access public
     * @param integer $first_row First row of the area to print
     * @param integer $first_col First column of the area to print
     * @param integer $last_row  Last row of the area to print
     * @param integer $last_col  Last column of the area to print
     */
    function printArea($first_row, $first_col, $last_row, $last_col) {
        $this->print_rowmin = $first_row;
        $this->print_colmin = $first_col;
        $this->print_rowmax = $last_row;
        $this->print_colmax = $last_col;
    }

    /**
     * Set the option to hide gridlines on the printed page.
     *
     * @access public
     */
    function hideGridlines() {
        $this->_print_gridlines = 0;
    }

    /**
     * Set the option to hide gridlines on the worksheet (as seen on the screen).
     *
     * @access public
     */
    function hideScreenGridlines() {
        $this->_screen_gridlines = 0;
    }

    /**
     * Set the option to print the row and column headers on the printed page.
     *
     * @access public
     * @param integer $print Whether to print the headers or not. Defaults to 1 (print).
     */
    function printRowColHeaders($print = 1) {
        $this->_print_headers = $print;
    }

    /**
     * Set the vertical and horizontal number of pages that will define the maximum area printed.
     * It doesn't seem to work with OpenOffice.
     *
     * @access public
     * @param  integer $width  Maximun width of printed area in pages
     * @param  integer $height Maximun heigth of printed area in pages
     * @see setPrintScale()
     */
    function fitToPages($width, $height) {
        $this->_fit_page = 1;
        $this->_fit_width = $width;
        $this->_fit_height = $height;
    }

    /**
     * Store the horizontal page breaks on a worksheet (for printing).
     * The breaks represent the row after which the break is inserted.
     *
     * @access public
     * @param array $breaks Array containing the horizontal page breaks
     */
    function setHPagebreaks($breaks) {
        foreach ($breaks as $break) {
            array_push($this->_hbreaks, $break);
        }
    }

    /**
     * Store the vertical page breaks on a worksheet (for printing).
     * The breaks represent the column after which the break is inserted.
     *
     * @access public
     * @param array $breaks Array containing the vertical page breaks
     */
    function setVPagebreaks($breaks) {
        foreach ($breaks as $break) {
            array_push($this->_vbreaks, $break);
        }
    }

    /**
     * Set the worksheet zoom factor.
     *
     * @access public
     * @param integer $scale The zoom factor
     */
    function setZoom($scale = 100) {
        // Confine the scale to Excel's range
        if ($scale < 10 || $scale > 400) {
            $this->raiseError("Zoom factor $scale outside range: 10 <= zoom <= 400");
            $scale = 100;
        }

        $this->_zoom = floor($scale);
    }

    /**
     * Set the scale factor for the printed page.
     * It turns off the "fit to page" option
     *
     * @access public
     * @param integer $scale The optional scale factor. Defaults to 100
     */
    function setPrintScale($scale = 100) {
        // Confine the scale to Excel's range
        if ($scale < 10 || $scale > 400) {
            $this->raiseError("Print scale $scale outside range: 10 <= zoom <= 400");
            $scale = 100;
        }

        // Turn off "fit to page" option
        $this->_fit_page = 0;

        $this->_print_scale = floor($scale);
    }

    /**
     * Map to the appropriate write method acording to the token recieved.
     *
     * @access public
     * @param integer $row    The row of the cell we are writing to
     * @param integer $col    The column of the cell we are writing to
     * @param mixed   $token  What we are writing
     * @param mixed   $format The optional format to apply to the cell
     */
    function write($row, $col, $token, $format = null) {
        // Check for a cell reference in A1 notation and substitute row and column
        /* if ($_[0] =~ /^\D/) {
          @_ = $this->_substituteCellref(@_);
          } */

        if (preg_match("/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $token)) {
            // Match number
            return $this->writeNumber($row, $col, $token, $format);
        } elseif (preg_match("/^[fh]tt?p:\/\//", $token)) {
            // Match http or ftp URL
            return $this->writeUrl($row, $col, $token, '', $format);
        } elseif (preg_match("/^mailto:/", $token)) {
            // Match mailto:
            return $this->writeUrl($row, $col, $token, '', $format);
        } elseif (preg_match("/^(?:in|ex)ternal:/", $token)) {
            // Match internal or external sheet link
            return $this->writeUrl($row, $col, $token, '', $format);
        } elseif (preg_match("/^=/", $token)) {
            // Match formula
            return $this->writeFormula($row, $col, $token, $format);
        } elseif ($token == '') {
            // Match blank
            return $this->writeBlank($row, $col, $format);
        } else {
            // Default: match string
            return $this->writeString($row, $col, $token, $format);
        }
    }

    /**
     * Write an array of values as a row
     *
     * @access public
     * @param integer $row    The row we are writing to
     * @param integer $col    The first col (leftmost col) we are writing to
     * @param array   $val    The array of values to write
     * @param mixed   $format The optional format to apply to the cell
     * @return mixed PEAR_Error on failure
     */
    function writeRow($row, $col, $val, $format = null) {
        $retval = '';
        if (is_array($val)) {
            foreach ($val as $v) {
                if (is_array($v)) {
                    $this->writeCol($row, $col, $v, $format);
                } else {
                    $this->write($row, $col, $v, $format);
                }
                $col++;
            }
        } else {
            $retval = new PEAR_Error('$val needs to be an array');
        }
        return($retval);
    }

    /**
     * Write an array of values as a column
     *
     * @access public
     * @param integer $row    The first row (uppermost row) we are writing to
     * @param integer $col    The col we are writing to
     * @param array   $val    The array of values to write
     * @param mixed   $format The optional format to apply to the cell
     * @return mixed PEAR_Error on failure
     */
    function writeCol($row, $col, $val, $format = null) {
        $retval = '';
        if (is_array($val)) {
            foreach ($val as $v) {
                $this->write($row, $col, $v, $format);
                $row++;
            }
        } else {
            $retval = new PEAR_Error('$val needs to be an array');
        }
        return($retval);
    }

    /**
     * Returns an index to the XF record in the workbook
     *
     * @access private
     * @param mixed &$format The optional XF format
     * @return integer The XF record index
     */
    function _XF(&$format) {
        if ($format) {
            return($format->getXfIndex());
        } else {
            return(0x0F);
        }
    }

    /*     * ****************************************************************************
     * ******************************************************************************
     *
     * Internal methods
     */

    /**
     * Store Worksheet data in memory using the parent's class append() or to a
     * temporary file, the default.
     *
     * @access private
     * @param string $data The binary data to append
     */
    function _append($data) {
        if ($this->_using_tmpfile) {
            // Add CONTINUE records if necessary
            if (strlen($data) > $this->_limit) {
                $data = $this->_addContinue($data);
            }
            fwrite($this->_filehandle, $data);
            $this->_datasize += strlen($data);
        } else {
            parent::_append($data);
        }
    }

    /**
     * Substitute an Excel cell reference in A1 notation for  zero based row and
     * column values in an argument list.
     *
     * Ex: ("A4", "Hello") is converted to (3, 0, "Hello").
     *
     * @access private
     * @param string $cell The cell reference. Or range of cells.
     * @return array
     */
    function _substituteCellref($cell) {
        $cell = strtoupper($cell);

        // Convert a column range: 'A:A' or 'B:G'
        if (preg_match("/([A-I]?[A-Z]):([A-I]?[A-Z])/", $cell, $match)) {
            list($no_use, $col1) = $this->_cellToRowcol($match[1] . '1'); // Add a dummy row
            list($no_use, $col2) = $this->_cellToRowcol($match[2] . '1'); // Add a dummy row
            return(array($col1, $col2));
        }

        // Convert a cell range: 'A1:B7'
        if (preg_match("/\$?([A-I]?[A-Z]\$?\d+):\$?([A-I]?[A-Z]\$?\d+)/", $cell, $match)) {
            list($row1, $col1) = $this->_cellToRowcol($match[1]);
            list($row2, $col2) = $this->_cellToRowcol($match[2]);
            return(array($row1, $col1, $row2, $col2));
        }

        // Convert a cell reference: 'A1' or 'AD2000'
        if (preg_match("/\$?([A-I]?[A-Z]\$?\d+)/", $cell)) {
            list($row1, $col1) = $this->_cellToRowcol($match[1]);
            return(array($row1, $col1));
        }

        //  use real error codes
        $this->raiseError("Unknown cell reference $cell", 0, PEAR_ERROR_DIE);
    }

    /**
     * Convert an Excel cell reference in A1 notation to a zero based row and column
     * reference; converts C1 to (0, 2).
     *
     * @access private
     * @param string $cell The cell reference.
     * @return array containing (row, column)
     */
    function _cellToRowcol($cell) {
        preg_match("/\$?([A-I]?[A-Z])\$?(\d+)/", $cell, $match);
        $col = $match[1];
        $row = $match[2];

        // Convert base26 column string to number
        $chars = split('', $col);
        $expn = 0;
        $col = 0;

        while ($chars) {
            $char = array_pop($chars);        // LS char first
            $col += ( ord($char) - ord('A') + 1) * pow(26, $expn);
            $expn++;
        }

        // Convert 1-index to zero-index
        $row--;
        $col--;

        return(array($row, $col));
    }

    /**
     * Based on the algorithm provided by Daniel Rentz of OpenOffice.
     *
     * @access private
     * @param string $plaintext The password to be encoded in plaintext.
     * @return string The encoded password
     */
    function _encodePassword($plaintext) {
        $password = 0x0000;
        $i = 1;       // char position
        // split the plain text password in its component characters
        $chars = preg_split('//', $plaintext, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $value = ord($char) << $i;   // shifted ASCII value
            $rotated_bits = $value >> 15;       // rotated bits beyond bit 15
            $value &= 0x7fff;             // first 15 bits
            $password ^= ( $value | $rotated_bits);
            $i++;
        }

        $password ^= strlen($plaintext);
        $password ^= 0xCE4B;

        return($password);
    }

    /**
     * This method sets the properties for outlining and grouping. The defaults
     * correspond to Excel's defaults.
     *
     * @param bool $visible
     * @param bool $symbols_below
     * @param bool $symbols_right
     * @param bool $auto_style
     */
    function setOutline($visible = true, $symbols_below = true, $symbols_right = true, $auto_style = false) {
        $this->_outline_on = $visible;
        $this->_outline_below = $symbols_below;
        $this->_outline_right = $symbols_right;
        $this->_outline_style = $auto_style;

        // Ensure this is a boolean vale for Window2
        if ($this->_outline_on) {
            $this->_outline_on = 1;
        }
    }

    /*     * ****************************************************************************
     * ******************************************************************************
     *
     * BIFF RECORDS
     */

    /**
     * Write a double to the specified row and column (zero indexed).
     * An integer can be written as a double. Excel will display an
     * integer. $format is optional.
     *
     * Returns  0 : normal termination
     *         -2 : row or column out of range
     *
     * @access public
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param float   $num    The number to write
     * @param mixed   $format The optional XF format
     * @return integer
     */
    function writeNumber($row, $col, $num, $format = null) {
        $record = 0x0203;                 // Record identifier
        $length = 0x000E;                 // Number of bytes to follow

        $xf = $this->_XF($format);    // The cell format
        // Check that row and col are valid and store max and min values
        if ($row >= $this->_xls_rowmax) {
            return(-2);
        }
        if ($col >= $this->_xls_colmax) {
            return(-2);
        }
        if ($row < $this->_dim_rowmin) {
            $this->_dim_rowmin = $row;
        }
        if ($row > $this->_dim_rowmax) {
            $this->_dim_rowmax = $row;
        }
        if ($col < $this->_dim_colmin) {
            $this->_dim_colmin = $col;
        }
        if ($col > $this->_dim_colmax) {
            $this->_dim_colmax = $col;
        }

        $header = pack("vv", $record, $length);
        $data = pack("vvv", $row, $col, $xf);
        $xl_double = pack("d", $num);
        if ($this->_byte_order) { // if it's Big Endian
            $xl_double = strrev($xl_double);
        }

        $this->_append($header . $data . $xl_double);
        return(0);
    }

    /**
     * Write a string to the specified row and column (zero indexed).
     * NOTE: there is an Excel 5 defined limit of 255 characters.
     * $format is optional.
     * Returns  0 : normal termination
     *         -2 : row or column out of range
     *         -3 : long string truncated to 255 chars
     *
     * @access public
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $str    The string to write
     * @param mixed   $format The XF format for the cell
     * @return integer
     */
    function writeString($row, $col, $str, $format = null) {
        if ($this->_BIFF_version == 0x0600) {
            return $this->writeStringBIFF8($row, $col, $str, $format);
        }
        $strlen = strlen($str);
        $record = 0x0204;                   // Record identifier
        $length = 0x0008 + $strlen;         // Bytes to follow
        $xf = $this->_XF($format);      // The cell format

        $str_error = 0;

        // Check that row and col are valid and store max and min values
        if ($row >= $this->_xls_rowmax) {
            return(-2);
        }
        if ($col >= $this->_xls_colmax) {
            return(-2);
        }
        if ($row < $this->_dim_rowmin) {
            $this->_dim_rowmin = $row;
        }
        if ($row > $this->_dim_rowmax) {
            $this->_dim_rowmax = $row;
        }
        if ($col < $this->_dim_colmin) {
            $this->_dim_colmin = $col;
        }
        if ($col > $this->_dim_colmax) {
            $this->_dim_colmax = $col;
        }

        if ($strlen > $this->_xls_strmax) { // LABEL must be < 255 chars
            $str = substr($str, 0, $this->_xls_strmax);
            $length = 0x0008 + $this->_xls_strmax;
            $strlen = $this->_xls_strmax;
            $str_error = -3;
        }

        $header = pack("vv", $record, $length);
        $data = pack("vvvv", $row, $col, $xf, $strlen);
        $this->_append($header . $data . $str);
        return($str_error);
    }

    /**
     * Sets Input Encoding for writing strings
     *
     * @access public
     * @param string $encoding The encoding. Ex: 'UTF-16LE', 'utf-8', 'ISO-859-7'
     */
    function setInputEncoding($encoding) {
        if ($encoding != 'UTF-16LE' && !function_exists('iconv')) {
            $this->raiseError("Using an input encoding other than UTF-16LE requires PHP support for iconv");
        }
        $this->_input_encoding = $encoding;
    }

    /**
     * Write a string to the specified row and column (zero indexed).
     * This is the BIFF8 version (no 255 chars limit).
     * $format is optional.
     * Returns  0 : normal termination
     *         -2 : row or column out of range
     *         -3 : long string truncated to 255 chars
     *
     * @access public
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $str    The string to write
     * @param mixed   $format The XF format for the cell
     * @return integer
     */
    function writeStringBIFF8($row, $col, $str, $format = null) {
        if ($this->_input_encoding == 'UTF-16LE') {
            $strlen = function_exists('mb_strlen') ? mb_strlen($str, 'UTF-16LE') : (strlen($str) / 2);
            $encoding = 0x1;
        } elseif ($this->_input_encoding != '') {
            $str = iconv($this->_input_encoding, 'UTF-16LE', $str);
            $strlen = function_exists('mb_strlen') ? mb_strlen($str, 'UTF-16LE') : (strlen($str) / 2);
            $encoding = 0x1;
        } else {
            $strlen = strlen($str);
            $encoding = 0x0;
        }
        $record = 0x00FD;                   // Record identifier
        $length = 0x000A;                   // Bytes to follow
        $xf = $this->_XF($format);      // The cell format

        $str_error = 0;

        // Check that row and col are valid and store max and min values
        if ($this->_checkRowCol($row, $col) == false) {
            return -2;
        }

        $str = pack('vC', $strlen, $encoding) . $str;

        /* check if string is already present */
        if (!isset($this->_str_table[$str])) {
            $this->_str_table[$str] = $this->_str_unique++;
        }
        $this->_str_total++;

        $header = pack('vv', $record, $length);
        $data = pack('vvvV', $row, $col, $xf, $this->_str_table[$str]);
        $this->_append($header . $data);
        return $str_error;
    }

    /**
     * Check row and col before writing to a cell, and update the sheet's
     * dimensions accordingly
     *
     * @access private
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @return boolean true for success, false if row and/or col are grester
     *                 then maximums allowed.
     */
    function _checkRowCol($row, $col) {
        if ($row >= $this->_xls_rowmax) {
            return false;
        }
        if ($col >= $this->_xls_colmax) {
            return false;
        }
        if ($row < $this->_dim_rowmin) {
            $this->_dim_rowmin = $row;
        }
        if ($row > $this->_dim_rowmax) {
            $this->_dim_rowmax = $row;
        }
        if ($col < $this->_dim_colmin) {
            $this->_dim_colmin = $col;
        }
        if ($col > $this->_dim_colmax) {
            $this->_dim_colmax = $col;
        }
        return true;
    }

    /**
     * Writes a note associated with the cell given by the row and column.
     * NOTE records don't have a length limit.
     *
     * @access public
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $note   The note to write
     */
    function writeNote($row, $col, $note) {
        $note_length = strlen($note);
        $record = 0x001C;                // Record identifier
        $max_length = 2048;                  // Maximun length for a NOTE record
        //$length      = 0x0006 + $note_length;    // Bytes to follow
        // Check that row and col are valid and store max and min values
        if ($row >= $this->_xls_rowmax) {
            return(-2);
        }
        if ($col >= $this->_xls_colmax) {
            return(-2);
        }
        if ($row < $this->_dim_rowmin) {
            $this->_dim_rowmin = $row;
        }
        if ($row > $this->_dim_rowmax) {
            $this->_dim_rowmax = $row;
        }
        if ($col < $this->_dim_colmin) {
            $this->_dim_colmin = $col;
        }
        if ($col > $this->_dim_colmax) {
            $this->_dim_colmax = $col;
        }

        // Length for this record is no more than 2048 + 6
        $length = 0x0006 + min($note_length, 2048);
        $header = pack("vv", $record, $length);
        $data = pack("vvv", $row, $col, $note_length);
        $this->_append($header . $data . substr($note, 0, 2048));

        for ($i = $max_length; $i < $note_length; $i += $max_length) {
            $chunk = substr($note, $i, $max_length);
            $length = 0x0006 + strlen($chunk);
            $header = pack("vv", $record, $length);
            $data = pack("vvv", -1, 0, strlen($chunk));
            $this->_append($header . $data . $chunk);
        }
        return(0);
    }

    /**
     * Write a blank cell to the specified row and column (zero indexed).
     * A blank cell is used to specify formatting without adding a string
     * or a number.
     *
     * A blank cell without a format serves no purpose. Therefore, we don't write
     * a BLANK record unless a format is specified.
     *
     * Returns  0 : normal termination (including no format)
     *         -1 : insufficient number of arguments
     *         -2 : row or column out of range
     *
     * @access public
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param mixed   $format The XF format
     */
    function writeBlank($row, $col, $format) {
        // Don't write a blank cell unless it has a format
        if (!$format) {
            return(0);
        }

        $record = 0x0201;                 // Record identifier
        $length = 0x0006;                 // Number of bytes to follow
        $xf = $this->_XF($format);    // The cell format
        // Check that row and col are valid and store max and min values
        if ($row >= $this->_xls_rowmax) {
            return(-2);
        }
        if ($col >= $this->_xls_colmax) {
            return(-2);
        }
        if ($row < $this->_dim_rowmin) {
            $this->_dim_rowmin = $row;
        }
        if ($row > $this->_dim_rowmax) {
            $this->_dim_rowmax = $row;
        }
        if ($col < $this->_dim_colmin) {
            $this->_dim_colmin = $col;
        }
        if ($col > $this->_dim_colmax) {
            $this->_dim_colmax = $col;
        }

        $header = pack("vv", $record, $length);
        $data = pack("vvv", $row, $col, $xf);
        $this->_append($header . $data);
        return 0;
    }

    /**
     * Write a formula to the specified row and column (zero indexed).
     * The textual representation of the formula is passed to the parser in
     * Parser.php which returns a packed binary string.
     *
     * Returns  0 : normal termination
     *         -1 : formula errors (bad formula)
     *         -2 : row or column out of range
     *
     * @access public
     * @param integer $row     Zero indexed row
     * @param integer $col     Zero indexed column
     * @param string  $formula The formula text string
     * @param mixed   $format  The optional XF format
     * @return integer
     */
    function writeFormula($row, $col, $formula, $format = null) {
        $record = 0x0006;     // Record identifier
        // Excel normally stores the last calculated value of the formula in $num.
        // Clearly we are not in a position to calculate this a priori. Instead
        // we set $num to zero and set the option flags in $grbit to ensure
        // automatic calculation of the formula when the file is opened.
        //
        $xf = $this->_XF($format); // The cell format
        $num = 0x00;                // Current value of formula
        $grbit = 0x03;                // Option flags
        $unknown = 0x0000;              // Must be zero
        // Check that row and col are valid and store max and min values
        if ($this->_checkRowCol($row, $col) == false) {
            return -2;
        }

        // Strip the '=' or '@' sign at the beginning of the formula string
        if (preg_match("/^=/", $formula)) {
            $formula = preg_replace("/(^=)/", "", $formula);
        } elseif (preg_match("/^@/", $formula)) {
            $formula = preg_replace("/(^@)/", "", $formula);
        } else {
            // Error handling
            $this->writeString($row, $col, 'Unrecognised character for formula');
            return -1;
        }

        // Parse the formula using the parser in Parser.php
        $error = $this->_parser->parse($formula);
        if ($this->isError($error)) {
            $this->writeString($row, $col, $error->getMessage());
            return -1;
        }

        $formula = $this->_parser->toReversePolish();
        if ($this->isError($formula)) {
            $this->writeString($row, $col, $formula->getMessage());
            return -1;
        }

        $formlen = strlen($formula);    // Length of the binary string
        $length = 0x16 + $formlen;     // Length of the record data

        $header = pack("vv", $record, $length);
        $data = pack("vvvdvVv", $row, $col, $xf, $num,
                        $grbit, $unknown, $formlen);

        $this->_append($header . $data . $formula);
        return 0;
    }

    /**
     * Write a hyperlink.
     * This is comprised of two elements: the visible label and
     * the invisible link. The visible label is the same as the link unless an
     * alternative string is specified. The label is written using the
     * writeString() method. Therefore the 255 characters string limit applies.
     * $string and $format are optional.
     *
     * The hyperlink can be to a http, ftp, mail, internal sheet (not yet), or external
     * directory url.
     *
     * Returns  0 : normal termination
     *         -2 : row or column out of range
     *         -3 : long string truncated to 255 chars
     *
     * @access public
     * @param integer $row    Row
     * @param integer $col    Column
     * @param string  $url    URL string
     * @param string  $string Alternative label
     * @param mixed   $format The cell format
     * @return integer
     */
    function writeUrl($row, $col, $url, $string = '', $format = null) {
        // Add start row and col to arg list
        return($this->_writeUrlRange($row, $col, $row, $col, $url, $string, $format));
    }

    /**
     * This is the more general form of writeUrl(). It allows a hyperlink to be
     * written to a range of cells. This function also decides the type of hyperlink
     * to be written. These are either, Web (http, ftp, mailto), Internal
     * (Sheet1!A1) or external ('c:\temp\foo.xls#Sheet1!A1').
     *
     * @access private
     * @see writeUrl()
     * @param integer $row1   Start row
     * @param integer $col1   Start column
     * @param integer $row2   End row
     * @param integer $col2   End column
     * @param string  $url    URL string
     * @param string  $string Alternative label
     * @param mixed   $format The cell format
     * @return integer
     */
    function _writeUrlRange($row1, $col1, $row2, $col2, $url, $string = '', $format = null) {

        // Check for internal/external sheet links or default to web link
        if (preg_match('[^internal:]', $url)) {
            return($this->_writeUrlInternal($row1, $col1, $row2, $col2, $url, $string, $format));
        }
        if (preg_match('[^external:]', $url)) {
            return($this->_writeUrlExternal($row1, $col1, $row2, $col2, $url, $string, $format));
        }
        return($this->_writeUrlWeb($row1, $col1, $row2, $col2, $url, $string, $format));
    }

    /**
     * Used to write http, ftp and mailto hyperlinks.
     * The link type ($options) is 0x03 is the same as absolute dir ref without
     * sheet. However it is differentiated by the $unknown2 data stream.
     *
     * @access private
     * @see writeUrl()
     * @param integer $row1   Start row
     * @param integer $col1   Start column
     * @param integer $row2   End row
     * @param integer $col2   End column
     * @param string  $url    URL string
     * @param string  $str    Alternative label
     * @param mixed   $format The cell format
     * @return integer
     */
    function _writeUrlWeb($row1, $col1, $row2, $col2, $url, $str, $format = null) {
        $record = 0x01B8;                       // Record identifier
        $length = 0x00000;                      // Bytes to follow

        if (!$format) {
            $format = $this->_url_format;
        }

        // Write the visible label using the writeString() method.
        if ($str == '') {
            $str = $url;
        }
        $str_error = $this->writeString($row1, $col1, $str, $format);
        if (($str_error == -2) || ($str_error == -3)) {
            return $str_error;
        }

        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack("H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000");
        $unknown2 = pack("H*", "E0C9EA79F9BACE118C8200AA004BA90B");

        // Pack the option flags
        $options = pack("V", 0x03);

        // Convert URL to a null terminated wchar string
        $url = join("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url = $url . "\0\0\0";

        // Pack the length of the URL
        $url_len = pack("V", strlen($url));

        // Calculate the data length
        $length = 0x34 + strlen($url);

        // Pack the header data
        $header = pack("vv", $record, $length);
        $data = pack("vvvv", $row1, $row2, $col1, $col2);

        // Write the packed data
        $this->_append($header . $data .
                $unknown1 . $options .
                $unknown2 . $url_len . $url);
        return($str_error);
    }

    /**
     * Used to write internal reference hyperlinks such as "Sheet1!A1".
     *
     * @access private
     * @see writeUrl()
     * @param integer $row1   Start row
     * @param integer $col1   Start column
     * @param integer $row2   End row
     * @param integer $col2   End column
     * @param string  $url    URL string
     * @param string  $str    Alternative label
     * @param mixed   $format The cell format
     * @return integer
     */
    function _writeUrlInternal($row1, $col1, $row2, $col2, $url, $str, $format = null) {
        $record = 0x01B8;                       // Record identifier
        $length = 0x00000;                      // Bytes to follow

        if (!$format) {
            $format = $this->_url_format;
        }

        // Strip URL type
        $url = preg_replace('/^internal:/', '', $url);

        // Write the visible label
        if ($str == '') {
            $str = $url;
        }
        $str_error = $this->writeString($row1, $col1, $str, $format);
        if (($str_error == -2) || ($str_error == -3)) {
            return $str_error;
        }

        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack("H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000");

        // Pack the option flags
        $options = pack("V", 0x08);

        // Convert the URL type and to a null terminated wchar string
        $url = join("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url = $url . "\0\0\0";

        // Pack the length of the URL as chars (not wchars)
        $url_len = pack("V", floor(strlen($url) / 2));

        // Calculate the data length
        $length = 0x24 + strlen($url);

        // Pack the header data
        $header = pack("vv", $record, $length);
        $data = pack("vvvv", $row1, $row2, $col1, $col2);

        // Write the packed data
        $this->_append($header . $data .
                $unknown1 . $options .
                $url_len . $url);
        return($str_error);
    }

    /**
     * Write links to external directory names such as 'c:\foo.xls',
     * c:\foo.xls#Sheet1!A1', '../../foo.xls'. and '../../foo.xls#Sheet1!A1'.
     *
     * Note: Excel writes some relative links with the $dir_long string. We ignore
     * these cases for the sake of simpler code.
     *
     * @access private
     * @see writeUrl()
     * @param integer $row1   Start row
     * @param integer $col1   Start column
     * @param integer $row2   End row
     * @param integer $col2   End column
     * @param string  $url    URL string
     * @param string  $str    Alternative label
     * @param mixed   $format The cell format
     * @return integer
     */
    function _writeUrlExternal($row1, $col1, $row2, $col2, $url, $str, $format = null) {
        // Network drives are different. We will handle them separately
        // MS/Novell network drives and shares start with \\
        if (preg_match('[^external:\\\\]', $url)) {
            return; //($this->_writeUrlExternal_net($row1, $col1, $row2, $col2, $url, $str, $format));
        }

        $record = 0x01B8;                       // Record identifier
        $length = 0x00000;                      // Bytes to follow

        if (!$format) {
            $format = $this->_url_format;
        }

        // Strip URL type and change Unix dir separator to Dos style (if needed)
        //
        $url = preg_replace('/^external:/', '', $url);
        $url = preg_replace('/\//', "\\", $url);

        // Write the visible label
        if ($str == '') {
            $str = preg_replace('/\#/', ' - ', $url);
        }
        $str_error = $this->writeString($row1, $col1, $str, $format);
        if (($str_error == -2) or ($str_error == -3)) {
            return $str_error;
        }

        // Determine if the link is relative or absolute:
        //   relative if link contains no dir separator, "somefile.xls"
        //   relative if link starts with up-dir, "..\..\somefile.xls"
        //   otherwise, absolute

        $absolute = 0x02; // Bit mask
        if (!preg_match("/\\\/", $url)) {
            $absolute = 0x00;
        }
        if (preg_match("/^\.\.\\\/", $url)) {
            $absolute = 0x00;
        }
        $link_type = 0x01 | $absolute;

        // Determine if the link contains a sheet reference and change some of the
        // parameters accordingly.
        // Split the dir name and sheet name (if it exists)
        /* if (preg_match("/\#/", $url)) {
          list($dir_long, $sheet) = split("\#", $url);
          } else {
          $dir_long = $url;
          }

          if (isset($sheet)) {
          $link_type |= 0x08;
          $sheet_len  = pack("V", strlen($sheet) + 0x01);
          $sheet      = join("\0", split('', $sheet));
          $sheet     .= "\0\0\0";
          } else {
          $sheet_len   = '';
          $sheet       = '';
          } */
        $dir_long = $url;
        if (preg_match("/\#/", $url)) {
            $link_type |= 0x08;
        }



        // Pack the link type
        $link_type = pack("V", $link_type);

        // Calculate the up-level dir count e.g.. (..\..\..\ == 3)
        $up_count = preg_match_all("/\.\.\\\/", $dir_long, $useless);
        $up_count = pack("v", $up_count);

        // Store the short dos dir name (null terminated)
        $dir_short = preg_replace("/\.\.\\\/", '', $dir_long) . "\0";

        // Store the long dir name as a wchar string (non-null terminated)
        //$dir_long       = join("\0", split('', $dir_long));
        $dir_long = $dir_long . "\0";

        // Pack the lengths of the dir strings
        $dir_short_len = pack("V", strlen($dir_short));
        $dir_long_len = pack("V", strlen($dir_long));
        $stream_len = pack("V", 0); //strlen($dir_long) + 0x06);
        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack("H*", 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
        $unknown2 = pack("H*", '0303000000000000C000000000000046');
        $unknown3 = pack("H*", 'FFFFADDE000000000000000000000000000000000000000');
        $unknown4 = pack("v", 0x03);

        // Pack the main data stream
        $data = pack("vvvv", $row1, $row2, $col1, $col2) .
                $unknown1 .
                $link_type .
                $unknown2 .
                $up_count .
                $dir_short_len .
                $dir_short .
                $unknown3 .
                $stream_len; /* .
          $dir_long_len .
          $unknown4     .
          $dir_long     .
          $sheet_len    .
          $sheet        ; */

        // Pack the header data
        $length = strlen($data);
        $header = pack("vv", $record, $length);

        // Write the packed data
        $this->_append($header . $data);
        return($str_error);
    }

    /**
     * This method is used to set the height and format for a row.
     *
     * @access public
     * @param integer $row    The row to set
     * @param integer $height Height we are giving to the row.
     *                        Use null to set XF without setting height
     * @param mixed   $format XF format we are giving to the row
     * @param bool    $hidden The optional hidden attribute
     * @param integer $level  The optional outline level for row, in range [0,7]
     */
    function setRow($row, $height, $format = null, $hidden = false, $level = 0) {
        $record = 0x0208;               // Record identifier
        $length = 0x0010;               // Number of bytes to follow

        $colMic = 0x0000;               // First defined column
        $colMac = 0x0000;               // Last defined column
        $irwMac = 0x0000;               // Used by Excel to optimise loading
        $reserved = 0x0000;               // Reserved
        $grbit = 0x0000;               // Option flags
        $ixfe = $this->_XF($format);  // XF index
        // set _row_sizes so _sizeRow() can use it
        $this->_row_sizes[$row] = $height;

        // Use setRow($row, null, $XF) to set XF format without setting height
        if ($height != null) {
            $miyRw = $height * 20;  // row height
        } else {
            $miyRw = 0xff;          // default row height is 256
        }

        $level = max(0, min($level, 7));  // level should be between 0 and 7
        $this->_outline_row_level = max($level, $this->_outline_row_level);


        // Set the options flags. fUnsynced is used to show that the font and row
        // heights are not compatible. This is usually the case for WriteExcel.
        // The collapsed flag 0x10 doesn't seem to be used to indicate that a row
        // is collapsed. Instead it is used to indicate that the previous row is
        // collapsed. The zero height flag, 0x20, is used to collapse a row.

        $grbit |= $level;
        if ($hidden) {
            $grbit |= 0x0020;
        }
        $grbit |= 0x0040; // fUnsynced
        if ($format) {
            $grbit |= 0x0080;
        }
        $grbit |= 0x0100;

        $header = pack("vv", $record, $length);
        $data = pack("vvvvvvvv", $row, $colMic, $colMac, $miyRw,
                        $irwMac, $reserved, $grbit, $ixfe);
        $this->_append($header . $data);
    }

    /**
     * Writes Excel DIMENSIONS to define the area in which there is data.
     *
     * @access private
     */
    function _storeDimensions() {
        $record = 0x0200;                 // Record identifier
        $row_min = $this->_dim_rowmin;     // First row
        $row_max = $this->_dim_rowmax + 1; // Last row plus 1
        $col_min = $this->_dim_colmin;     // First column
        $col_max = $this->_dim_colmax + 1; // Last column plus 1
        $reserved = 0x0000;                 // Reserved by Excel

        if ($this->_BIFF_version == 0x0500) {
            $length = 0x000A;               // Number of bytes to follow
            $data = pack("vvvvv", $row_min, $row_max,
                            $col_min, $col_max, $reserved);
        } elseif ($this->_BIFF_version == 0x0600) {
            $length = 0x000E;
            $data = pack("VVvvv", $row_min, $row_max,
                            $col_min, $col_max, $reserved);
        }
        $header = pack("vv", $record, $length);
        $this->_prepend($header . $data);
    }

    /**
     * Write BIFF record Window2.
     *
     * @access private
     */
    function _storeWindow2() {
        $record = 0x023E;     // Record identifier
        if ($this->_BIFF_version == 0x0500) {
            $length = 0x000A;     // Number of bytes to follow
        } elseif ($this->_BIFF_version == 0x0600) {
            $length = 0x0012;
        }

        $grbit = 0x00B6;     // Option flags
        $rwTop = 0x0000;     // Top row visible in window
        $colLeft = 0x0000;     // Leftmost column visible in window
        // The options flags that comprise $grbit
        $fDspFmla = 0;                     // 0 - bit
        $fDspGrid = $this->_screen_gridlines; // 1
        $fDspRwCol = 1;                     // 2
        $fFrozen = $this->_frozen;        // 3
        $fDspZeros = 1;                     // 4
        $fDefaultHdr = 1;                     // 5
        $fArabic = 0;                     // 6
        $fDspGuts = $this->_outline_on;    // 7
        $fFrozenNoSplit = 0;                     // 0 - bit
        $fSelected = $this->selected;       // 1
        $fPaged = 1;                     // 2

        $grbit = $fDspFmla;
        $grbit |= $fDspGrid << 1;
        $grbit |= $fDspRwCol << 2;
        $grbit |= $fFrozen << 3;
        $grbit |= $fDspZeros << 4;
        $grbit |= $fDefaultHdr << 5;
        $grbit |= $fArabic << 6;
        $grbit |= $fDspGuts << 7;
        $grbit |= $fFrozenNoSplit << 8;
        $grbit |= $fSelected << 9;
        $grbit |= $fPaged << 10;

        $header = pack("vv", $record, $length);
        $data = pack("vvv", $grbit, $rwTop, $colLeft);
        //  !!!
        if ($this->_BIFF_version == 0x0500) {
            $rgbHdr = 0x00000000; // Row/column heading and gridline color
            $data .= pack("V", $rgbHdr);
        } elseif ($this->_BIFF_version == 0x0600) {
            $rgbHdr = 0x0040; // Row/column heading and gridline color index
            $zoom_factor_page_break = 0x0000;
            $zoom_factor_normal = 0x0000;
            $data .= pack("vvvvV", $rgbHdr, 0x0000, $zoom_factor_page_break, $zoom_factor_normal, 0x00000000);
        }
        $this->_append($header . $data);
    }

    /**
     * Write BIFF record DEFCOLWIDTH if COLINFO records are in use.
     *
     * @access private
     */
    function _storeDefcol() {
        $record = 0x0055;      // Record identifier
        $length = 0x0002;      // Number of bytes to follow
        $colwidth = 0x0008;      // Default column width

        $header = pack("vv", $record, $length);
        $data = pack("v", $colwidth);
        $this->_prepend($header . $data);
    }

    /**
     * Write BIFF record COLINFO to define column widths
     *
     * Note: The SDK says the record length is 0x0B but Excel writes a 0x0C
     * length record.
     *
     * @access private
     * @param array $col_array This is the only parameter received and is composed of the following:
     *                0 => First formatted column,
     *                1 => Last formatted column,
     *                2 => Col width (8.43 is Excel default),
     *                3 => The optional XF format of the column,
     *                4 => Option flags.
     *                5 => Optional outline level
     */
    function _storeColinfo($col_array) {
        if (isset($col_array[0])) {
            $colFirst = $col_array[0];
        }
        if (isset($col_array[1])) {
            $colLast = $col_array[1];
        }
        if (isset($col_array[2])) {
            $coldx = $col_array[2];
        } else {
            $coldx = 8.43;
        }
        if (isset($col_array[3])) {
            $format = $col_array[3];
        } else {
            $format = 0;
        }
        if (isset($col_array[4])) {
            $grbit = $col_array[4];
        } else {
            $grbit = 0;
        }
        if (isset($col_array[5])) {
            $level = $col_array[5];
        } else {
            $level = 0;
        }
        $record = 0x007D;          // Record identifier
        $length = 0x000B;          // Number of bytes to follow

        $coldx += 0.72;            // Fudge. Excel subtracts 0.72 !?
        $coldx *= 256;             // Convert to units of 1/256 of a char

        $ixfe = $this->_XF($format);
        $reserved = 0x00;            // Reserved

        $level = max(0, min($level, 7));
        $grbit |= $level << 8;

        $header = pack("vv", $record, $length);
        $data = pack("vvvvvC", $colFirst, $colLast, $coldx,
                        $ixfe, $grbit, $reserved);
        $this->_prepend($header . $data);
    }

    /**
     * Write BIFF record SELECTION.
     *
     * @access private
     * @param array $array array containing ($rwFirst,$colFirst,$rwLast,$colLast)
     * @see setSelection()
     */
    function _storeSelection($array) {
        list($rwFirst, $colFirst, $rwLast, $colLast) = $array;
        $record = 0x001D;                  // Record identifier
        $length = 0x000F;                  // Number of bytes to follow

        $pnn = $this->_active_pane;     // Pane position
        $rwAct = $rwFirst;                // Active row
        $colAct = $colFirst;               // Active column
        $irefAct = 0;                       // Active cell ref
        $cref = 1;                       // Number of refs

        if (!isset($rwLast)) {
            $rwLast = $rwFirst;       // Last  row in reference
        }
        if (!isset($colLast)) {
            $colLast = $colFirst;      // Last  col in reference
        }

        // Swap last row/col for first row/col as necessary
        if ($rwFirst > $rwLast) {
            list($rwFirst, $rwLast) = array($rwLast, $rwFirst);
        }

        if ($colFirst > $colLast) {
            list($colFirst, $colLast) = array($colLast, $colFirst);
        }

        $header = pack("vv", $record, $length);
        $data = pack("CvvvvvvCC", $pnn, $rwAct, $colAct,
                        $irefAct, $cref,
                        $rwFirst, $rwLast,
                        $colFirst, $colLast);
        $this->_append($header . $data);
    }

    /**
     * Store the MERGEDCELLS record for all ranges of merged cells
     *
     * @access private
     */
    function _storeMergedCells() {
        // if there are no merged cell ranges set, return
        if (count($this->_merged_ranges) == 0) {
            return;
        }
        $record = 0x00E5;
        $length = 2 + count($this->_merged_ranges) * 8;

        $header = pack('vv', $record, $length);
        $data = pack('v', count($this->_merged_ranges));
        foreach ($this->_merged_ranges as $range) {
            $data .= pack('vvvv', $range[0], $range[2], $range[1], $range[3]);
        }
        $this->_append($header . $data);
    }

    /**
     * Write BIFF record EXTERNCOUNT to indicate the number of external sheet
     * references in a worksheet.
     *
     * Excel only stores references to external sheets that are used in formulas.
     * For simplicity we store references to all the sheets in the workbook
     * regardless of whether they are used or not. This reduces the overall
     * complexity and eliminates the need for a two way dialogue between the formula
     * parser the worksheet objects.
     *
     * @access private
     * @param integer $count The number of external sheet references in this worksheet
     */
    function _storeExterncount($count) {
        $record = 0x0016;          // Record identifier
        $length = 0x0002;          // Number of bytes to follow

        $header = pack("vv", $record, $length);
        $data = pack("v", $count);
        $this->_prepend($header . $data);
    }

    /**
     * Writes the Excel BIFF EXTERNSHEET record. These references are used by
     * formulas. A formula references a sheet name via an index. Since we store a
     * reference to all of the external worksheets the EXTERNSHEET index is the same
     * as the worksheet index.
     *
     * @access private
     * @param string $sheetname The name of a external worksheet
     */
    function _storeExternsheet($sheetname) {
        $record = 0x0017;         // Record identifier
        // References to the current sheet are encoded differently to references to
        // external sheets.
        //
        if ($this->name == $sheetname) {
            $sheetname = '';
            $length = 0x02;  // The following 2 bytes
            $cch = 1;     // The following byte
            $rgch = 0x02;  // Self reference
        } else {
            $length = 0x02 + strlen($sheetname);
            $cch = strlen($sheetname);
            $rgch = 0x03;  // Reference to a sheet in the current workbook
        }

        $header = pack("vv", $record, $length);
        $data = pack("CC", $cch, $rgch);
        $this->_prepend($header . $data . $sheetname);
    }

    /**
     * Writes the Excel BIFF PANE record.
     * The panes can either be frozen or thawed (unfrozen).
     * Frozen panes are specified in terms of an integer number of rows and columns.
     * Thawed panes are specified in terms of Excel's units for rows and columns.
     *
     * @access private
     * @param array $panes This is the only parameter received and is composed of the following:
     *                     0 => Vertical split position,
     *                     1 => Horizontal split position
     *                     2 => Top row visible
     *                     3 => Leftmost column visible
     *                     4 => Active pane
     */
    function _storePanes($panes) {
        $y = $panes[0];
        $x = $panes[1];
        $rwTop = $panes[2];
        $colLeft = $panes[3];
        if (count($panes) > 4) { // if Active pane was received
            $pnnAct = $panes[4];
        } else {
            $pnnAct = null;
        }
        $record = 0x0041;       // Record identifier
        $length = 0x000A;       // Number of bytes to follow
        // Code specific to frozen or thawed panes.
        if ($this->_frozen) {
            // Set default values for $rwTop and $colLeft
            if (!isset($rwTop)) {
                $rwTop = $y;
            }
            if (!isset($colLeft)) {
                $colLeft = $x;
            }
        } else {
            // Set default values for $rwTop and $colLeft
            if (!isset($rwTop)) {
                $rwTop = 0;
            }
            if (!isset($colLeft)) {
                $colLeft = 0;
            }

            // Convert Excel's row and column units to the internal units.
            // The default row height is 12.75
            // The default column width is 8.43
            // The following slope and intersection values were interpolated.
            //
            $y = 20 * $y + 255;
            $x = 113.879 * $x + 390;
        }


        // Determine which pane should be active. There is also the undocumented
        // option to override this should it be necessary: may be removed later.
        //
        if (!isset($pnnAct)) {
            if ($x != 0 && $y != 0) {
                $pnnAct = 0; // Bottom right
            }
            if ($x != 0 && $y == 0) {
                $pnnAct = 1; // Top right
            }
            if ($x == 0 && $y != 0) {
                $pnnAct = 2; // Bottom left
            }
            if ($x == 0 && $y == 0) {
                $pnnAct = 3; // Top left
            }
        }

        $this->_active_pane = $pnnAct; // Used in _storeSelection

        $header = pack("vv", $record, $length);
        $data = pack("vvvvv", $x, $y, $rwTop, $colLeft, $pnnAct);
        $this->_append($header . $data);
    }

    /**
     * Store the page setup SETUP BIFF record.
     *
     * @access private
     */
    function _storeSetup() {
        $record = 0x00A1;                  // Record identifier
        $length = 0x0022;                  // Number of bytes to follow

        $iPaperSize = $this->_paper_size;    // Paper size
        $iScale = $this->_print_scale;   // Print scaling factor
        $iPageStart = 0x01;                 // Starting page number
        $iFitWidth = $this->_fit_width;    // Fit to number of pages wide
        $iFitHeight = $this->_fit_height;   // Fit to number of pages high
        $grbit = 0x00;                 // Option flags
        $iRes = 0x0258;               // Print resolution
        $iVRes = 0x0258;               // Vertical print resolution
        $numHdr = $this->_margin_head;  // Header Margin
        $numFtr = $this->_margin_foot;   // Footer Margin
        $iCopies = 0x01;                 // Number of copies

        $fLeftToRight = 0x0;                     // Print over then down
        $fLandscape = $this->_orientation;     // Page orientation
        $fNoPls = 0x0;                     // Setup not read from printer
        $fNoColor = 0x0;                     // Print black and white
        $fDraft = 0x0;                     // Print draft quality
        $fNotes = 0x0;                     // Print notes
        $fNoOrient = 0x0;                     // Orientation not set
        $fUsePage = 0x0;                     // Use custom starting page

        $grbit = $fLeftToRight;
        $grbit |= $fLandscape << 1;
        $grbit |= $fNoPls << 2;
        $grbit |= $fNoColor << 3;
        $grbit |= $fDraft << 4;
        $grbit |= $fNotes << 5;
        $grbit |= $fNoOrient << 6;
        $grbit |= $fUsePage << 7;

        $numHdr = pack("d", $numHdr);
        $numFtr = pack("d", $numFtr);
        if ($this->_byte_order) { // if it's Big Endian
            $numHdr = strrev($numHdr);
            $numFtr = strrev($numFtr);
        }

        $header = pack("vv", $record, $length);
        $data1 = pack("vvvvvvvv", $iPaperSize,
                        $iScale,
                        $iPageStart,
                        $iFitWidth,
                        $iFitHeight,
                        $grbit,
                        $iRes,
                        $iVRes);
        $data2 = $numHdr . $numFtr;
        $data3 = pack("v", $iCopies);
        $this->_prepend($header . $data1 . $data2 . $data3);
    }

    /**
     * Store the header caption BIFF record.
     *
     * @access private
     */
    function _storeHeader() {
        $record = 0x0014;               // Record identifier

        $str = $this->_header;       // header string
        $cch = strlen($str);         // Length of header string
        if ($this->_BIFF_version == 0x0600) {
            $encoding = 0x0;                  // : Unicode support
            $length = 3 + $cch;             // Bytes to follow
        } else {
            $length = 1 + $cch;             // Bytes to follow
        }

        $header = pack("vv", $record, $length);
        if ($this->_BIFF_version == 0x0600) {
            $data = pack("vC", $cch, $encoding);
        } else {
            $data = pack("C", $cch);
        }

        $this->_prepend($header . $data . $str);
    }

    /**
     * Store the footer caption BIFF record.
     *
     * @access private
     */
    function _storeFooter() {
        $record = 0x0015;               // Record identifier

        $str = $this->_footer;       // Footer string
        $cch = strlen($str);         // Length of footer string
        if ($this->_BIFF_version == 0x0600) {
            $encoding = 0x0;                  // : Unicode support
            $length = 3 + $cch;             // Bytes to follow
        } else {
            $length = 1 + $cch;
        }

        $header = pack("vv", $record, $length);
        if ($this->_BIFF_version == 0x0600) {
            $data = pack("vC", $cch, $encoding);
        } else {
            $data = pack("C", $cch);
        }

        $this->_prepend($header . $data . $str);
    }

    /**
     * Store the horizontal centering HCENTER BIFF record.
     *
     * @access private
     */
    function _storeHcenter() {
        $record = 0x0083;              // Record identifier
        $length = 0x0002;              // Bytes to follow

        $fHCenter = $this->_hcenter;     // Horizontal centering

        $header = pack("vv", $record, $length);
        $data = pack("v", $fHCenter);

        $this->_prepend($header . $data);
    }

    /**
     * Store the vertical centering VCENTER BIFF record.
     *
     * @access private
     */
    function _storeVcenter() {
        $record = 0x0084;              // Record identifier
        $length = 0x0002;              // Bytes to follow

        $fVCenter = $this->_vcenter;     // Horizontal centering

        $header = pack("vv", $record, $length);
        $data = pack("v", $fVCenter);
        $this->_prepend($header . $data);
    }

    /**
     * Store the LEFTMARGIN BIFF record.
     *
     * @access private
     */
    function _storeMarginLeft() {
        $record = 0x0026;                   // Record identifier
        $length = 0x0008;                   // Bytes to follow

        $margin = $this->_margin_left;       // Margin in inches

        $header = pack("vv", $record, $length);
        $data = pack("d", $margin);
        if ($this->_byte_order) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->_prepend($header . $data);
    }

    /**
     * Store the RIGHTMARGIN BIFF record.
     *
     * @access private
     */
    function _storeMarginRight() {
        $record = 0x0027;                   // Record identifier
        $length = 0x0008;                   // Bytes to follow

        $margin = $this->_margin_right;      // Margin in inches

        $header = pack("vv", $record, $length);
        $data = pack("d", $margin);
        if ($this->_byte_order) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->_prepend($header . $data);
    }

    /**
     * Store the TOPMARGIN BIFF record.
     *
     * @access private
     */
    function _storeMarginTop() {
        $record = 0x0028;                   // Record identifier
        $length = 0x0008;                   // Bytes to follow

        $margin = $this->_margin_top;        // Margin in inches

        $header = pack("vv", $record, $length);
        $data = pack("d", $margin);
        if ($this->_byte_order) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->_prepend($header . $data);
    }

    /**
     * Store the BOTTOMMARGIN BIFF record.
     *
     * @access private
     */
    function _storeMarginBottom() {
        $record = 0x0029;                   // Record identifier
        $length = 0x0008;                   // Bytes to follow

        $margin = $this->_margin_bottom;     // Margin in inches

        $header = pack("vv", $record, $length);
        $data = pack("d", $margin);
        if ($this->_byte_order) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->_prepend($header . $data);
    }

    /**
     * Merges the area given by its arguments.
     * This is an Excel97/2000 method. It is required to perform more complicated
     * merging than the normal setAlign('merge').
     *
     * @access public
     * @param integer $first_row First row of the area to merge
     * @param integer $first_col First column of the area to merge
     * @param integer $last_row  Last row of the area to merge
     * @param integer $last_col  Last column of the area to merge
     */
    function mergeCells($first_row, $first_col, $last_row, $last_col) {
        $record = 0x00E5;                   // Record identifier
        $length = 0x000A;                   // Bytes to follow
        $cref = 1;                       // Number of refs
        // Swap last row/col for first row/col as necessary
        if ($first_row > $last_row) {
            list($first_row, $last_row) = array($last_row, $first_row);
        }

        if ($first_col > $last_col) {
            list($first_col, $last_col) = array($last_col, $first_col);
        }

        $header = pack("vv", $record, $length);
        $data = pack("vvvvv", $cref, $first_row, $last_row,
                        $first_col, $last_col);

        $this->_append($header . $data);
    }

    /**
     * Write the PRINTHEADERS BIFF record.
     *
     * @access private
     */
    function _storePrintHeaders() {
        $record = 0x002a;                   // Record identifier
        $length = 0x0002;                   // Bytes to follow

        $fPrintRwCol = $this->_print_headers;     // Boolean flag

        $header = pack("vv", $record, $length);
        $data = pack("v", $fPrintRwCol);
        $this->_prepend($header . $data);
    }

    /**
     * Write the PRINTGRIDLINES BIFF record. Must be used in conjunction with the
     * GRIDSET record.
     *
     * @access private
     */
    function _storePrintGridlines() {
        $record = 0x002b;                    // Record identifier
        $length = 0x0002;                    // Bytes to follow

        $fPrintGrid = $this->_print_gridlines;    // Boolean flag

        $header = pack("vv", $record, $length);
        $data = pack("v", $fPrintGrid);
        $this->_prepend($header . $data);
    }

    /**
     * Write the GRIDSET BIFF record. Must be used in conjunction with the
     * PRINTGRIDLINES record.
     *
     * @access private
     */
    function _storeGridset() {
        $record = 0x0082;                        // Record identifier
        $length = 0x0002;                        // Bytes to follow

        $fGridSet = !($this->_print_gridlines);     // Boolean flag

        $header = pack("vv", $record, $length);
        $data = pack("v", $fGridSet);
        $this->_prepend($header . $data);
    }

    /**
     * Write the GUTS BIFF record. This is used to configure the gutter margins
     * where Excel outline symbols are displayed. The visibility of the gutters is
     * controlled by a flag in WSBOOL.
     *
     * @see _storeWsbool()
     * @access private
     */
    function _storeGuts() {
        $record = 0x0080;   // Record identifier
        $length = 0x0008;   // Bytes to follow

        $dxRwGut = 0x0000;   // Size of row gutter
        $dxColGut = 0x0000;   // Size of col gutter

        $row_level = $this->_outline_row_level;
        $col_level = 0;

        // Calculate the maximum column outline level. The equivalent calculation
        // for the row outline level is carried out in setRow().
        $colcount = count($this->_colinfo);
        for ($i = 0; $i < $colcount; $i++) {
            // Skip cols without outline level info.
            if (count($this->_colinfo[$i]) >= 6) {
                $col_level = max($this->_colinfo[$i][5], $col_level);
            }
        }

        // Set the limits for the outline levels (0 <= x <= 7).
        $col_level = max(0, min($col_level, 7));

        // The displayed level is one greater than the max outline levels
        if ($row_level) {
            $row_level++;
        }
        if ($col_level) {
            $col_level++;
        }

        $header = pack("vv", $record, $length);
        $data = pack("vvvv", $dxRwGut, $dxColGut, $row_level, $col_level);

        $this->_prepend($header . $data);
    }

    /**
     * Write the WSBOOL BIFF record, mainly for fit-to-page. Used in conjunction
     * with the SETUP record.
     *
     * @access private
     */
    function _storeWsbool() {
        $record = 0x0081;   // Record identifier
        $length = 0x0002;   // Bytes to follow
        $grbit = 0x0000;

        // The only option that is of interest is the flag for fit to page. So we
        // set all the options in one go.
        //
        /* if ($this->_fit_page) {
          $grbit = 0x05c1;
          } else {
          $grbit = 0x04c1;
          } */
        // Set the option flags
        $grbit |= 0x0001;                           // Auto page breaks visible
        if ($this->_outline_style) {
            $grbit |= 0x0020; // Auto outline styles
        }
        if ($this->_outline_below) {
            $grbit |= 0x0040; // Outline summary below
        }
        if ($this->_outline_right) {
            $grbit |= 0x0080; // Outline summary right
        }
        if ($this->_fit_page) {
            $grbit |= 0x0100; // Page setup fit to page
        }
        if ($this->_outline_on) {
            $grbit |= 0x0400; // Outline symbols displayed
        }

        $header = pack("vv", $record, $length);
        $data = pack("v", $grbit);
        $this->_prepend($header . $data);
    }

    /**
     * Write the HORIZONTALPAGEBREAKS BIFF record.
     *
     * @access private
     */
    function _storeHbreak() {
        // Return if the user hasn't specified pagebreaks
        if (empty($this->_hbreaks)) {
            return;
        }

        // Sort and filter array of page breaks
        $breaks = $this->_hbreaks;
        sort($breaks, SORT_NUMERIC);
        if ($breaks[0] == 0) { // don't use first break if it's 0
            array_shift($breaks);
        }

        $record = 0x001b;               // Record identifier
        $cbrk = count($breaks);       // Number of page breaks
        if ($this->_BIFF_version == 0x0600) {
            $length = 2 + 6 * $cbrk;      // Bytes to follow
        } else {
            $length = 2 + 2 * $cbrk;      // Bytes to follow
        }

        $header = pack("vv", $record, $length);
        $data = pack("v", $cbrk);

        // Append each page break
        foreach ($breaks as $break) {
            if ($this->_BIFF_version == 0x0600) {
                $data .= pack("vvv", $break, 0x0000, 0x00ff);
            } else {
                $data .= pack("v", $break);
            }
        }

        $this->_prepend($header . $data);
    }

    /**
     * Write the VERTICALPAGEBREAKS BIFF record.
     *
     * @access private
     */
    function _storeVbreak() {
        // Return if the user hasn't specified pagebreaks
        if (empty($this->_vbreaks)) {
            return;
        }

        // 1000 vertical pagebreaks appears to be an internal Excel 5 limit.
        // It is slightly higher in Excel 97/200, approx. 1026
        $breaks = array_slice($this->_vbreaks, 0, 1000);

        // Sort and filter array of page breaks
        sort($breaks, SORT_NUMERIC);
        if ($breaks[0] == 0) { // don't use first break if it's 0
            array_shift($breaks);
        }

        $record = 0x001a;               // Record identifier
        $cbrk = count($breaks);       // Number of page breaks
        if ($this->_BIFF_version == 0x0600) {
            $length = 2 + 6 * $cbrk;      // Bytes to follow
        } else {
            $length = 2 + 2 * $cbrk;      // Bytes to follow
        }

        $header = pack("vv", $record, $length);
        $data = pack("v", $cbrk);

        // Append each page break
        foreach ($breaks as $break) {
            if ($this->_BIFF_version == 0x0600) {
                $data .= pack("vvv", $break, 0x0000, 0xffff);
            } else {
                $data .= pack("v", $break);
            }
        }

        $this->_prepend($header . $data);
    }

    /**
     * Set the Biff PROTECT record to indicate that the worksheet is protected.
     *
     * @access private
     */
    function _storeProtect() {
        // Exit unless sheet protection has been specified
        if ($this->_protect == 0) {
            return;
        }

        $record = 0x0012;             // Record identifier
        $length = 0x0002;             // Bytes to follow

        $fLock = $this->_protect;    // Worksheet is protected

        $header = pack("vv", $record, $length);
        $data = pack("v", $fLock);

        $this->_prepend($header . $data);
    }

    /**
     * Write the worksheet PASSWORD record.
     *
     * @access private
     */
    function _storePassword() {
        // Exit unless sheet protection and password have been specified
        if (($this->_protect == 0) || (!isset($this->_password))) {
            return;
        }

        $record = 0x0013;               // Record identifier
        $length = 0x0002;               // Bytes to follow

        $wPassword = $this->_password;     // Encoded password

        $header = pack("vv", $record, $length);
        $data = pack("v", $wPassword);

        $this->_prepend($header . $data);
    }

    /**
     * Insert a 24bit bitmap image in a worksheet.
     *
     * @access public
     * @param integer $row     The row we are going to insert the bitmap into
     * @param integer $col     The column we are going to insert the bitmap into
     * @param string  $bitmap  The bitmap filename
     * @param integer $x       The horizontal position (offset) of the image inside the cell.
     * @param integer $y       The vertical position (offset) of the image inside the cell.
     * @param integer $scale_x The horizontal scale
     * @param integer $scale_y The vertical scale
     */
    function insertBitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1) {
        $bitmap_array = $this->_processBitmap($bitmap);
        if ($this->isError($bitmap_array)) {
            $this->writeString($row, $col, $bitmap_array->getMessage());
            return;
        }
        list($width, $height, $size, $data) = $bitmap_array; //$this->_processBitmap($bitmap);
        // Scale the frame of the image.
        $width *= $scale_x;
        $height *= $scale_y;

        // Calculate the vertices of the image and write the OBJ record
        $this->_positionImage($col, $row, $x, $y, $width, $height);

        // Write the IMDATA record to store the bitmap data
        $record = 0x007f;
        $length = 8 + $size;
        $cf = 0x09;
        $env = 0x01;
        $lcb = $size;

        $header = pack("vvvvV", $record, $length, $cf, $env, $lcb);
        $this->_append($header . $data);
    }

    /**
     * Calculate the vertices that define the position of the image as required by
     * the OBJ record.
     *
     *         +------------+------------+
     *         |     A      |      B     |
     *   +-----+------------+------------+
     *   |     |(x1,y1)     |            |
     *   |  1  |(A1)._______|______      |
     *   |     |    |              |     |
     *   |     |    |              |     |
     *   +-----+----|    BITMAP    |-----+
     *   |     |    |              |     |
     *   |  2  |    |______________.     |
     *   |     |            |        (B2)|
     *   |     |            |     (x2,y2)|
     *   +---- +------------+------------+
     *
     * Example of a bitmap that covers some of the area from cell A1 to cell B2.
     *
     * Based on the width and height of the bitmap we need to calculate 8 vars:
     *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
     * The width and height of the cells are also variable and have to be taken into
     * account.
     * The values of $col_start and $row_start are passed in from the calling
     * function. The values of $col_end and $row_end are calculated by subtracting
     * the width and height of the bitmap from the width and height of the
     * underlying cells.
     * The vertices are expressed as a percentage of the underlying cell width as
     * follows (rhs values are in pixels):
     *
     *       x1 = X / W *1024
     *       y1 = Y / H *256
     *       x2 = (X-1) / W *1024
     *       y2 = (Y-1) / H *256
     *
     *       Where:  X is distance from the left side of the underlying cell
     *               Y is distance from the top of the underlying cell
     *               W is the width of the cell
     *               H is the height of the cell
     *
     * @access private
     * @note  the SDK incorrectly states that the height should be expressed as a
     *        percentage of 1024.
     * @param integer $col_start Col containing upper left corner of object
     * @param integer $row_start Row containing top left corner of object
     * @param integer $x1        Distance to left side of object
     * @param integer $y1        Distance to top of object
     * @param integer $width     Width of image frame
     * @param integer $height    Height of image frame
     */
    function _positionImage($col_start, $row_start, $x1, $y1, $width, $height) {
        // Initialise end cell to the same as the start cell
        $col_end = $col_start;  // Col containing lower right corner of object
        $row_end = $row_start;  // Row containing bottom right corner of object
        // Zero the specified offset if greater than the cell dimensions
        if ($x1 >= $this->_sizeCol($col_start)) {
            $x1 = 0;
        }
        if ($y1 >= $this->_sizeRow($row_start)) {
            $y1 = 0;
        }

        $width = $width + $x1 - 1;
        $height = $height + $y1 - 1;

        // Subtract the underlying cell widths to find the end cell of the image
        while ($width >= $this->_sizeCol($col_end)) {
            $width -= $this->_sizeCol($col_end);
            $col_end++;
        }

        // Subtract the underlying cell heights to find the end cell of the image
        while ($height >= $this->_sizeRow($row_end)) {
            $height -= $this->_sizeRow($row_end);
            $row_end++;
        }

        // Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
        // with zero eight or width.
        //
        if ($this->_sizeCol($col_start) == 0) {
            return;
        }
        if ($this->_sizeCol($col_end) == 0) {
            return;
        }
        if ($this->_sizeRow($row_start) == 0) {
            return;
        }
        if ($this->_sizeRow($row_end) == 0) {
            return;
        }

        // Convert the pixel values to the percentage value expected by Excel
        $x1 = $x1 / $this->_sizeCol($col_start) * 1024;
        $y1 = $y1 / $this->_sizeRow($row_start) * 256;
        $x2 = $width / $this->_sizeCol($col_end) * 1024; // Distance to right side of object
        $y2 = $height / $this->_sizeRow($row_end) * 256; // Distance to bottom of object

        $this->_storeObjPicture($col_start, $x1,
                $row_start, $y1,
                $col_end, $x2,
                $row_end, $y2);
    }

    /**
     * Convert the width of a cell from user's units to pixels. By interpolation
     * the relationship is: y = 7x +5. If the width hasn't been set by the user we
     * use the default value. If the col is hidden we use a value of zero.
     *
     * @access private
     * @param integer $col The column
     * @return integer The width in pixels
     */
    function _sizeCol($col) {
        // Look up the cell value to see if it has been changed
        if (isset($this->col_sizes[$col])) {
            if ($this->col_sizes[$col] == 0) {
                return(0);
            } else {
                return(floor(7 * $this->col_sizes[$col] + 5));
            }
        } else {
            return(64);
        }
    }

    /**
     * Convert the height of a cell from user's units to pixels. By interpolation
     * the relationship is: y = 4/3x. If the height hasn't been set by the user we
     * use the default value. If the row is hidden we use a value of zero. (Not
     * possible to hide row yet).
     *
     * @access private
     * @param integer $row The row
     * @return integer The width in pixels
     */
    function _sizeRow($row) {
        // Look up the cell value to see if it has been changed
        if (isset($this->_row_sizes[$row])) {
            if ($this->_row_sizes[$row] == 0) {
                return(0);
            } else {
                return(floor(4 / 3 * $this->_row_sizes[$row]));
            }
        } else {
            return(17);
        }
    }

    /**
     * Store the OBJ record that precedes an IMDATA record. This could be generalise
     * to support other Excel objects.
     *
     * @access private
     * @param integer $colL Column containing upper left corner of object
     * @param integer $dxL  Distance from left side of cell
     * @param integer $rwT  Row containing top left corner of object
     * @param integer $dyT  Distance from top of cell
     * @param integer $colR Column containing lower right corner of object
     * @param integer $dxR  Distance from right of cell
     * @param integer $rwB  Row containing bottom right corner of object
     * @param integer $dyB  Distance from bottom of cell
     */
    function _storeObjPicture($colL, $dxL, $rwT, $dyT, $colR, $dxR, $rwB, $dyB) {
        $record = 0x005d;   // Record identifier
        $length = 0x003c;   // Bytes to follow

        $cObj = 0x0001;   // Count of objects in file (set to 1)
        $OT = 0x0008;   // Object type. 8 = Picture
        $id = 0x0001;   // Object ID
        $grbit = 0x0614;   // Option flags

        $cbMacro = 0x0000;   // Length of FMLA structure
        $Reserved1 = 0x0000;   // Reserved
        $Reserved2 = 0x0000;   // Reserved

        $icvBack = 0x09;     // Background colour
        $icvFore = 0x09;     // Foreground colour
        $fls = 0x00;     // Fill pattern
        $fAuto = 0x00;     // Automatic fill
        $icv = 0x08;     // Line colour
        $lns = 0xff;     // Line style
        $lnw = 0x01;     // Line weight
        $fAutoB = 0x00;     // Automatic border
        $frs = 0x0000;   // Frame style
        $cf = 0x0009;   // Image format, 9 = bitmap
        $Reserved3 = 0x0000;   // Reserved
        $cbPictFmla = 0x0000;   // Length of FMLA structure
        $Reserved4 = 0x0000;   // Reserved
        $grbit2 = 0x0001;   // Option flags
        $Reserved5 = 0x0000;   // Reserved


        $header = pack("vv", $record, $length);
        $data = pack("V", $cObj);
        $data .= pack("v", $OT);
        $data .= pack("v", $id);
        $data .= pack("v", $grbit);
        $data .= pack("v", $colL);
        $data .= pack("v", $dxL);
        $data .= pack("v", $rwT);
        $data .= pack("v", $dyT);
        $data .= pack("v", $colR);
        $data .= pack("v", $dxR);
        $data .= pack("v", $rwB);
        $data .= pack("v", $dyB);
        $data .= pack("v", $cbMacro);
        $data .= pack("V", $Reserved1);
        $data .= pack("v", $Reserved2);
        $data .= pack("C", $icvBack);
        $data .= pack("C", $icvFore);
        $data .= pack("C", $fls);
        $data .= pack("C", $fAuto);
        $data .= pack("C", $icv);
        $data .= pack("C", $lns);
        $data .= pack("C", $lnw);
        $data .= pack("C", $fAutoB);
        $data .= pack("v", $frs);
        $data .= pack("V", $cf);
        $data .= pack("v", $Reserved3);
        $data .= pack("v", $cbPictFmla);
        $data .= pack("v", $Reserved4);
        $data .= pack("v", $grbit2);
        $data .= pack("V", $Reserved5);

        $this->_append($header . $data);
    }

    /**
     * Convert a 24 bit bitmap into the modified internal format used by Windows.
     * This is described in BITMAPCOREHEADER and BITMAPCOREINFO structures in the
     * MSDN library.
     *
     * @access private
     * @param string $bitmap The bitmap to process
     * @return array Array with data and properties of the bitmap
     */
    function _processBitmap($bitmap) {
        // Open file.
        $bmp_fd = @fopen($bitmap, "rb");
        if (!$bmp_fd) {
            $this->raiseError("Couldn't import $bitmap");
        }

        // Slurp the file into a string.
        $data = fread($bmp_fd, filesize($bitmap));

        // Check that the file is big enough to be a bitmap.
        if (strlen($data) <= 0x36) {
            $this->raiseError("$bitmap doesn't contain enough data.\n");
        }

        // The first 2 bytes are used to identify the bitmap.
        $identity = unpack("A2ident", $data);
        if ($identity['ident'] != "BM") {
            $this->raiseError("$bitmap doesn't appear to be a valid bitmap image.\n");
        }

        // Remove bitmap data: ID.
        $data = substr($data, 2);

        // Read and remove the bitmap size. This is more reliable than reading
        // the data size at offset 0x22.
        //
        $size_array = unpack("Vsa", substr($data, 0, 4));
        $size = $size_array['sa'];
        $data = substr($data, 4);
        $size -= 0x36; // Subtract size of bitmap header.
        $size += 0x0C; // Add size of BIFF header.
        // Remove bitmap data: reserved, offset, header length.
        $data = substr($data, 12);

        // Read and remove the bitmap width and height. Verify the sizes.
        $width_and_height = unpack("V2", substr($data, 0, 8));
        $width = $width_and_height[1];
        $height = $width_and_height[2];
        $data = substr($data, 8);
        if ($width > 0xFFFF) {
            $this->raiseError("$bitmap: largest image width supported is 65k.\n");
        }
        if ($height > 0xFFFF) {
            $this->raiseError("$bitmap: largest image height supported is 65k.\n");
        }

        // Read and remove the bitmap planes and bpp data. Verify them.
        $planes_and_bitcount = unpack("v2", substr($data, 0, 4));
        $data = substr($data, 4);
        if ($planes_and_bitcount[2] != 24) { // Bitcount
            $this->raiseError("$bitmap isn't a 24bit true color bitmap.\n");
        }
        if ($planes_and_bitcount[1] != 1) {
            $this->raiseError("$bitmap: only 1 plane supported in bitmap image.\n");
        }

        // Read and remove the bitmap compression. Verify compression.
        $compression = unpack("Vcomp", substr($data, 0, 4));
        $data = substr($data, 4);

        //$compression = 0;
        if ($compression['comp'] != 0) {
            $this->raiseError("$bitmap: compression not supported in bitmap image.\n");
        }

        // Remove bitmap data: data size, hres, vres, colours, imp. colours.
        $data = substr($data, 20);

        // Add the BITMAPCOREHEADER data
        $header = pack("Vvvvv", 0x000c, $width, $height, 0x01, 0x18);
        $data = $header . $data;

        return (array($width, $height, $size, $data));
    }

    /**
     * Store the window zoom factor. This should be a reduced fraction but for
     * simplicity we will store all fractions with a numerator of 100.
     *
     * @access private
     */
    function _storeZoom() {
        // If scale is 100 we don't need to write a record
        if ($this->_zoom == 100) {
            return;
        }

        $record = 0x00A0;               // Record identifier
        $length = 0x0004;               // Bytes to follow

        $header = pack("vv", $record, $length);
        $data = pack("vv", $this->_zoom, 100);
        $this->_append($header . $data);
    }

    /**
     * : add comments
     */
//    function setValidation($row1, $col1, $row2, $col2, &$validator, $rango = "") {
    public function setDataValidation($pCellCoordinate = 'A1', PHPExcel_Cell_DataValidation $pDataValidation = null) {
//        $this->_dv[] = $validator->_getData($rango) .
//                pack("vvvvv", 1, $row1, $row2, $col1, $col2);
        if ($pDataValidation === null) {
            unset($this->_dv[$pCellCoordinate]);
        } else {
            $this->_dv[$pCellCoordinate] = $pDataValidation;
        }
        return $this;
    }

    /**
     * Store the DVAL and DV records.
     *
     * @access private
     */
    function _storeDataValidity() {
//        $record      = 0x01b2;      // Record identifier
//        $length      = 0x0012;      // Bytes to follow
//
//        $grbit       = 0x0002;      // Prompt box at cell, no cached validity data at DV records
//        $horPos      = 0x00000000;  // Horizontal position of prompt box, if fixed position
//        $verPos      = 0x00000000;  // Vertical position of prompt box, if fixed position
//        $objId       = 0xffffffff;  // Object identifier of drop down arrow object, or -1 if not visible
//
//        $header      = pack('vv', $record, $length);
//        $data        = pack('vVVVV', $grbit, $horPos, $verPos, $objId,
//                                     count($this->_dv));
//        $this->_append($header.$data);
//
//        $record = 0x01be;              // Record identifier
//        foreach ($this->_dv as $dv) {
//            $length = strlen($dv);      // Bytes to follow
//            $header = pack("vv", $record, $length);
//            $this->_append($header . $dv);
//        }
        // Datavalidation collection
//        $dataValidationCollection = $this->_phpSheet->getDataValidationCollection();
        // Write data validations?
        if (count($this->_dv) > 0) {

            // DATAVALIDATIONS record
            $record = 0x01B2;   // Record identifier
            $length = 0x0012;   // Bytes to follow

            $grbit = 0x0000;    // Prompt box at cell, no cached validity data at DV records
            $horPos = 0x00000000;  // Horizontal position of prompt box, if fixed position
            $verPos = 0x00000000;  // Vertical position of prompt box, if fixed position
            $objId = 0xFFFFFFFF;  // Object identifier of drop down arrow object, or -1 if not visible

            $header = pack('vv', $record, $length);
            $data = pack('vVVVV', $grbit, $horPos, $verPos, $objId,
                            count($this->_dv));
            $this->_append($header . $data);

            // DATAVALIDATION records
            $record = 0x01BE;     // Record identifier

            foreach ($this->_dv as $cellCoordinate => $dataValidation) {
                // initialize record data
                $data = '';

                // options
                $options = 0x00000000;

                // data type
                $type = $dataValidation->getType();
                switch ($type) {
                    case PHPExcel_Cell_DataValidation::TYPE_NONE: $type = 0x00;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_WHOLE: $type = 0x01;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_DECIMAL: $type = 0x02;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_LIST: $type = 0x03;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_DATE: $type = 0x04;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_TIME: $type = 0x05;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_TEXTLENGTH: $type = 0x06;
                        break;
                    case PHPExcel_Cell_DataValidation::TYPE_CUSTOM: $type = 0x07;
                        break;
                }
                $options |= $type << 0;

                // error style
                $errorStyle = $dataValidation->getType();
                switch ($errorStyle) {
                    case PHPExcel_Cell_DataValidation::STYLE_STOP: $errorStyle = 0x00;
                        break;
                    case PHPExcel_Cell_DataValidation::STYLE_WARNING: $errorStyle = 0x01;
                        break;
                    case PHPExcel_Cell_DataValidation::STYLE_INFORMATION: $errorStyle = 0x02;
                        break;
                }
                $options |= $errorStyle << 4;

                // explicit formula?
                if ($type == 0x03 && preg_match('/^\".*\"$/', $dataValidation->getFormula1())) {
                    $options |= 0x01 << 7;
                }

                // empty cells allowed
                $options |= $dataValidation->getAllowBlank() << 8;

                // show drop down
                $options |= ( !$dataValidation->getShowDropDown()) << 9;

                // show input message
                $options |= $dataValidation->getShowInputMessage() << 18;

                // show error message
                $options |= $dataValidation->getShowErrorMessage() << 19;

                // condition operator
                $operator = $dataValidation->getOperator();
                switch ($operator) {
                    case PHPExcel_Cell_DataValidation::OPERATOR_BETWEEN: $operator = 0x00;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_NOTBETWEEN: $operator = 0x01;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_EQUAL: $operator = 0x02;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_NOTEQUAL: $operator = 0x03;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHAN: $operator = 0x04;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_LESSTHAN: $operator = 0x05;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHANOREQUAL: $operator = 0x06;
                        break;
                    case PHPExcel_Cell_DataValidation::OPERATOR_LESSTHANOREQUAL: $operator = 0x07;
                        break;
                }
                $options |= $operator << 20;

                $data = pack('V', $options);

                // prompt title
                $promptTitle = $dataValidation->getPromptTitle() !== '' ?
                        $dataValidation->getPromptTitle() : chr(0);
                $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($promptTitle);

                // error title
                $errorTitle = $dataValidation->getErrorTitle() !== '' ?
                        $dataValidation->getErrorTitle() : chr(0);
                $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($errorTitle);

                // prompt text
                $prompt = $dataValidation->getPrompt() !== '' ?
                        $dataValidation->getPrompt() : chr(0);
                $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($prompt);

                // error text
                $error = $dataValidation->getError() !== '' ?
                        $dataValidation->getError() : chr(0);
                $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($error);

                // formula 1
                try {
                    $formula1 = $dataValidation->getFormula1();
                    if ($type == 0x03) { // list type
                        $formula1 = str_replace(',', chr(0), $formula1);
                    }
                    $this->_parser->parse($formula1);
                    $formula1 = $this->_parser->toReversePolish();
                    $sz1 = strlen($formula1);
                } catch (Exception $e) {
                    $sz1 = 0;
                    $formula1 = '';
                }
                $data .= pack('vv', $sz1, 0x0000);
                $data .= $formula1;

                // formula 2
                try {
                    $formula2 = $dataValidation->getFormula2();
                    if ($formula2 === '') {
                        throw new Exception('No formula2');
                    }
                    $this->_parser->parse($formula2);
                    $formula2 = $this->_parser->toReversePolish();
                    $sz2 = strlen($formula2);
                } catch (Exception $e) {
                    $sz2 = 0;
                    $formula2 = '';
                }
                $data .= pack('vv', $sz2, 0x0000);
                $data .= $formula2;

                // cell range address list
                $data .= pack('v', 0x0001);
                $data .= $this->_writeBIFF8CellRangeAddressFixed($cellCoordinate);

                $length = strlen($data);
                $header = pack("vv", $record, $length);

                $this->_append($header . $data);
            }
        }
    }

    public function _writeBIFF8CellRangeAddressFixed($range = 'A1') {
        $explodes = explode(':', $range);

        // extract first cell, e.g. 'A1'
        $firstCell = $explodes[0];

        // extract last cell, e.g. 'B6'
        if (count($explodes) == 1) {
            $lastCell = $firstCell;
        } else {
            $lastCell = $explodes[1];
        }

        $firstCellCoordinates = PHPExcel_Cell::coordinateFromString($firstCell); // e.g. array(0, 1)
        $lastCellCoordinates = PHPExcel_Cell::coordinateFromString($lastCell);  // e.g. array(1, 6)

        return(pack('vvvv',
                $firstCellCoordinates[1] - 1,
                $lastCellCoordinates[1] - 1,
                PHPExcel_Cell::columnIndexFromString($firstCellCoordinates[0]) - 1,
                PHPExcel_Cell::columnIndexFromString($lastCellCoordinates[0]) - 1
        ));
    }

}



class PHPExcel_Cell_DataValidation
{
	/* Data validation types */
	const TYPE_NONE			= 'none';
	const TYPE_CUSTOM		= 'custom';
	const TYPE_DATE			= 'date';
	const TYPE_DECIMAL		= 'decimal';
	const TYPE_LIST			= 'list';
	const TYPE_TEXTLENGTH	= 'textLength';
	const TYPE_TIME			= 'time';
	const TYPE_WHOLE		= 'whole';

	/* Data validation error styles */
	const STYLE_STOP		= 'stop';
	const STYLE_WARNING		= 'warning';
	const STYLE_INFORMATION	= 'information';

	/* Data validation operators */
	const OPERATOR_BETWEEN				= 'between';
	const OPERATOR_EQUAL				= 'equal';
	const OPERATOR_GREATERTHAN			= 'greaterThan';
	const OPERATOR_GREATERTHANOREQUAL	= 'greaterThanOrEqual';
	const OPERATOR_LESSTHAN				= 'lessThan';
	const OPERATOR_LESSTHANOREQUAL		= 'lessThanOrEqual';
	const OPERATOR_NOTBETWEEN			= 'notBetween';
	const OPERATOR_NOTEQUAL				= 'notEqual';

    /**
     * Formula 1
     *
     * @var string
     */
    private $_formula1;

    /**
     * Formula 2
     *
     * @var string
     */
    private $_formula2;

    /**
     * Type
     *
     * @var string
     */
    private $_type = PHPExcel_Cell_DataValidation::TYPE_NONE;

    /**
     * Error style
     *
     * @var string
     */
    private $_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;

    /**
     * Operator
     *
     * @var string
     */
    private $_operator;

    /**
     * Allow Blank
     *
     * @var boolean
     */
    private $_allowBlank;

    /**
     * Show DropDown
     *
     * @var boolean
     */
    private $_showDropDown;

    /**
     * Show InputMessage
     *
     * @var boolean
     */
    private $_showInputMessage;

    /**
     * Show ErrorMessage
     *
     * @var boolean
     */
    private $_showErrorMessage;

    /**
     * Error title
     *
     * @var string
     */
    private $_errorTitle;

    /**
     * Error
     *
     * @var string
     */
    private $_error;

    /**
     * Prompt title
     *
     * @var string
     */
    private $_promptTitle;

    /**
     * Prompt
     *
     * @var string
     */
    private $_prompt;

    /**
     * Create a new PHPExcel_Cell_DataValidation
     *
     * @throws	Exception
     */
    public function __construct()
    {
    	// Initialise member variables
		$this->_formula1 			= '';
		$this->_formula2 			= '';
		$this->_type 				= PHPExcel_Cell_DataValidation::TYPE_NONE;
		$this->_errorStyle 			= PHPExcel_Cell_DataValidation::STYLE_STOP;
		$this->_operator 			= '';
		$this->_allowBlank 			= false;
		$this->_showDropDown 		= false;
		$this->_showInputMessage 	= false;
		$this->_showErrorMessage 	= false;
		$this->_errorTitle 			= '';
		$this->_error 				= '';
		$this->_promptTitle 		= '';
		$this->_prompt 				= '';
    }

	/**
	 * Get Formula 1
	 *
	 * @return string
	 */
	public function getFormula1() {
		return $this->_formula1;
	}

	/**
	 * Set Formula 1
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setFormula1($value = '') {
		$this->_formula1 = $value;
		return $this;
	}

	/**
	 * Get Formula 2
	 *
	 * @return string
	 */
	public function getFormula2() {
		return $this->_formula2;
	}

	/**
	 * Set Formula 2
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setFormula2($value = '') {
		$this->_formula2 = $value;
		return $this;
	}

	/**
	 * Get Type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Set Type
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setType($value = PHPExcel_Cell_DataValidation::TYPE_NONE) {
		$this->_type = $value;
		return $this;
	}

	/**
	 * Get Error style
	 *
	 * @return string
	 */
	public function getErrorStyle() {
		return $this->_errorStyle;
	}

	/**
	 * Set Error style
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setErrorStyle($value = PHPExcel_Cell_DataValidation::STYLE_STOP) {
		$this->_errorStyle = $value;
		return $this;
	}

	/**
	 * Get Operator
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->_operator;
	}

	/**
	 * Set Operator
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setOperator($value = '') {
		$this->_operator = $value;
		return $this;
	}

	/**
	 * Get Allow Blank
	 *
	 * @return boolean
	 */
	public function getAllowBlank() {
		return $this->_allowBlank;
	}

	/**
	 * Set Allow Blank
	 *
	 * @param	boolean	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setAllowBlank($value = false) {
		$this->_allowBlank = $value;
		return $this;
	}

	/**
	 * Get Show DropDown
	 *
	 * @return boolean
	 */
	public function getShowDropDown() {
		return $this->_showDropDown;
	}

	/**
	 * Set Show DropDown
	 *
	 * @param	boolean	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setShowDropDown($value = false) {
		$this->_showDropDown = $value;
		return $this;
	}

	/**
	 * Get Show InputMessage
	 *
	 * @return boolean
	 */
	public function getShowInputMessage() {
		return $this->_showInputMessage;
	}

	/**
	 * Set Show InputMessage
	 *
	 * @param	boolean	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setShowInputMessage($value = false) {
		$this->_showInputMessage = $value;
		return $this;
	}

	/**
	 * Get Show ErrorMessage
	 *
	 * @return boolean
	 */
	public function getShowErrorMessage() {
		return $this->_showErrorMessage;
	}

	/**
	 * Set Show ErrorMessage
	 *
	 * @param	boolean	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setShowErrorMessage($value = false) {
		$this->_showErrorMessage = $value;
		return $this;
	}

	/**
	 * Get Error title
	 *
	 * @return string
	 */
	public function getErrorTitle() {
		return $this->_errorTitle;
	}

	/**
	 * Set Error title
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setErrorTitle($value = '') {
		$this->_errorTitle = $value;
		return $this;
	}

	/**
	 * Get Error
	 *
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * Set Error
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setError($value = '') {
		$this->_error = $value;
		return $this;
	}

	/**
	 * Get Prompt title
	 *
	 * @return string
	 */
	public function getPromptTitle() {
		return $this->_promptTitle;
	}

	/**
	 * Set Prompt title
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setPromptTitle($value = '') {
		$this->_promptTitle = $value;
		return $this;
	}

	/**
	 * Get Prompt
	 *
	 * @return string
	 */
	public function getPrompt() {
		return $this->_prompt;
	}

	/**
	 * Set Prompt
	 *
	 * @param	string	$value
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function setPrompt($value = '') {
		$this->_prompt = $value;
		return $this;
	}

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_formula1
    		. $this->_formula2
    		. $this->_type = PHPExcel_Cell_DataValidation::TYPE_NONE
    		. $this->_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP
    		. $this->_operator
    		. ($this->_allowBlank ? 't' : 'f')
    		. ($this->_showDropDown ? 't' : 'f')
    		. ($this->_showInputMessage ? 't' : 'f')
    		. ($this->_showErrorMessage ? 't' : 'f')
    		. $this->_errorTitle
    		. $this->_error
    		. $this->_promptTitle
    		. $this->_prompt
    		. __CLASS__
    	);
    }

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}




class PHPExcel_Shared_String
{
	/**	Constants				*/
	/**	Regular Expressions		*/
	//	Fraction
	const STRING_REGEXP_FRACTION	= '(-?)(\d+)\s+(\d+\/\d+)';


	/**
	 * Control characters array
	 *
	 * @var string[]
	 */
	private static $_controlCharacters = array();

	/**
	 * SYLK Characters array
	 *
	 * $var array
	 */
	private static $_SYLKCharacters = array();

	/**
	 * Decimal separator
	 *
	 * @var string
	 */
	private static $_decimalSeparator;

	/**
	 * Thousands separator
	 *
	 * @var string
	 */
	private static $_thousandsSeparator;

	/**
	 * Is mbstring extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isMbstringEnabled;

	/**
	 * Is iconv extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isIconvEnabled;

	/**
	 * Build control characters array
	 */
	private static function _buildControlCharacters() {
		for ($i = 0; $i <= 31; ++$i) {
			if ($i != 9 && $i != 10 && $i != 13) {
				$find = '_x' . sprintf('%04s' , strtoupper(dechex($i))) . '_';
				$replace = chr($i);
				self::$_controlCharacters[$find] = $replace;
			}
		}
	}

	/**
	 * Build SYLK characters array
	 */
	private static function _buildSYLKCharacters()
	{
		self::$_SYLKCharacters = array(
			"\x1B 0"  => chr(0),
			"\x1B 1"  => chr(1),
			"\x1B 2"  => chr(2),
			"\x1B 3"  => chr(3),
			"\x1B 4"  => chr(4),
			"\x1B 5"  => chr(5),
			"\x1B 6"  => chr(6),
			"\x1B 7"  => chr(7),
			"\x1B 8"  => chr(8),
			"\x1B 9"  => chr(9),
			"\x1B :"  => chr(10),
			"\x1B ;"  => chr(11),
			"\x1B <"  => chr(12),
			"\x1B :"  => chr(13),
			"\x1B >"  => chr(14),
			"\x1B ?"  => chr(15),
			"\x1B!0"  => chr(16),
			"\x1B!1"  => chr(17),
			"\x1B!2"  => chr(18),
			"\x1B!3"  => chr(19),
			"\x1B!4"  => chr(20),
			"\x1B!5"  => chr(21),
			"\x1B!6"  => chr(22),
			"\x1B!7"  => chr(23),
			"\x1B!8"  => chr(24),
			"\x1B!9"  => chr(25),
			"\x1B!:"  => chr(26),
			"\x1B!;"  => chr(27),
			"\x1B!<"  => chr(28),
			"\x1B!="  => chr(29),
			"\x1B!>"  => chr(30),
			"\x1B!?"  => chr(31),
			"\x1B'?"  => chr(127),
			"\x1B(0"  => '€', // 128 in CP1252
			"\x1B(2"  => '‚', // 130 in CP1252
			"\x1B(3"  => 'ƒ', // 131 in CP1252
			"\x1B(4"  => '„', // 132 in CP1252
			"\x1B(5"  => '…', // 133 in CP1252
			"\x1B(6"  => '†', // 134 in CP1252
			"\x1B(7"  => '‡', // 135 in CP1252
			"\x1B(8"  => 'ˆ', // 136 in CP1252
			"\x1B(9"  => '‰', // 137 in CP1252
			"\x1B(:"  => 'Š', // 138 in CP1252
			"\x1B(;"  => '‹', // 139 in CP1252
			"\x1BNj"  => 'Œ', // 140 in CP1252
			"\x1B(>"  => 'Ž', // 142 in CP1252
			"\x1B)1"  => '‘', // 145 in CP1252
			"\x1B)2"  => '’', // 146 in CP1252
			"\x1B)3"  => '“', // 147 in CP1252
			"\x1B)4"  => '”', // 148 in CP1252
			"\x1B)5"  => '•', // 149 in CP1252
			"\x1B)6"  => '–', // 150 in CP1252
			"\x1B)7"  => '—', // 151 in CP1252
			"\x1B)8"  => '˜', // 152 in CP1252
			"\x1B)9"  => '™', // 153 in CP1252
			"\x1B):"  => 'š', // 154 in CP1252
			"\x1B);"  => '›', // 155 in CP1252
			"\x1BNz"  => 'œ', // 156 in CP1252
			"\x1B)>"  => 'ž', // 158 in CP1252
			"\x1B)?"  => 'Ÿ', // 159 in CP1252
			"\x1B*0"  => ' ', // 160 in CP1252
			"\x1BN!"  => '¡', // 161 in CP1252
			"\x1BN\"" => '¢', // 162 in CP1252
			"\x1BN#"  => '£', // 163 in CP1252
			"\x1BN("  => '¤', // 164 in CP1252
			"\x1BN%"  => '¥', // 165 in CP1252
			"\x1B*6"  => '¦', // 166 in CP1252
			"\x1BN'"  => '§', // 167 in CP1252
			"\x1BNH " => '¨', // 168 in CP1252
			"\x1BNS"  => '©', // 169 in CP1252
			"\x1BNc"  => 'ª', // 170 in CP1252
			"\x1BN+"  => '«', // 171 in CP1252
			"\x1B*<"  => '¬', // 172 in CP1252
			"\x1B*="  => '­', // 173 in CP1252
			"\x1BNR"  => '®', // 174 in CP1252
			"\x1B*?"  => '¯', // 175 in CP1252
			"\x1BN0"  => '°', // 176 in CP1252
			"\x1BN1"  => '±', // 177 in CP1252
			"\x1BN2"  => '²', // 178 in CP1252
			"\x1BN3"  => '³', // 179 in CP1252
			"\x1BNB " => '´', // 180 in CP1252
			"\x1BN5"  => 'µ', // 181 in CP1252
			"\x1BN6"  => '¶', // 182 in CP1252
			"\x1BN7"  => '·', // 183 in CP1252
			"\x1B+8"  => '¸', // 184 in CP1252
			"\x1BNQ"  => '¹', // 185 in CP1252
			"\x1BNk"  => 'º', // 186 in CP1252
			"\x1BN;"  => '»', // 187 in CP1252
			"\x1BN<"  => '¼', // 188 in CP1252
			"\x1BN="  => '½', // 189 in CP1252
			"\x1BN>"  => '¾', // 190 in CP1252
			"\x1BN?"  => '¿', // 191 in CP1252
			"\x1BNAA" => 'À', // 192 in CP1252
			"\x1BNBA" => 'Á', // 193 in CP1252
			"\x1BNCA" => 'Â', // 194 in CP1252
			"\x1BNDA" => 'Ã', // 195 in CP1252
			"\x1BNHA" => 'Ä', // 196 in CP1252
			"\x1BNJA" => 'Å', // 197 in CP1252
			"\x1BNa"  => 'Æ', // 198 in CP1252
			"\x1BNKC" => 'Ç', // 199 in CP1252
			"\x1BNAE" => 'È', // 200 in CP1252
			"\x1BNBE" => 'É', // 201 in CP1252
			"\x1BNCE" => 'Ê', // 202 in CP1252
			"\x1BNHE" => 'Ë', // 203 in CP1252
			"\x1BNAI" => 'Ì', // 204 in CP1252
			"\x1BNBI" => 'Í', // 205 in CP1252
			"\x1BNCI" => 'Î', // 206 in CP1252
			"\x1BNHI" => 'Ï', // 207 in CP1252
			"\x1BNb"  => 'Ð', // 208 in CP1252
			"\x1BNDN" => 'Ñ', // 209 in CP1252
			"\x1BNAO" => 'Ò', // 210 in CP1252
			"\x1BNBO" => 'Ó', // 211 in CP1252
			"\x1BNCO" => 'Ô', // 212 in CP1252
			"\x1BNDO" => 'Õ', // 213 in CP1252
			"\x1BNHO" => 'Ö', // 214 in CP1252
			"\x1B-7"  => '×', // 215 in CP1252
			"\x1BNi"  => 'Ø', // 216 in CP1252
			"\x1BNAU" => 'Ù', // 217 in CP1252
			"\x1BNBU" => 'Ú', // 218 in CP1252
			"\x1BNCU" => 'Û', // 219 in CP1252
			"\x1BNHU" => 'Ü', // 220 in CP1252
			"\x1B-="  => 'Ý', // 221 in CP1252
			"\x1BNl"  => 'Þ', // 222 in CP1252
			"\x1BN{"  => 'ß', // 223 in CP1252
			"\x1BNAa" => 'à', // 224 in CP1252
			"\x1BNBa" => 'á', // 225 in CP1252
			"\x1BNCa" => 'â', // 226 in CP1252
			"\x1BNDa" => 'ã', // 227 in CP1252
			"\x1BNHa" => 'ä', // 228 in CP1252
			"\x1BNJa" => 'å', // 229 in CP1252
			"\x1BNq"  => 'æ', // 230 in CP1252
			"\x1BNKc" => 'ç', // 231 in CP1252
			"\x1BNAe" => 'è', // 232 in CP1252
			"\x1BNBe" => 'é', // 233 in CP1252
			"\x1BNCe" => 'ê', // 234 in CP1252
			"\x1BNHe" => 'ë', // 235 in CP1252
			"\x1BNAi" => 'ì', // 236 in CP1252
			"\x1BNBi" => 'í', // 237 in CP1252
			"\x1BNCi" => 'î', // 238 in CP1252
			"\x1BNHi" => 'ï', // 239 in CP1252
			"\x1BNs"  => 'ð', // 240 in CP1252
			"\x1BNDn" => 'ñ', // 241 in CP1252
			"\x1BNAo" => 'ò', // 242 in CP1252
			"\x1BNBo" => 'ó', // 243 in CP1252
			"\x1BNCo" => 'ô', // 244 in CP1252
			"\x1BNDo" => 'õ', // 245 in CP1252
			"\x1BNHo" => 'ö', // 246 in CP1252
			"\x1B/7"  => '÷', // 247 in CP1252
			"\x1BNy"  => 'ø', // 248 in CP1252
			"\x1BNAu" => 'ù', // 249 in CP1252
			"\x1BNBu" => 'ú', // 250 in CP1252
			"\x1BNCu" => 'û', // 251 in CP1252
			"\x1BNHu" => 'ü', // 252 in CP1252
			"\x1B/="  => 'ý', // 253 in CP1252
			"\x1BN|"  => 'þ', // 254 in CP1252
			"\x1BNHy" => 'ÿ', // 255 in CP1252
		);
	}

	/**
	 * Get whether mbstring extension is available
	 *
	 * @return boolean
	 */
	public static function getIsMbstringEnabled()
	{
		if (isset(self::$_isMbstringEnabled)) {
			return self::$_isMbstringEnabled;
		}

		self::$_isMbstringEnabled = function_exists('mb_convert_encoding') ?
			true : false;

		return self::$_isMbstringEnabled;
	}

	/**
	 * Get whether iconv extension is available
	 *
	 * @return boolean
	 */
	public static function getIsIconvEnabled()
	{
		if (isset(self::$_isIconvEnabled)) {
			return self::$_isIconvEnabled;
		}

		// Fail if iconv doesn't exist
		if (!function_exists('iconv')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		// Sometimes iconv is not working, and e.g. iconv('UTF-8', 'UTF-16LE', 'x') just returns false,
		if (!@iconv('UTF-8', 'UTF-16LE', 'x')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		// Sometimes iconv_substr('A', 0, 1, 'UTF-8') just returns false in PHP 5.2.0
		// we cannot use iconv in that case either (http://bugs.php.net/bug.php?id=37773)
		if (!@iconv_substr('A', 0, 1, 'UTF-8')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		// CUSTOM: IBM AIX iconv() does not work
		if ( defined('PHP_OS') && @stristr(PHP_OS, 'AIX')
				&& defined('ICONV_IMPL') && (@strcasecmp(ICONV_IMPL, 'unknown') == 0)
				&& defined('ICONV_VERSION') && (@strcasecmp(ICONV_VERSION, 'unknown') == 0) )
		{
			self::$_isIconvEnabled = false;
			return false;
		}

		// If we reach here no problems were detected with iconv
		self::$_isIconvEnabled = true;
		return true;
	}

	public static function buildCharacterSets() {
		if(empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}
		if(empty(self::$_SYLKCharacters)) {
			self::_buildSYLKCharacters();
		}
	}

	/**
	 * Convert from OpenXML escaped control character to PHP control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to unescape
	 * @return 	string
	 */
	public static function ControlCharacterOOXML2PHP($value = '') {
		return str_replace( array_keys(self::$_controlCharacters), array_values(self::$_controlCharacters), $value );
	}

	/**
	 * Convert from PHP control character to OpenXML escaped control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to escape
	 * @return 	string
	 */
	public static function ControlCharacterPHP2OOXML($value = '') {
		return str_replace( array_values(self::$_controlCharacters), array_keys(self::$_controlCharacters), $value );
	}

	/**
	 * Try to sanitize UTF8, stripping invalid byte sequences. Not perfect. Does not surrogate characters.
	 *
	 * @param string $value
	 * @return string
	 */
	public static function SanitizeUTF8($value)
	{
		if (self::getIsIconvEnabled()) {
			$value = @iconv('UTF-8', 'UTF-8', $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
			return $value;
		}

		// else, no conversion
		return $value;
	}

	/**
	 * Check if a string contains UTF8 data
	 *
	 * @param string $value
	 * @return boolean
	 */
	public static function IsUTF8($value = '') {
		return utf8_encode(utf8_decode($value)) === $value;
	}

	/**
	 * Formats a numeric value as a string for output in various output writers forcing
	 * point as decimal separator in case locale is other than English.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function FormatNumber($value) {
		if (is_float($value)) {
			return str_replace(',', '.', $value);
		}
		return (string) $value;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (8-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeShort($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ?
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('CC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (16-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeLong($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ?
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('vC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Convert string from one encoding to another. First try iconv, then mbstring, or no convertion
	 *
	 * @param string $value
	 * @param string $to Encoding to convert to, e.g. 'UTF-8'
	 * @param string $from Encoding to convert from, e.g. 'UTF-16LE'
	 * @return string
	 */
	public static function ConvertEncoding($value, $to, $from)
	{
		if (self::getIsIconvEnabled()) {
			$value = iconv($from, $to, $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, $to, $from);
			return $value;
		}
		if($from == 'UTF-16LE'){
			return self::utf16_decode($value, false);
		}else if($from == 'UTF-16BE'){
			return self::utf16_decode($value);
		}
		// else, no conversion
		return $value;
	}

	/**
	 * Decode UTF-16 encoded strings.
	 *
	 * Can handle both BOM'ed data and un-BOM'ed data.
	 * Assumes Big-Endian byte order if no BOM is available.
	 * This function was taken from http://php.net/manual/en/function.utf8-decode.php
	 * and $bom_be parameter added.
	 *
	 * @param   string  $str  UTF-16 encoded data to decode.
	 * @return  string  UTF-8 / ISO encoded data.
	 * @access  public
	 * @version 0.2 / 2010-05-13
	 * @author  Rasmus Andersson {@link http://rasmusandersson.se/}
	 * @author vadik56
	 */
	public static function utf16_decode( $str, $bom_be=true ) {
		if( strlen($str) < 2 ) return $str;
		$c0 = ord($str{0});
		$c1 = ord($str{1});
		if( $c0 == 0xfe && $c1 == 0xff ) { $str = substr($str,2); }
		elseif( $c0 == 0xff && $c1 == 0xfe ) { $str = substr($str,2); $bom_be = false; }
		$len = strlen($str);
		$newstr = '';
		for($i=0;$i<$len;$i+=2) {
			if( $bom_be ) { $val = ord($str{$i})   << 4; $val += ord($str{$i+1}); }
			else {        $val = ord($str{$i+1}) << 4; $val += ord($str{$i}); }
			$newstr .= ($val == 0x228) ? "\n" : chr($val);
		}
		return $newstr;
	}

	/**
	 * Get character count. First try mbstring, then iconv, finally strlen
	 *
	 * @param string $value
	 * @param string $enc Encoding
	 * @return int Character count
	 */
	public static function CountCharacters($value, $enc = 'UTF-8')
	{
		if (self::getIsIconvEnabled()) {
			return iconv_strlen($value, $enc);
		}

		if (self::getIsMbstringEnabled()) {
			return mb_strlen($value, $enc);
		}

		// else strlen
		return strlen($value);
	}

	/**
	 * Get a substring of a UTF-8 encoded string
	 *
	 * @param string $pValue UTF-8 encoded string
	 * @param int $start Start offset
	 * @param int $length Maximum number of characters in substring
	 * @return string
	 */
	public static function Substring($pValue = '', $pStart = 0, $pLength = 0)
	{
		if (self::getIsIconvEnabled()) {
			return iconv_substr($pValue, $pStart, $pLength, 'UTF-8');
		}

		if (self::getIsMbstringEnabled()) {
			return mb_substr($pValue, $pStart, $pLength, 'UTF-8');
		}

		// else substr
		return substr($pValue, $pStart, $pLength);
	}


	/**
	 * Identify whether a string contains a fractional numeric value,
	 *    and convert it to a numeric if it is
	 *
	 * @param string &$operand string value to test
	 * @return boolean
	 */
	public static function convertToNumberIfFraction(&$operand) {
		if (preg_match('/^'.self::STRING_REGEXP_FRACTION.'$/i', $operand, $match)) {
			$sign = ($match[1] == '-') ? '-' : '+';
			$fractionFormula = '='.$sign.$match[2].$sign.$match[3];
			$operand = PHPExcel_Calculation::getInstance()->_calculateFormulaValue($fractionFormula);
			return true;
		}
		return false;
	}	//	function convertToNumberIfFraction()

	/**
	 * Get the decimal separator. If it has not yet been set explicitly, try to obtain number
	 * formatting information from locale.
	 *
	 * @return string
	 */
	public static function getDecimalSeparator()
	{
		if (!isset(self::$_decimalSeparator)) {
			$localeconv = localeconv();
			self::$_decimalSeparator = $localeconv['decimal_point'] != ''
				? $localeconv['decimal_point'] : $localeconv['mon_decimal_point'];

			if (self::$_decimalSeparator == '')
			{
				// Default to .
				self::$_decimalSeparator = '.';
			}
		}
		return self::$_decimalSeparator;
	}

	/**
	 * Set the decimal separator. Only used by PHPExcel_Style_NumberFormat::toFormattedString()
	 * to format output by PHPExcel_Writer_HTML and PHPExcel_Writer_PDF
	 *
	 * @param string $pValue Character for decimal separator
	 */
	public static function setDecimalSeparator($pValue = '.')
	{
		self::$_decimalSeparator = $pValue;
	}

	/**
	 * Get the thousands separator. If it has not yet been set explicitly, try to obtain number
	 * formatting information from locale.
	 *
	 * @return string
	 */
	public static function getThousandsSeparator()
	{
		if (!isset(self::$_thousandsSeparator)) {
			$localeconv = localeconv();
			self::$_thousandsSeparator = $localeconv['thousands_sep'] != ''
				? $localeconv['thousands_sep'] : $localeconv['mon_thousands_sep'];
		}
		return self::$_thousandsSeparator;
	}

	/**
	 * Set the thousands separator. Only used by PHPExcel_Style_NumberFormat::toFormattedString()
	 * to format output by PHPExcel_Writer_HTML and PHPExcel_Writer_PDF
	 *
	 * @param string $pValue Character for thousands separator
	 */
	public static function setThousandsSeparator($pValue = ',')
	{
		self::$_thousandsSeparator = $pValue;
	}

	/**
	 * Convert SYLK encoded string to UTF-8
	 *
	 * @param string $pValue
	 * @return string UTF-8 encoded string
	 */
	public static function SYLKtoUTF8($pValue = '')
	{
		// If there is no escape character in the string there is nothing to do
		if (strpos($pValue, '') === false) {
			return $pValue;
		}

		foreach (self::$_SYLKCharacters as $k => $v) {
			$pValue = str_replace($k, $v, $pValue);
		}

		return $pValue;
	}

}



class PHPExcel_Cell
{
	/**
	 * Value binder to use
	 *
	 * @var PHPExcel_Cell_IValueBinder
	 */
	private static $_valueBinder = null;

	/**
	 * Column of the cell
	 *
	 * @var string
	 */
	private $_column;

	/**
	 * Row of the cell
	 *
	 * @var int
	 */
	private $_row;

	/**
	 * Value of the cell
	 *
	 * @var mixed
	 */
	private $_value;

	/**
	 * Calculated value of the cell (used for caching)
	 *
	 * @var mixed
	 */
	private $_calculatedValue = null;

	/**
	 * Type of the cell data
	 *
	 * @var string
	 */
	private $_dataType;

	/**
	 * Parent worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_parent;

	/**
	 * Index to cellXf
	 *
	 * @var int
	 */
	private $_xfIndex;

	/**
	 * Attributes of the formula
	 *
	 *
	 */
	private $_formulaAttributes;


	/**
	 * Send notification to the cache controller
	 * @return void
	 **/
	public function notifyCacheController() {
		$this->_parent->getCellCacheController()->updateCacheData($this);
		return $this;
	}

	public function detach() {
		$this->_parent = null;
	}

	public function attach($parent) {
		$this->_parent = $parent;
	}


	/**
	 * Create a new Cell
	 *
	 * @param	string				$pColumn
	 * @param	int				$pRow
	 * @param	mixed				$pValue
	 * @param	string				$pDataType
	 * @param	PHPExcel_Worksheet	$pSheet
	 * @throws	Exception
	 */
	public function __construct($pColumn = 'A', $pRow = 1, $pValue = null, $pDataType = null, PHPExcel_Worksheet $pSheet = null)
	{
		// Initialise cell coordinate
		$this->_column = strtoupper($pColumn);
		$this->_row = $pRow;

		// Initialise cell value
		$this->_value = $pValue;

		// Set worksheet
		$this->_parent = $pSheet;

		// Set datatype?
		if ($pDataType !== NULL) {
			$this->_dataType = $pDataType;
		} else {
			if (!self::getValueBinder()->bindValue($this, $pValue)) {
				throw new Exception("Value could not be bound to cell.");
			}
		}

		// set default index to cellXf
		$this->_xfIndex = 0;
	}

	/**
	 * Get cell coordinate column
	 *
	 * @return string
	 */
	public function getColumn()
	{
		return $this->_column;
	}

	/**
	 * Get cell coordinate row
	 *
	 * @return int
	 */
	public function getRow()
	{
		return $this->_row;
	}

	/**
	 * Get cell coordinate
	 *
	 * @return string
	 */
	public function getCoordinate()
	{
		return $this->_column . $this->_row;
	}

	/**
	 * Get cell value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Get cell value with formatting
	 *
	 * @return string
	 */
	public function getFormattedValue()
	{
		return PHPExcel_Style_NumberFormat::toFormattedString( $this->getCalculatedValue(),
						$this->_parent->getParent()->getCellXfByIndex($this->getXfIndex())->getNumberFormat()->getFormatCode()
			   );
	}

	/**
	 * Set cell value
	 *
	 * This clears the cell formula.
	 *
	 * @param mixed	$pValue					Value
	 * @return PHPExcel_Cell
	 */
	public function setValue($pValue = null)
	{
		if (!self::getValueBinder()->bindValue($this, $pValue)) {
			throw new Exception("Value could not be bound to cell.");
		}
		return $this;
	}

	/**
	 * Set cell value (with explicit data type given)
	 *
	 * @param mixed	$pValue			Value
	 * @param string	$pDataType		Explicit data type
	 * @return PHPExcel_Cell
	 * @throws Exception
	 */
	public function setValueExplicit($pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
	{
		// set the value according to data type
		switch ($pDataType) {
			case PHPExcel_Cell_DataType::TYPE_STRING:
			case PHPExcel_Cell_DataType::TYPE_NULL:
			case PHPExcel_Cell_DataType::TYPE_INLINE:
				$this->_value = PHPExcel_Cell_DataType::checkString($pValue);
				break;

			case PHPExcel_Cell_DataType::TYPE_NUMERIC:
				$this->_value = (float)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_FORMULA:
				$this->_value = (string)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_BOOL:
				$this->_value = (bool)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_ERROR:
				$this->_value = PHPExcel_Cell_DataType::checkErrorCode($pValue);
				break;

			default:
				throw new Exception('Invalid datatype: ' . $pDataType);
				break;
		}

		// set the datatype
		$this->_dataType = $pDataType;

		return $this->notifyCacheController();
	}

	/**
	 * Get calculated cell value
	 *
	 * @return mixed
	 */
	public function getCalculatedValue($resetLog=true)
	{
//		echo 'Cell '.$this->getCoordinate().' value is a '.$this->_dataType.' with a value of '.$this->getValue().'<br />';
		if ($this->_dataType == PHPExcel_Cell_DataType::TYPE_FORMULA) {
			try {
//				echo 'Cell value for '.$this->getCoordinate().' is a formula: Calculating value<br />';
				$result = PHPExcel_Calculation::getInstance()->calculateCellValue($this,$resetLog);
//				echo $this->getCoordinate().' calculation result is '.$result.'<br />';
			} catch ( Exception $ex ) {
//				echo 'Calculation Exception: '.$ex->getMessage().'<br />';
				$result = '#N/A';
				throw(new Exception($this->getParent()->getTitle().'!'.$this->getCoordinate().' -> '.$ex->getMessage()));
			}

			if ($result === '#Not Yet Implemented') {
//				echo 'Returning fallback value of '.$this->_calculatedValue.' for cell '.$this->getCoordinate().'<br />';
				return $this->_calculatedValue; // Fallback if calculation engine does not support the formula.
			}
//			echo 'Returning calculated value of '.$result.' for cell '.$this->getCoordinate().'<br />';
			return $result;
		}

//		if (is_null($this->_value)) {
//			echo 'Cell '.$this->getCoordinate().' has no value, formula or otherwise<br />';
//			return null;
//		}
//		echo 'Cell value for '.$this->getCoordinate().' is not a formula: Returning data value of '.$this->_value.'<br />';
		return $this->_value;
	}

	/**
	 * Set calculated value (used for caching)
	 *
	 * @param mixed $pValue	Value
	 * @return PHPExcel_Cell
	 */
	public function setCalculatedValue($pValue = null)
	{
		if (!is_null($pValue)) {
			$this->_calculatedValue = $pValue;
		}

		return $this->notifyCacheController();
	}

	/**
	 * Get old calculated value (cached)
	 *
	 * @return mixed
	 */
	public function getOldCalculatedValue()
	{
		return $this->_calculatedValue;
	}

	/**
	 * Get cell data type
	 *
	 * @return string
	 */
	public function getDataType()
	{
		return $this->_dataType;
	}

	/**
	 * Set cell data type
	 *
	 * @param string $pDataType
	 * @return PHPExcel_Cell
	 */
	public function setDataType($pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
	{
		$this->_dataType = $pDataType;

		return $this->notifyCacheController();
	}

	/**
	 * Has Data validation?
	 *
	 * @return boolean
	 */
	public function hasDataValidation()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot check for data validation when cell is not bound to a worksheet');
		}

		return $this->_parent->dataValidationExists($this->getCoordinate());
	}

	/**
	 * Get Data validation
	 *
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function getDataValidation()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot get data validation for cell that is not bound to a worksheet');
		}

		return $this->_parent->getDataValidation($this->getCoordinate());
	}

	/**
	 * Set Data validation
	 *
	 * @param	PHPExcel_Cell_DataValidation	$pDataValidation
	 * @throws	Exception
	 * @return PHPExcel_Cell
	 */
	public function setDataValidation(PHPExcel_Cell_DataValidation $pDataValidation = null)
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot set data validation for cell that is not bound to a worksheet');
		}

		$this->_parent->setDataValidation($this->getCoordinate(), $pDataValidation);

		return $this->notifyCacheController();
	}

	/**
	 * Has Hyperlink
	 *
	 * @return boolean
	 */
	public function hasHyperlink()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
		}

		return $this->_parent->hyperlinkExists($this->getCoordinate());
	}

	/**
	 * Get Hyperlink
	 *
	 * @throws Exception
	 * @return PHPExcel_Cell_Hyperlink
	 */
	public function getHyperlink()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
		}

		return $this->_parent->getHyperlink($this->getCoordinate());
	}

	/**
	 * Set Hyperlink
	 *
	 * @param	PHPExcel_Cell_Hyperlink	$pHyperlink
	 * @throws	Exception
	 * @return PHPExcel_Cell
	 */
	public function setHyperlink(PHPExcel_Cell_Hyperlink $pHyperlink = null)
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
		}

		$this->_parent->setHyperlink($this->getCoordinate(), $pHyperlink);

		return $this->notifyCacheController();
	}

	/**
	 * Get parent
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Re-bind parent
	 *
	 * @param PHPExcel_Worksheet $parent
	 * @return PHPExcel_Cell
	 */
	public function rebindParent(PHPExcel_Worksheet $parent) {
		$this->_parent = $parent;

		return $this->notifyCacheController();
	}

	/**
	 * Is cell in a specific range?
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	boolean
	 */
	public function isInRange($pRange = 'A1:A1')
	{
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);

		// Translate properties
		$myColumn	= PHPExcel_Cell::columnIndexFromString($this->getColumn()) - 1;
		$myRow		= $this->getRow();

		// Verify if cell is in range
		return (($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn) &&
				($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow)
			   );
	}

	/**
	 * Coordinate from string
	 *
	 * @param	string	$pCoordinateString
	 * @return	array	Array containing column and row (indexes 0 and 1)
	 * @throws	Exception
	 */
	public static function coordinateFromString($pCoordinateString = 'A1')
	{
		if (preg_match("/^([$]?[A-Z]{1,3})([$]?\d{1,5})$/", $pCoordinateString, $matches)) {
			return array($matches[1],$matches[2]);
		} elseif ((strpos($pCoordinateString,':') !== false) || (strpos($pCoordinateString,',') !== false)) {
			throw new Exception('Cell coordinate string can not be a range of cells.');
		} elseif ($pCoordinateString == '') {
			throw new Exception('Cell coordinate can not be zero-length string.');
		} else {
			throw new Exception('Invalid cell coordinate '.$pCoordinateString);
		}
	}

	/**
	 * Make string coordinate absolute
	 *
	 * @param	string	$pCoordinateString
	 * @return	string	Absolute coordinate
	 * @throws	Exception
	 */
	public static function absoluteCoordinate($pCoordinateString = 'A1')
	{
		if (strpos($pCoordinateString,':') === false && strpos($pCoordinateString,',') === false) {
			// Create absolute coordinate
			list($column, $row) = PHPExcel_Cell::coordinateFromString($pCoordinateString);
			if ($column[0] == '$')	$column = substr($column,1);
			if ($row[0] == '$')		$row = substr($row,1);
			return '$' . $column . '$' . $row;
		} else {
			throw new Exception("Coordinate string should not be a cell range.");
		}
	}

	/**
	 * Split range into coordinate strings
	 *
	 * @param	string	$pRange
	 * @return	array	Array containg one or more arrays containing one or two coordinate strings
	 */
	public static function splitRange($pRange = 'A1:A1')
	{
		$exploded = explode(',', $pRange);
		$counter = count($exploded);
		for ($i = 0; $i < $counter; ++$i) {
			$exploded[$i] = explode(':', $exploded[$i]);
		}
		return $exploded;
	}

	/**
	 * Build range from coordinate strings
	 *
	 * @param	array	$pRange	Array containg one or more arrays containing one or two coordinate strings
	 * @return  string	String representation of $pRange
	 * @throws	Exception
	 */
	public static function buildRange($pRange)
	{
		// Verify range
		if (!is_array($pRange) || count($pRange) == 0 || !is_array($pRange[0])) {
			throw new Exception('Range does not contain any information.');
		}

		// Build range
		$imploded = array();
		$counter = count($pRange);
		for ($i = 0; $i < $counter; ++$i) {
			$pRange[$i] = implode(':', $pRange[$i]);
		}
		$imploded = implode(',', $pRange);

		return $imploded;
	}

	/**
	 * Calculate range boundaries
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range coordinates (Start Cell, End Cell) where Start Cell and End Cell are arrays (Column Number, Row Number)
	 */
	public static function rangeBoundaries($pRange = 'A1:A1')
	{
		// Uppercase coordinate
		$pRange = strtoupper($pRange);

		// Extract range
		if (strpos($pRange, ':') === false) {
			$rangeA = $rangeB = $pRange;
		} else {
			list($rangeA, $rangeB) = explode(':', $pRange);
		}

		// Calculate range outer borders
		$rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
		$rangeEnd	= PHPExcel_Cell::coordinateFromString($rangeB);

		// Translate column into index
		$rangeStart[0]	= PHPExcel_Cell::columnIndexFromString($rangeStart[0]);
		$rangeEnd[0]	= PHPExcel_Cell::columnIndexFromString($rangeEnd[0]);

		return array($rangeStart, $rangeEnd);
	}

	/**
	 * Calculate range dimension
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range dimension (width, height)
	 */
	public static function rangeDimension($pRange = 'A1:A1')
	{
		// Calculate range outer borders
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);

		return array( ($rangeEnd[0] - $rangeStart[0] + 1), ($rangeEnd[1] - $rangeStart[1] + 1) );
	}

	/**
	 * Calculate range boundaries
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range boundaries (staring Column, starting Row, Final Column, Final Row)
	 */
	public static function getRangeBoundaries($pRange = 'A1:A1')
	{
		// Uppercase coordinate
		$pRange = strtoupper($pRange);

		// Extract range
		if (strpos($pRange, ':') === false) {
			$rangeA = $pRange;
			$rangeB = $pRange;
		} else {
			list($rangeA, $rangeB) = explode(':', $pRange);
		}

		return array( self::coordinateFromString($rangeA), self::coordinateFromString($rangeB));
	}

	/**
	 * Column index from string
	 *
	 * @param	string $pString
	 * @return	int Column index (base 1 !!!)
	 * @throws	Exception
	 */
	public static function columnIndexFromString($pString = 'A')
	{
		//	It's surprising how costly the strtoupper() and ord() calls actually are, so we use a lookup array rather than use ord()
		//		and make it case insensitive to get rid of the strtoupper() as well. Because it's a static, there's no significant
		//		memory overhead either
		static $_columnLookup = array(
			'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
			'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
			'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
			'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26
		);

		//	We also use the language construct isset() rather than the more costly strlen() function to match the length of $pString
		//		for improved performance
		if (isset($pString{0})) {
			if (!isset($pString{1})) {
				return $_columnLookup[$pString];
			} elseif(!isset($pString{2})) {
				return $_columnLookup[$pString{0}] * 26 + $_columnLookup[$pString{1}];
			} elseif(!isset($pString{3})) {
				return $_columnLookup[$pString{0}] * 676 + $_columnLookup[$pString{1}] * 26 + $_columnLookup[$pString{2}];
			}
		}
		throw new Exception("Column string index can not be " . ((isset($pString{0})) ? "longer than 3 characters" : "empty") . ".");
	}

	/**
	 * String from columnindex
	 *
	 * @param int $pColumnIndex Column index (base 0 !!!)
	 * @return string
	 */
	public static function stringFromColumnIndex($pColumnIndex = 0)
	{
		// Determine column string
		if ($pColumnIndex < 26) {
			return chr(65 + $pColumnIndex);
		} elseif ($pColumnIndex < 702) {
			return chr(64 + ($pColumnIndex / 26)).chr(65 + $pColumnIndex % 26);
		}
		return chr(64 + (($pColumnIndex - 26) / 676)).chr(65 + ((($pColumnIndex - 26) % 676) / 26)).chr(65 + $pColumnIndex % 26);
	}

	/**
	 * Extract all cell references in range
	 *
	 * @param	string	$pRange		Range (e.g. A1 or A1:A10 or A1:A10 A100:A1000)
	 * @return	array	Array containing single cell references
	 */
	public static function extractAllCellReferencesInRange($pRange = 'A1') {
		// Returnvalue
		$returnValue = array();

		// Explode spaces
		$cellBlocks = explode(' ', str_replace('$', '', strtoupper($pRange)));
		foreach ($cellBlocks as $cellBlock) {
			// Single cell?
			if (strpos($cellBlock,':') === false && strpos($cellBlock,',') === false) {
				$returnValue[] = $cellBlock;
				continue;
			}

			// Range...
			$ranges = PHPExcel_Cell::splitRange($cellBlock);
			foreach($ranges as $range) {
				// Single cell?
				if (!isset($range[1])) {
					$returnValue[] = $range[0];
					continue;
				}

				// Range...
				list($rangeStart, $rangeEnd)	= $range;
				list($startCol, $startRow)	= sscanf($rangeStart,'%[A-Z]%d');
				list($endCol, $endRow)		= sscanf($rangeEnd,'%[A-Z]%d');
				$endCol++;

				// Current data
				$currentCol	= $startCol;
				$currentRow	= $startRow;

				// Loop cells
				while ($currentCol != $endCol) {
					while ($currentRow <= $endRow) {
						$returnValue[] = $currentCol.$currentRow;
						++$currentRow;
					}
					++$currentCol;
					$currentRow = $startRow;
				}
			}
		}

		// Return value
		return $returnValue;
	}

	/**
	 * Compare 2 cells
	 *
	 * @param	PHPExcel_Cell	$a	Cell a
	 * @param	PHPExcel_Cell	$a	Cell b
	 * @return	int		Result of comparison (always -1 or 1, never zero!)
	 */
	public static function compareCells(PHPExcel_Cell $a, PHPExcel_Cell $b)
	{
		if ($a->_row < $b->_row) {
			return -1;
		} elseif ($a->_row > $b->_row) {
			return 1;
		} elseif (PHPExcel_Cell::columnIndexFromString($a->_column) < PHPExcel_Cell::columnIndexFromString($b->_column)) {
			return -1;
		} else {
			return 1;
		}
	}

	/**
	 * Get value binder to use
	 *
	 * @return PHPExcel_Cell_IValueBinder
	 */
	public static function getValueBinder() {
		if (is_null(self::$_valueBinder)) {
			self::$_valueBinder = new PHPExcel_Cell_DefaultValueBinder();
		}

		return self::$_valueBinder;
	}

	/**
	 * Set value binder to use
	 *
	 * @param PHPExcel_Cell_IValueBinder $binder
	 * @throws Exception
	 */
	public static function setValueBinder(PHPExcel_Cell_IValueBinder $binder = null) {
		if (is_null($binder)) {
			throw new Exception("A PHPExcel_Cell_IValueBinder is required for PHPExcel to function correctly.");
		}

		self::$_valueBinder = $binder;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if ((is_object($value)) && ($key != '_parent')) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Get index to cellXf
	 *
	 * @return int
	 */
	public function getXfIndex()
	{
		return $this->_xfIndex;
	}

	/**
	 * Set index to cellXf
	 *
	 * @param int $pValue
	 * @return PHPExcel_Cell
	 */
	public function setXfIndex($pValue = 0)
	{
		$this->_xfIndex = $pValue;

		return $this->notifyCacheController();
	}


	public function setFormulaAttributes($pAttributes)
	{
		$this->_formulaAttributes = $pAttributes;
		return $this;
	}

	public function getFormulaAttributes()
	{
		return $this->_formulaAttributes;
	}

}

?>
