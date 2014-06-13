<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
$config = array();
//Config
$config['maintenance'] = false;
$config['sslavailable'] = false;
//Web  
$config['web_name'] = "Bicubic PHP Framework";
$config['web_copyright'] = "©2014 Bicubic Technology - All rights reserved";
$config['web_contact_email'] = "hello@bicubic.cl";
$config['web_contact_name'] = "Bicubic Technology";
$config['web_time_out'] = 360000;
$config['web_table_size'] = 100;
//Code
$config['code_time_out'] = 300;
$config['code_time_zone'] = 'America/Santiago';
$config['code_error_report'] =  E_ALL & ~E_STRICT;//E_ERROR | E_PARSE | E_NOTICE | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE
//Data Base values
$config['database_host'] = "localhost";
$config['database_database'] = "bicubic";
$config['database_user'] = "root";
$config['database_password'] = "root";
//Email with SMTP
$config['email_host'] = "ssl://smtp.gmail.com";
$config['email_port'] = 465;
$config['email_auth'] = true;
$config['email_user'] = "";
$config['email_password'] = "";
//Email with Mandrill
$config['mandrill_key'] = "";
$config['mandrill_template'] = "";
//recaptcha
$config['recaptcha_publickey'] = "";
$config['recaptcha_privatekey'] = "";
//Facebook
$config['facebook_appid'] = "";
$config['facebook_secret'] = "";
//Twitter
$config['twitter_key'] = "";
$config['twitter_secret'] = "";
//Foursquare
$config['foursquare_key'] = "";
$config['foursquare_secret'] = "";
//Instagram
$config['instagram_key'] = "";
$config['instagram_secret'] = "";
//Bitly
$config['bitly_login'] = "";
$config['bitly_apikey'] = "";
//Google Cloud Storage
$config['gs_util'] = "gsutil";
$config['gs_bucket'] = "gs://mystorage/";
$config['gs_bucketprivate'] = "gs://mystorageprivate/";
$config['gs_storage'] = "http://storage.bicubic.cl/";
//Certificates for ios push notification
$config['certificate_private'] = "/.../server_certificates_bundle_sandbox.pem";
$config['certificate_auth'] = "/.../entrust_root_certification_authority.pem";
?>