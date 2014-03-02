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
//maintainance
$config['maintenance'] = false;
$config['sslavailable'] = false;
//Web values  
$config['web_contact_email'] = "";
$config['web_contact_name'] = "";
$config['web_time_out'] = 360000;
//Data Base values
$config['database_host'] = "localhost";
$config['database_database'] = "bicubic"; 
$config['database_user'] = "root";
$config['database_password'] = "root";
//Email values
$config['email_host'] = "ssl://smtp.gmail.com";
$config['email_port'] = 465;
$config['email_auth'] = true;
$config['email_user'] = "";
$config['email_password'] = "";
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
//bitly
//precios
$config['bitlylogin'] = "";
$config['bitlyapikey'] = "";
//Google Cloud Storage
$config['gsutil'] = "gsutil";
$config['gsbucket'] = "gs://storage/";
$config['gsbucketprivate'] = "gs://storageprivate/";
$config['storage_folder'] = "http://storage/"; 
//certificates ios push notification
$config['certificate_private'] = "/.../server_certificates_bundle_sandbox.pem";
$config['certificate_auth'] = "/.../entrust_root_certification_authority.pem";
?>