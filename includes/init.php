<?php

function bfa_modifyHtaccess( $path ) {
	if ( bfa_is_subdirectory_install() ) {
		$path = bfa_getABSPATH( $path );
	}
	if ( isset( $_SERVER["SERVER_SOFTWARE"] ) && $_SERVER["SERVER_SOFTWARE"] && preg_match( "/iis/i", $_SERVER["SERVER_SOFTWARE"] ) ) {
		return array( "The plugin does not work with Microsoft IIS. Only with Apache", "error" );
	}

	if ( ! file_exists( $path . ".htaccess" ) ) {
		if ( isset( $_SERVER["SERVER_SOFTWARE"] ) && $_SERVER["SERVER_SOFTWARE"] && preg_match( "/nginx/i", $_SERVER["SERVER_SOFTWARE"] ) ) {
		} else {
			return array( '<label>.htaccess was not found</label>', "error" );
		}
	}
	$htaccess = @file_get_contents( $path . ".htaccess" );

	return $htaccess;
}

function bfa_is_subdirectory_install() {
	if ( strlen( site_url() ) > strlen( home_url() ) ) {
		return true;
	}

	return false;
}

function bfa_getABSPATH( $path ) {
	$siteUrl = site_url();
	$homeUrl = home_url();
	$diff    = str_replace( $homeUrl, "", $siteUrl );
	$diff    = trim( $diff, "/" );

	$pos = strrpos( $path, $diff );

	if ( $pos !== false ) {
		$path = substr_replace( $path, "", $pos, strlen( $diff ) );
		$path = trim( $path, "/" );
		$path = "/" . $path . "/";
	}

	return $path;
}

function bfa_checkmodifyHtaccess( $path ) {
	$re       = '/# BEGIN BLOCK FILE ACCESS[\s\S]*?# END BLOCK FILE ACCESS/';
	$htaccess = bfa_modifyHtaccess( $path );
	preg_match_all( $re, $htaccess, $matches, PREG_SET_ORDER, 0 );

	return count( $matches );
}

function bfa_activate() {
	$path      = ABSPATH;
	$bfa_rules = "\n\n" . "# BEGIN BLOCK FILE ACCESS" . "\n\n" . "RewriteCond %{REQUEST_FILENAME} -s" . "\n" . "RewriteRule ^wp-content/uploads/(.*)$ " . plugin_dir_url( __FILE__ ) . "dl-file.php?file=$1 [QSA,L]" . "\n\n" . "# END BLOCK FILE ACCESS" . "\n\n";

	$htaccess = bfa_modifyHtaccess( $path ) . $bfa_rules;

	if ( ! bfa_checkmodifyHtaccess( $path ) > 0 ) {
		file_put_contents( $path . ".htaccess", $htaccess );
	}
}

function bfa_deactivation() {
	$path = ABSPATH;
	if ( is_file( $path . ".htaccess" ) && is_writable( $path . ".htaccess" ) ) {
		if ( bfa_checkmodifyHtaccess( $path ) > 0 ) {
			$htaccess = preg_replace( "/# BEGIN BLOCK FILE ACCESS[\s\S]*?# END BLOCK FILE ACCESS/", "", bfa_modifyHtaccess( $path ) );

			file_put_contents( $path . ".htaccess", trim( $htaccess ) );
		}
	}
}

