<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
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
