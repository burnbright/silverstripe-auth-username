<?php
/**
 * Add username to login recording.
 */
class UsernameLoginAttempt extends DataExtension {
	
	private static $db = array(
		'Username' => 'Varchar(255)'
	);
		
}