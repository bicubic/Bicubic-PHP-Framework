<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 * @version 1.0.0
 */
$config = array();
//Server values
$config['server_temp_folder'] = "/tmp/";
$config['server_logg_file'] = "/home/apache/logs/bic_";
$config['server_app_folder'] = "/home/apache/http/bic/";
//Web values
$config['web_folder'] = "http://";
$config['web_url'] = "http://index.php";
$config['web_secure_url'] = "https://index.php";
$config['storage_folder'] = "http://storage/";   
$config['web_contact_email'] = "";
$config['web_contact_name'] = "";
$config['web_time_out'] = 360000;
$config['web_page_items'] = 100;
//Data Base values
$config['database_host'] = "";
$config['database_database'] = ""; 
$config['database_user'] = "";
$config['database_password'] = "";
//Email values
$config['email_host'] = "ssl://smtp.gmail.com";
$config['email_port'] = 465;
$config['email_auth'] = true;
$config['email_user'] = "";
$config['email_password'] = "";
//System
$config['system'] = "linux";
//URL params value
$config['param_app'] = "app";
$config['param_navigation'] = "nav";
$config['param_id'] = "id";
$config['param_page'] = "page";
$config['param_compress'] = "cp";
$config['param_lang'] = "lang";
//Folders
$config['folder_template'] = "templates/";
$config['folder_navigation'] = "views/";
$config['folder_uploads'] = "uploads/";
$config['folder_images'] = "images/";
//Facebook
$config['appId'] = "";
$config['secret'] = "";
//twitter
$config['twitterKey'] = "";
$config['twitterSecret'] = "";
//foursquare
$config['foursquareKey'] = "";
$config['foursquareSecret'] = "";
//instagram
$config['instagramKey'] = "";
$config['instagramSecret'] = "";
//certificates ios push notification
$config['certificate_private'] = "/home/src/certificates/server_certificates_bundle_sandbox.pem";
$config['certificate_auth'] = "/home/src/certificates/entrust_root_certification_authority.pem";
?>
