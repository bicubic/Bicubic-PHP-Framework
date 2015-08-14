<?php

/*
 * The MIT License
 *
 * Copyright 2015 Juan Francisco Rodríguez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
$config = array();
$config['maintenance'] = false;
$config['sslavailable'] = false;
$config['debugdatabase'] = false;
$config['urlforms'] = false;
$config['urlbase'] = "";
//Web
$config['web_name'] = "";
$config['web_copyright'] = "";
$config['web_contact_email'] = "";
$config['web_contact_name'] = "";
$config['web_time_out'] = 360000;
$config['web_table_size'] = 100;
//Code
$config['code_time_out'] = 300;
$config['code_time_zone'] = 'America/Santiago';
$config['code_error_report'] = E_ALL & ~(E_STRICT | E_DEPRECATED);
//Data Base values
$config['database_host'] = "";
$config['database_database'] = "";
$config['database_user'] = "";
$config['database_password'] = "";
//Email with SMTP
$config['email_host'] = "ssl://smtp.gmail.com";
$config['email_port'] = 465;
$config['email_auth'] = true;
$config['email_user'] = "";
$config['email_password'] = "";
//Email with Mandrill
$config['mandrill_key'] = "";
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
$config['mylodon_apikey'] = "";
//Certificates for ios push notification
$config['certificate_private'] = "";
$config['certificate_auth'] = "";

/* URL BASE Apache
  RewriteEngine On
  RewriteCond %{REQUEST_URI} !/assets
  RewriteRule /([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-\.]+)?(.*) /index.php?app=$1&nav=$2&id=$3 [QSA]
  RewriteCond %{REQUEST_URI} !/assets
  RewriteRule /([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-\.]+) /index.php?app=$1&nav=$2&id=$3
  RewriteCond %{REQUEST_URI} !/assets
  RewriteRule /([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-\.]+)?(.*) /index.php?nav=$1&id=$2 [QSA]
  RewriteCond %{REQUEST_URI} !/assets
  RewriteRule /([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-\.]+) /index.php?nav=$1&id=$2
  RewriteCond %{REQUEST_URI} !/assets
  RewriteRule /([a-zA-Z0-9\-]+)?(.*) /index.php?nav=$1 [QSA]
  RewriteRule /([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/assets/(.*) /assets/$3
  RewriteRule /([a-zA-Z0-9\-]+)/assets/(.*) /assets/$2
 */