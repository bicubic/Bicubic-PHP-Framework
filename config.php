<?php

/**
 * Bicubic PHP Framework
 * Configuration File
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    Licenced for ONEMI, Chile
 * @framework  2.1
 */

$config = array(); 
//Server values
$config['server_temp_folder']    = "/tmp/";
$config['server_logg_file']      = "/tmp/cretamal_contentserver_";
$config['server_app_folder']     = "/home/cretamal/ContentServer/git/ContentServer/src/";
//Web values
$config['web_folder']            = "http://localhost/cretamal/contentserver/";
$config['web_url']               = "http://localhost/cretamal/contentserver/index.php";
$config['web_secure_url']        = "http://localhost/cretamal/contentserver/index.php";
$config['web_contact_email']     = "developer@bicubic.cl";
$config['web_contact_name']      = "ContentServer";
$config['web_time_out']          =  1000 * 60 * 60;
$config['web_page_items']        =  15;
//Data Base values
$config['database_host']            = "ancud";
$config['database_database']        = "contentserver";
$config['database_user']            = "cretamal";
$config['database_password']        = "cretamal";
//Email values
$config['email_host']            = "mail.bicubic.cl";
$config['email_port']            =  25;
$config['email_auth']            =  true;
$config['email_user']            = "developer@bicubic.cl";
$config['email_password']        = "-T2yBkK#";
//Daemon values
$config['ext_timeout']           =  3;
$config['daemon_timeout']        =  240;
$config['daemon_ext_timeout']    =  120;
$config['daemon_phppath']        = "nohup php";

$config['daemon_file']           = "\"/home/cretamal/ContentServer/git/ContentServer/src/index.php\"";

$config['daemon_system']         = "linux";
//URL params value
$config['param_app']             = "aa";
$config['param_case']            = "bc";
$config['param_navigation']      = "cn";
$config['param_id']              = "di";
$config['param_page']            = "ep";
$config['param_session']         = "fs";
$config['param_type']            = "gt";
$config['param_url']             = "hu";
$config['param_email']           = "ie";
$config['param_token']           = "jt";
//Folders
$config['folder_template']       = "templates/";
$config['folder_navigation']     = "views/";
$config['folder_resources']      = "resources/";
//Bicubic Account
$config['bicubic_css']           = "http://ancud/cretamal/BicubicAccount/index.php?aa=html&cn=css&app=yofacturo";
$config['bicubic_javascript']    = "http://ancud/cretamal/BicubicAccount/index.php?aa=html&cn=javascript&app=yofacturo";


?>
