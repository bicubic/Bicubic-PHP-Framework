<?php

/*
 * Copyright (C)  Juan Francisco Rodríguez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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