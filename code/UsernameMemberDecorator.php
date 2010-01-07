<?php
/**
 * Member decorator to add a Username database field.
 * 
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 */
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
			$username = preg_replace("/[^a-zA-Z0-9]/", "", $username);
			$count++;
		}while(DataObject::get_one('Member',"Username = '$username'"));
		return $username;
	}
	
	function GenerateAndSetPassword(){
		if(!Permission::check("ADMIN")){return false;}
		$member = $this->getOwner();
		$password = substr(md5(microtime()),0,6);
		$member->changePassword($password);
		return $password;
	}

}


?>