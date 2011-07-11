<?php

/**
 * Bicubic PHP Framework
 * LogData Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
class LogData {

    private $file;

    //Constructor
    //@param $data the base data access object
    function __construct($file) {
        $this->file = $file;
    }

    function insertLog($log) {
        $fh = fopen($this->file . date("Ymd", time()) . ".log", 'a');
        fwrite($fh, $log);
        $fh = fclose($fh);
    }

    function getLog($date) {
        $filename = $this->file . date("Ymd", $date) . ".log";
        $log = "";
        if (file_exists($filename)) {
            $fh = fopen($filename, 'rb');
            if ($fh !== false) {
                while (!feof($fh)) {
                    $log .= fread($fh, 8192);
                }
                $fh = fclose($fh);
            }
        }
        return $log;
    }

}
?>
