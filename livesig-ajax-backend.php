<?php
/*
Filename: 		livesig-ajax-backend.php
Date: 			2008-05-17
Copyright: 		2008, miCRoSCoPiC^eaRthLinG
Author: 		Sourjya Sankar Sen (sourjya@choas-laboratory.com)
Description: 	PHP Back-end for LiveSig
License:		GPL
Requires:
*/

// Exit if no function specified
if( !isset( $_POST['action'] ) || '' == $_POST['action'] ) {
	echo '{ errcode: "ERR-000", errmsg: "No action specified" }';
	exit();
}

include( $_POST['wp-root'] . 'wp-config.php' );

switch( $_POST['action'] ) {
	
	case 'get-user-list':
		// Get Role
		$role = $_POST['role'];
		
		// Connect to MySQL
		$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
		if( !$link ) {
			echo '{ errcode: "ERR-200", errmsg: "Database error." }';
			exit();
		}
		// Select DB
		$db_selected = mysql_select_db( DB_NAME, $link );
		
		// Query String
		$query = "";
		switch( $role ) {
			case 'Administrator':
					$query = "SELECT ID, user_login FROM wp_users, wp_usermeta WHERE ( wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%administrator%' ) AND wp_users.ID = wp_usermeta.user_id";
					break;
			case 'Editor':
					$query = "SELECT ID, user_login FROM wp_users, wp_usermeta WHERE ( wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%editor%' ) AND wp_users.ID = wp_usermeta.user_id";
					break;
			case 'Author':
					$query = "SELECT ID, user_login FROM wp_users, wp_usermeta WHERE ( wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%author%' ) AND wp_users.ID = wp_usermeta.user_id";
					break;
			case 'Contributor':
					$query = "SELECT ID, user_login FROM wp_users, wp_usermeta WHERE ( wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%contributor%' ) AND wp_users.ID = wp_usermeta.user_id";
					break;
			case 'Subscriber':
					$query = "SELECT ID, user_login FROM wp_users, wp_usermeta WHERE ( wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value LIKE '%subscriber%' ) AND wp_users.ID = wp_usermeta.user_id";
					break;
		}
		// Query
		$results = mysql_query( $query, $link );
		if( mysql_num_rows( $results ) == 0 ) {
			echo '{ errcode: "ERR-110", errmsg: "No users found." }';
			exit();
		}
		// Parse results
		$resultSet = '{ errcode: "ERR-100", errmsg: "Users found", userlist: [ ';
		while ( $row = mysql_fetch_array( $results, MYSQL_ASSOC ) ) {
			$resultSet .= '{ id: "' . $row["ID"] . '", userlogin: "' . $row["user_login"] . '" }, ';
		}
		$resultSet = substr( $resultSet, 0, strlen( $resultSet ) - 2 ) . ' ] }';

		echo $resultSet;

		break;
		
	case 'get-livesig-code':
		// Get User ID
		$userid = $_POST['userid'];
		
		// Connect to MySQL
		$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
		if( !$link ) {
			echo '{ errcode: "ERR-200", errmsg: "Database error." }';
			exit();
		}
		// Select DB
		$db_selected = mysql_select_db( DB_NAME, $link );
		
		// Query String
		$query = "SELECT livesig_code FROM wp_livesig WHERE id = '$userid';";
		// Query
		$result = mysql_query( $query, $link );
		if( mysql_num_rows( $result ) == 0 ) {
			echo '{ errcode: "ERR-110", errmsg: "No LiveSig Code found." }';
			exit();
		}
		
		// Get row
		$row = mysql_fetch_row( $result );
		echo '{ errcode: "ERR-100", errmsg: "LiveSig Code found", livesig: "' . addslashes( $row[0] ) . '" }';
		
		break;
		
	case 'save-livesig-code':
		// Get User ID
		$userid = $_POST['userid'];
		// Get Code
		$livesig_code = $_POST['livesig-code'];
		
		// Connect to MySQL
		$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
		if( !$link ) {
			echo '{ errcode: "ERR-200", errmsg: "Database error." }';
			exit();
		}
		// Select DB
		$db_selected = mysql_select_db( DB_NAME, $link );
		
		// Query String
		$query = "SELECT COUNT(*) FROM wp_livesig WHERE id = '$userid';";
		// Query
		$result = mysql_query( $query, $link );
		$row = mysql_fetch_row( $result );
		
		// Querystring
		$query = "";
		if( $row[0] == 0 )
			$query = "INSERT INTO wp_livesig( id, livesig_code ) VALUES( $userid, '$livesig_code' );";
		else
			$query = "UPDATE wp_livesig SET livesig_code = '$livesig_code' WHERE id = '$userid';";
		// Query
		$result = mysql_query( $query, $link );
		if( !$result )
			echo '{ errcode: "ERR-110", errmsg: "Database error." }';
		else
			echo '{ errcode: "ERR-100", errmsg: "LiveSig code Saved / Updated." }';

		break;

	default:
		echo '{ errcode: "ERR-210", errmsg: "Unknown error" }';
		exit();
		break;
	
}
?>