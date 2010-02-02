<?php
/**
 * Add username to login recording.
 */
class UsernameLoginAttempt extends DataObjectDecorator {
	
	function extraStatics(){
	
		return array(
			'db' => array(
				'Username' => 'Varchar(255)' 
			)
		);
	
	}
		
}
?>