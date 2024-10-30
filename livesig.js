/*
File Name: livesig.js
Plugin URI: http://chaos-laboratory.com/2007/03/22/livesig-wordpress-plug-in-for-automatic-insertion-of-graphical-signatures-generated-by-mylivesignature-at-the-end-of-each-post/
Description: Adds a Custom Signature from <a href="http://www.mylivesignature.com">MyLiveSignature</a> at the bottom of every post
Author: miCRoSCoPiC^eaRthLinG
Author URI: http://chaos-laboratory.com
License: GPL
*/

// XMLHttpRequester Object
var reqAjax;

// Gets the user list associated with a particular role
function getUserList() {

	$jQ( '#livesig-code' ).val( '' );
	$jQ( '#role' ).block({ message: null });
	$jQ( '#userlist' ).empty();
	$jQ( '#userlist' ).block({ 
		message: '<img src="' + Loader_Image + '" />',
		css: { border: 'none', background: 'transparent' }
	});
	
	// Send AJAX Request
	try {
		reqAjax = $jQ.ajax( {
						type:		"POST",
						url:		livesig_Backend_URL,
						dataType:	"json",
						data:		"action=get-user-list" +
									"&wp-root=" + wp_root +
									"&role=" + $jQ( "#role" ).val(),
						success:	function( json ) {

										// Debug
										// alert( json.errcode + ' - '  + json.errmsg );return;

										$jQ( '#role' ).unblock(); 
										$jQ( '#userlist' ).unblock();
												
										switch( json.errcode ) {

											case 'ERR-100':
												// Found
												optionSet = '';
												$jQ.each( json.userlist, function( i, n ) {
													optionSet += '<option value="' + json.userlist[i].id + '">' + json.userlist[i].userlogin + '</option>';
												} );
												$jQ( '#userlist' ).html( optionSet );
												$jQ( '#status-message' )
													.html( 'Now select the <strong>Username</strong> for whom you want to add / update the LiveSig code.' )
													.animate( { backgroundColor: "#89EAA2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

											case 'ERR-110':
												// Not found
												$jQ( '#status-message' )
												.html( 'No users belonging to this role were found.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

											case 'ERR-200':
												// Database Error
												$jQ( '#status-message' )
													.html( 'There were errors connecting to the database. Please try again later.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

										} // switch

									},
						error:		function( xhr, msg, ex ) {
										alert( xhr.responseText );
										// Nullify Request Object
										reqAjax = null;
									}
		} );
				
	} catch(e) {
		alert(e);
	} // try-catch
		
} // getUserList

// Gets the LiveSig code associated with a particular user
function getLiveSigCode() {

	$jQ( '#livesig-code' ).val( '' );
	$jQ( '#userlist' ).block({ 
		message: '<img src="' + Loader_Image + '" />',
		css: { border: 'none', background: 'transparent' }
	});
	
	// Send AJAX Request
	try {
		reqAjax = $jQ.ajax( {
						type:		"POST",
						url:		livesig_Backend_URL,
						dataType:	"json",
						data:		"action=get-livesig-code" +
									"&wp-root=" + wp_root +
									"&userid=" + $jQ( "#userlist" ).val(),
						success:	function( json ) {
						
										// Debug
										// alert( json.errcode + ' - '  + json.errmsg );return;

										$jQ( '#userlist' ).unblock();
												
										switch( json.errcode ) {

											case 'ERR-100':
												// Found
												$jQ( '#livesig-code' ).val( stripslashes( json.livesig ) );
												$jQ( '#status-message' )
													.html( 'Once you\'re done with adding / editing the code, make sure you press <strong>Save</strong>.' )
													.animate( { backgroundColor: "#89EAA2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												$jQ( '#livesig-code' ).focus();
												break;

											case 'ERR-110':
												// Not found
												$jQ( '#status-message' )
													.html( 'No LiveSig code corresponding to this user was found. Add the code for this user and press <strong>Save</strong>.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												$jQ( '#livesig-code' ).focus();
												break;

											case 'ERR-200':
												// Database Error
												$jQ( '#status-message' )
													.html( 'There were errors connecting to the database. Please try again later.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

										} // switch

									},
						error:		function( xhr, msg, ex ) {
										alert( xhr.responseText );
										// Nullify Request Object
										reqAjax = null;
									}
		} );
				
	} catch(e) {
		alert(e);
	} // try-catch
	
} // getLiveSigCode

function saveLiveSigCode() {

	// Send AJAX Request
	try {
		reqAjax = $jQ.ajax( {
						type:		"POST",
						url:		livesig_Backend_URL,
						dataType:	"json",
						data:		"action=save-livesig-code" +
									"&wp-root=" + wp_root +
									"&userid=" + $jQ( "#userlist" ).val() + 
									"&livesig-code=" + $jQ( '#livesig-code' ).val(),
						success:	function( json ) {
						
										// Debug
										//alert( json.errcode + ' - '  + json.errmsg );return;

										$jQ( '#userlist' ).unblock();
												
										switch( json.errcode ) {

											case 'ERR-100':
												// Saved
												$jQ( '#status-message' )
													.html( 'LiveSig Code has been <strong>Saved / Updated</strong>.' )
													.animate( { backgroundColor: "#89EAA2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;
												
											case 'ERR-110':
												$jQ( '#status-message' )
													.html( 'An error occured while Saving / Updating the LiveSig code. Please try again.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

											case 'ERR-200':
												// Database Error
												$jQ( '#status-message' )
													.html( 'There were errors connecting to the database. The LiveSig code couldn\'t be <strong>Saved / Updated</strong>. Please try again.' )
													.animate( { backgroundColor: "#FFA2A2" }, 400 )
													.animate( { backgroundColor: "white" }, 600 );
												break;

										} // switch

									},
						error:		function( xhr, msg, ex ) {
										alert( xhr.responseText );
										// Nullify Request Object
										reqAjax = null;
									}
		} );
				
	} catch(e) {
		alert(e);
	} // try-catch
	
}

function addslashes( str ) {
    return str.replace( '/(["\'\])/g', "\\$1" ).replace( '/\0/g', "\\0" );
}

function stripslashes( str ) {
    return str.replace( '/\0/g', '0' ).replace( '/\(.)/g', '$1' );
}