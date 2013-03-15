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
$config['server_logg_file'] = "/tmp/log_";
$config['server_app_folder'] = "/srv/http/path/";
//Web values
$config['web_folder'] = "http://192.168.100.100/path/";
$config['web_url'] = "http://192.168.100.100/path/index.php";
$config['web_secure_url'] = "http://192.168.100.100/path/index.php";
$config['storage_folder'] = "http://storage.mydomain/";   
$config['web_contact_email'] = "contact@mmydomain";
$config['web_contact_name'] = "MyName";
$config['web_time_out'] = 360000;
$config['web_page_items'] = 100;
//Data Base values
$config['database_host'] = "localhost";
$config['database_database'] = "mydb"; 
$config['database_user'] = "myuser";
$config['database_password'] = "mypass";
//Email values
$config['email_host'] = "ssl://smtp.gmail.com";
$config['email_port'] = 465;
$config['email_auth'] = true;
$config['email_user'] = "mygmailaccount";
$config['email_password'] = "mygmailaccountpass";
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
$config['gsutil'] = "gsutilpath";
$config['gsbucket'] = "gs://gsutildomain/";
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
