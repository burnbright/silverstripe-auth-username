<?php

class UsernameMemberDecorator extends DataObjectDecorator{
	
	function extraStatics(){
		return array(
			'db' => array(
				'Username' => 'Varchar'
			)
		);
	}
	
	/**
	 * Generates a username in the form: FSurname , where F is the first letter of the member's first name
	 * More of the first name is added if that username is already taken
	 */
	function generateUsername(){
		$member = $this->getOwner();	
		$count = 1;
		do{
			$username = strtolower(substr($member->FirstName,0,$count).$member->Surname);
			if(strlen($member->FirstName) < $count) $username .= $count;
			$username = preg_replace("/[^a-zA-Z0-9\s]/", "", $username);
			$count++;
		}while(DataObject::get_one('Member',"Username = '$username'"));
		return $username;
	}	
}


?>