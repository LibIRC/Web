<?php
if ( !defined( 'INCLUDE_CHECK' ) ) {
	die( 'You are not allowed to execute this file directly' );
}

$db_host     = '';
$db_user     = 'anope';
$db_pass     = '';
$db_database = 'anope';

$link = mysql_connect( $db_host, $db_user, $db_pass ) or die( 'Unable to establish a DB connection' );
mysql_select_db( $db_database, $link );
mysql_query( "SET names UTF8" );
