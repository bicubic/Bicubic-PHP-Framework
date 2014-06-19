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
$config['code_error_report'] =  E_ALL & ~E_STRICT;
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
$config['mandrill_key'] = "3NeY1O1sRDHpTCIu0wmfYQ";
//recaptcha
$config['recaptcha_publickey'] = "6LcQvvISAAAAAOLuwWc7ZnyE6Uzbe2wZLLPWjGM7";
$config['recaptcha_privatekey'] = "6LcQvvISAAAAABRhjEajksKtTzn7k7XRnENOGDUB";
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
//Mylodon Cloud Storage
$config['mylodon_apikey'] = "hadiuqwye126JjJOUWkhsaKJFDHJKfhjdksjKFJhiubdj39759SKJDH283746273864201982390123JG67DWOWI59137JHAJ498DKBAUWD23ddsadK";
//Certificates for ios push notification
$config['certificate_private'] = "";
$config['certificate_auth'] = "";

?>