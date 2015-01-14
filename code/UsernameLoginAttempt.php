<?php
/**
 * Add username to login recording.
 */
class UsernameLoginAttempt extends DataExtension {
	
	static $db = array(
		'Username' => 'Varchar(255)'
	);
		
}