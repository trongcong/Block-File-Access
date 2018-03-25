<?php
/**
 * Created by NGUYEN TRONG CONG - PhpStorm.
 * User: NTC - 2DEV4U.COM
 * Date: 12/30/2017 - 17:19
 * Project Name: block-file-access
 */

include( "BFA_Check_Login.php" );
$check = new BFA_Check_User_Login();

require_once( '../../../../wp-load.php' );

list( $basedir ) = array_values( array_intersect_key( wp_upload_dir(), array( 'basedir' => 1 ) ) ) + array( null );
$file = rtrim( $basedir, '/' ) . '/' . str_replace( '..', '', isset( $_GET['file'] ) ? $_GET['file'] : '' );
$url  = ( ! empty( $_SERVER['HTTPS'] ) ) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

if ( ! $basedir || ! is_file( $file ) ) {
	status_header( 404 );
	die( '404 &#8212; File not found.' );
}

$mime        = wp_check_filetype( $file );
$file_access = array();
if ( get_option( 'alfc_link_option' ) ) {
	$alfc_link   = get_option( 'alfc_link_option' );
	$file_access = array_map( 'trim', explode( "\n", trim( $alfc_link ) ) );
}

if ( false === $mime['type'] && function_exists( 'mime_content_type' ) ) {
	$mime['type'] = mime_content_type( $file );
}
if ( $mime['type'] ) {
	$mimetype = $mime['type'];
} else {
	$mimetype = 'image/' . substr( $file, strrpos( $file, '.' ) + 1 );
}

//var_dump( $file );
//die();

if ( is_user_logged_in() ) {
	$check->bfa_Read_File( $mimetype, $file );
} elseif ( ! is_user_logged_in() && is_array( getimagesize( $file ) ) ) {
	$check->bfa_Read_File( $mimetype, $file );
} elseif ( ! is_user_logged_in() && in_array( $url, $file_access ) ) {
	$check->bfa_Read_File( $mimetype, $file );
} elseif ( ! is_user_logged_in() && ! is_array( getimagesize( $file ) ) ) {
	auth_redirect();
}
