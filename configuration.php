<?php
class JConfig
{
	var $application_env = 'production';
	var $editor = 'ckeditor';
	var $list_limit = '20';
	var $helpurl = 'http://www.hubzero.org';
	var $debug = '0';
	var $debug_lang = '0';
	var $feed_limit = '10';
	var $feed_email = 'author';
	var $secret = '';
	var $gzip = '0';
	var $error_reporting = 'default';
	var $api_server = '1';
	var $xmlrpc_server = '';
	var $log_path = '/var/www/hsscommons/app/logs';
	var $tmp_path = '/var/www/hsscommons/app/tmp';
	var $live_site = '';
	var $force_ssl = '2';
	var $offset = 'America/Vancouver';
	var $sitename = 'hsscommons';
	var $robots = '';
	var $captcha = 'image';
	var $access = '1';
	var $profile = '0';
	var $log_post_data = '0';
	var $caching = '0';
	var $cachetime = '15';
	var $cache_handler = 'file';
	var $memcache_settings = '';
	var $dbtype = 'pdo';
	var $host = 'localhost';
	var $user = 'hsscommons';
	var $password = '';
	var $db = 'hsscommons';
	var $dbcharset = '';
	var $dbcollation = '';
	var $dbprefix = 'jos_';
	var $ftp_enabled = '';
	var $ftp_host = '127.0.0.1';
	var $ftp_port = '21';
	var $ftp_user = '';
	var $ftp_pass = '';
	var $ftp_root = '';
	var $mailer = 'mail';
	var $mailfrom = 'admin@hsscommons.ca';
	var $fromname = 'hsscommons';
	var $smtpauth = '0';
	var $smtphost = 'localhost';
	var $smtpport = '25';
	var $smtpuser = 'admin';
	var $smtppass = '';
	var $smtpsecure = 'none';
	var $sendmail = '/usr/sbin/sendmail';
	var $MetaAuthor = '1';
	var $MetaTitle = '1';
	var $MetaDesc = 'Canadian HSS Commons - Community for Scientific and Educational Collaboration';
	var $MetaKeys = 'hsscommons,HSS Commons,';
	var $MetaRights = '';
	var $MetaVersion = '0';
	var $display_offline_message = '1';
	var $offline_image = '';
	var $offline_message = 'This site is down for maintenance. Please check back again soon.';
	var $offline = '0';
	var $short = array("period" => "1", "limit" => "120");
	var $long = array("period" => "1440", "limit" => "10000");
	var $sef = '1';
	var $sef_rewrite = '1';
	var $sef_suffix = '0';
	var $sef_groups = '0';
	var $unicodeslugs = '0';
	var $sitename_pagetitles = '0';
	var $session_handler = 'database';
	var $lifetime = '15';
	var $cookiesubdomains = '';
	var $cookie_path = '';
	var $cookie_domain = '';
}
