<?php
/**
 * Created by NGUYEN TRONG CONG - PhpStorm.
 * User: NTC - 2DEV4U.COM
 * Date: 12/30/2017 - 17:20
 * Project Name: block-file-access
 */

if ( ! class_exists( 'WP_Importer' ) ) {
	class BFA_Check_User_Login {

		public function bfa_Read_File( $mimetype, $file ) {
			header( 'Content-Type: ' . $mimetype ); // always send this
			if ( false === strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) ) {
				header( 'Content-Length: ' . filesize( $file ) );
			}
			$last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file ) );
			$etag          = '"' . md5( $last_modified ) . '"';
			header( "Last-Modified: $last_modified GMT" );
			header( 'ETag: ' . $etag );
			header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );
			// Support for Conditional GET
			$client_etag = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;
			if ( ! isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {
				$_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;
			}
			$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
			// If string is empty, return 0. If not, attempt to parse into a timestamp
			$client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;
			// Make a timestamp for our most recent modification...
			$modified_timestamp = strtotime( $last_modified );
			if ( ( $client_last_modified && $client_etag ) ? ( ( $client_modified_timestamp >= $modified_timestamp ) && ( $client_etag == $etag ) ) : ( ( $client_modified_timestamp >= $modified_timestamp ) || ( $client_etag == $etag ) ) ) {
				status_header( 304 );
				exit;
			}
			// If we made it this far, just serve the file
			readfile( $file );
		}
	}
}