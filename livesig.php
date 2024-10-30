<?php
/*
Plugin Name:	LiveSig
Plugin URI:		http://chaos-laboratory.com/2007/03/22/livesig-wordpress-plug-in-for-automatic-insertion-of-graphical-signatures-generated-by-mylivesignature-at-the-end-of-each-post/
Description:	Adds a Custom Signature from <a href="http://www.mylivesignature.com">MyLiveSignature</a> at the bottom of every post
Version:		0.4
Author:			miCRoSCoPiC^eaRthLinG
Author URI:		http://chaos-laboratory.com
License:		GPL
Last modified: 	2008-05-24 11:45pm
*/

// Database Version
$livesig_db_version = "1.0";

// Settings array
$livesig_general_settings = array(
	"signature-placement"	=>	"front"
);

// Database: Table installation
function livesig_install () {

	global $wpdb;
	global $livesig_db_version;
	global $livesig_general_settings;

	$table_name = $wpdb->prefix . "livesig";
	if( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
      
		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL,
			livesig_code text NOT NULL,
			UNIQUE KEY id (id)
		);";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );

		// Add database version
		add_option( "livesig_db_version", $livesig_db_version );
		// Add default signature placement
		add_option( "livesig-general-settings", serialize( $livesig_general_settings ) );
   
   } // if

} // livesig_install

// Admin Panel
function livesig_add_pages() {
	add_options_page( 'LiveSig Options', 'LiveSig', 8, __FILE__, 'livesig_options_page' );
} // livesig_add_pages

// Options Page
function livesig_options_page() {

	// Form submitted
	if( isset( $_POST[ 'livesig-general-update' ] ) ) {
		$livesig_general_settings[ 'signature-placement' ] = $_POST[ 'signature-placement' ];
		update_option( 'livesig-general-settings', serialize( $livesig_general_settings ) );
		_e( '<div id="message" class="updated fade" style="margin:10px 0 5px 15px;"><p>LiveSig General Settings updated.</p></div>' );
	}

	$livesig_general_settings = unserialize( get_option( 'livesig-general-settings' ) );
	
	$selected_radio = '<fieldset><legend>Signature placement:</legend>';
	$selected_radio .= '<label for="front-placement">' .
							'<input type="radio" id="front-placement" name="signature-placement" value="front"';
	if( $livesig_general_settings[ 'signature-placement' ] == "front" ) 
		$selected_radio .= "checked";
	$selected_radio .=	' />&nbsp;Blog front page only' .
						'</label>';
						
	$selected_radio .= '<label for="single-placement">' .
							'<input type="radio" id="single-placement" name="signature-placement" value="single"';
	if( $livesig_general_settings[ 'signature-placement' ] == "single" ) 
		$selected_radio .= "checked";
	$selected_radio .=	' />&nbsp;Individual Posts only' .
						'</label>';
						
	$selected_radio .= '<label for="both-placement">' .
							'<input type="radio" id="both-placement" name="signature-placement" value="both"';
	if( $livesig_general_settings[ 'signature-placement' ] == "both" ) 
		$selected_radio .= "checked";
	$selected_radio .=	' />&nbsp;Blog front page &amp; individual posts' .
						'</label>';

	$selected_radio .= '</fieldset>';	

	_e( '
	
	<style type="text/css">
	/* ---------------------------------------------------------
								Cleaner
	--------------------------------------------------------- */
	div.cleaner {
		clear:both;
		height:1px;
		font-size:1px;
		border:none;
		margin:0; padding:0;
		background:transparent;
	}
	div#general-settings fieldset {
		margin:10px;
		border:1px solid #D3E5F0;
	}
	div#general-settings fieldset label {
		margin-right:15px;
	}
	input.btn {
		border:1px solid #80B5D0;
		background-color:#E5E5E5;
		cursor:pointer;
	}
	select#role {
		border:1px solid #DDDDDD;
		height:90px;
		float:left;
	}
	select#userlist {
		border:1px solid #DDDDDD;
		height:200px;
		width:200px;
		float:left;
	}
	div#status-message {
		border:1px solid #DDDDDD;
		padding:5px;
	}
	textarea#livesig-code {
		width:99%;
		font-size:14px;
		border:1px solid #CBCBCB;
	}
	</style>

	<link rel="stylesheet" href="' . get_bloginfo( 'url' ) . '/wp-content/plugins/livesig/jquery.ui.tabs.css" type="text/css" media="print, projection, screen">
	<link rel="stylesheet" href="' . get_bloginfo( 'url' ) . '/wp-content/plugins/livesig/ui.tabs.css" type="text/css" media="print, projection, screen">
	<script type="text/javascript" language="javascript" src="' . get_bloginfo( 'url' ) . '/wp-content/plugins/livesig/jquery-ui-tabs.pack.js"></script>
	<script type="text/javascript" language="javascript" src="' . get_bloginfo( 'url' ) . '/wp-content/plugins/livesig/jquery.plugins.pack.js"></script>
	<script type="text/javascript" language="javascript" src="' . get_bloginfo( 'url' ) . '/wp-content/plugins/livesig/livesig.pack.js"></script>
	<script type="text/javascript" language="javascript">
	// Move jQuery to a different namespace
	$jQ = jQuery.noConflict();
	// Root
	var wp_root = "' . str_replace( "\\", "/", ABSPATH ) . '";
	// Backend URL
	var livesig_Backend_URL = "' . get_option( "siteurl" ) . '/wp-content/plugins/livesig/livesig-ajax-backend.php";
	// Loader Image
	var Loader_Image = "' . get_option( "siteurl" ) . '/wp-content/plugins/livesig/loader.gif";
	</script>
	
	<div class="wrap">
	
		<h2>LiveSig Options</h2>
	
		<div id="livesig-tabs">
			<ul>
				<li><a href="#general-settings">General Settings</a></li>
				<li><a href="#signature-settings">Singatures</a></li>
			</ul>
		
			<div id="general-settings">
				<p align="justify">
					<strong>Instructions:</strong> This section defines general settings for the LiveSig plug-in. If you change any of the settings here, you\'re required to save them using the <strong><em>Save</em></strong> button on this screen.
				</p>
				<p align="justify">
					<strong>Warning:</strong> Don\'t confuse this with the <strong><em>Save</em></strong> button in the Signatures section. That is meant for saving the user-specific signatures only.
				</p>
				<form method="post" action="options-general.php?page=livesig/livesig.php">' .
					wp_nonce_field( "update-options" ) .
					$selected_radio .
					'<small>More options coming soon...</small><br /><br />
					<input type="submit" name="livesig-general-update" class="btn" value="Save &raquo;" />
				</form>
			</div>
		
			<div id="signature-settings">
			<p>
				<strong>Usage:</strong> Select the <strong>Role</strong> and <strong>User</strong> from the list on the left and paste the signature code snippet provided by <strong>MyLiveSignature</strong> in the space below and hit <strong><em>Save</em></strong>.
				If you don\'t have your Live Signature yet, please head over to the 
				<a href="http://www.mylivesignature.com"><strong>MyLiveSignature</strong></a> site and get yourself one for <strong>free</strong>.
			</p>
		
			<p>
				<strong>Disabling:</strong> If you wish to stop displaying your Signature, simply delete the code here for the respective user and press <strong><em>Save</em></strong>.
				Alternatively, disabling this plug-in should also do the trick - but this will disable the <em>signature for all users</em>.
			</p>		
		
			<div style="float:left;margin-left:30px;"><strong>&nbsp;User Role:</strong>
				<select id="role" size="5" onchange="getUserList();">
					<option value="Administrator">Administrator</option>
					<option value="Editor">Editor</option>
					<option value="Author">Author</option>
					<option value="Contributor">Contributor</option>
					<option value="Subscriber">Subscriber</option>
				</select>
			</div>

			<div style="float:left;margin-left:30px;"><strong>&nbsp;Username:</strong>
				<select id="userlist" size="12" onchange="getLiveSigCode();">
				</select>
			</div>
		
			<div style="float:left;margin-left:30px;width:450px;min-height:100px;">
				<strong>&nbsp;Status:</strong>
				<div id="status-message">Select a <strong>User Role</strong> role first</div>
			</div>
		
			<div class="cleaner">&nbsp;</div>
		
			<p>
				Paste the MyLiveSignature code here exactly as provided - without any alteration, unless you\'re an an advanced user and <strong>know what you are doing</strong>.
				<br />
				<textarea id="livesig-code" cols="40" rows="3" class="code"></textarea>
					<input type="button" class="btn" value="Save &raquo;" onclick="saveLiveSigCode();" />
			</p>
		
			</div>
		
		</div>
		
	</div>
		
	<script type="text/javascript" language="javascript">
	$jQ( "#livesig-tabs > ul" ).tabs({ 
		fx: { opacity: "toggle" },
		navClass: "livesig-tabs-nav",
		selectedClass: "livesig-tabs-selected",
		unselectClass: "livesig-tabs-unselect",
		disabledClass: "livesig-tabs-disabled",
		panelClass: "livesig-tabs-panel",
		hideClass: "livesig-tabs-hide",
		loadingClass: "livesig-tabs-loading"		
	});
	</script>

	' );

} // livesig_options_page

// Signature Hook: The function that adds the signature after each post
function signature_hook( $content = '' ) {

	// Post
	global $post;
	// DB Object
	global $wpdb;
	
	$livesig_general_settings = unserialize( get_option( 'livesig-general-settings' ) );

	// Get Signature
	$signature = $wpdb->get_row( "SELECT livesig_code FROM wp_livesig WHERE wp_livesig.id IN ( SELECT post_author FROM $wpdb->posts WHERE $wpdb->posts.ID = '$post->ID' ) " );
	
	switch( $livesig_general_settings[ 'signature-placement' ] ) {
		
		case 'front':
			// Display singature only in Front Page only
			if( !is_single() ) {
				// If code is present add signature
				if( !empty ( $signature->livesig_code ) ) {
					// Add signature to content
					$content_with_signature = $content . "<br />" . stripslashes( $signature->livesig_code );
					// Print out the modified content
					echo $content_with_signature;
				}
			}
			else
				echo $content;
			break;
		
		case 'single':
			// Display singature only in SINGLE mode
			if( is_single() ) {
				// If code is present add signature
				if( !empty ( $signature->livesig_code ) ) {
					// Add signature to content
					$content_with_signature = $content . "<br />" . stripslashes( $signature->livesig_code );
					// Print out the modified content
					echo $content_with_signature;
				}
			}
			else
				echo $content;
			break;
			
		case 'both':
			// Display singature in both
			// If code is present add signature
			if( !empty ( $signature->livesig_code ) ) {
				// Add signature to content
				$content_with_signature = $content . "<br />" . stripslashes( $signature->livesig_code );
				// Print out the modified content
				echo $content_with_signature;
			}
			else
				echo $content;
			break;

	} // switch
	
}

// Activation hook
register_activation_hook( __FILE__, 'livesig_install' );

// Add Options Page
add_action( 'admin_menu', 'livesig_add_pages' );

// Add content hook
add_filter( 'the_content', 'signature_hook', 1 );

?>