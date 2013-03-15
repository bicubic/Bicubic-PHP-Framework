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
$config['server_temp_folder'] = "";
$config['server_logg_file'] = "";
$config['server_app_folder'] = "";
//Web values
$config['web_folder'] = "";
$config['web_url'] = "";
$config['web_secure_url'] = "";
$config['storage_folder'] = "";   
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
$config['email_host'] = "";
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
//google storage
$config['gsutil'] = "";
$config['gsbucket'] = "";
$config['gsimages'] = "images/";
$config['gsuploads'] = "uploads/";
$config['gswines'] = "wines/";
$config['gsvinas'] = "vinas/";
$config['gsprofiles'] = "profiles/";
//default images
$config['default256'] = "images/noimage.png";
//certificates
$config['certificate_private'] = "";
$config['certificate_auth'] = "";
//short url
$config['bitlylogin'] = "";
$config['bitlyapikey'] = "";
?>
