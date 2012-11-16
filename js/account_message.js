jQuery(document).ready(function($) {

	/**
	 * Account 
	 */
	
	//Account update info
	$('#mintthemes_account_msg_close_form').submit(function(event) {
		/* stop form from submitting normally */ 
		event.preventDefault();   
		$.post( '/account-message-plugin/', $("#mintthemes_account_msg_close_form").serialize(),  
		  function( data ) {  
				$('#mintthemes_account_msg_box' ).fadeOut();
		  }  
		);  

	  
	});	

});

