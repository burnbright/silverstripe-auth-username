<?php
/**
 * Member decorator to add a Username database field.
 *
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 */
class UsernameMemberExtension extends DataExtension{
	
	private static $db = array(
		'Username' => 'Varchar'
	);
	
	/**
	 * Generates a username in the form: FSurname , where F is the first letter of the member's first name
	 * More of the first name is added if that username is already taken
	 */
	function generateUsername(){
		$member = $this->getOwner();
		$username = trim($member->FirstName) . '.' . trim($member->Surname);
		$username = Object::create('SS_Transliterator')->toASCII($username);
		$username = preg_replace('/[ ]/', '-', $username);
		$username = preg_replace('/[^a-zA-Z0-9\-]/', '.', $username);
		$username = preg_replace('/[\.]+/', '.', $username);
		$username = preg_replace('/[-]+/', '-', $username);
		$username = strtolower($username);
		$unique = $username;
		$count = 1;
		while (Member::get()->where("Username = '".Convert::raw2sql($username)."' AND ID != ".Convert::raw2sql($this->owner->ID))->First()) {
			$count++;
			$unique = $username.".".$count;
		}
		return $unique;
	}
	
	function GenerateAndSetPassword(){
		if(!Permission::check("ADMIN")){return false;}
		$member = $this->getOwner();
		// get source sets
		$upper = "ABCDEFGHJKLMNPQRSTUVWXYZ";
		$lower = "abcdefghijkmnpqrstuvwxyz";
		$digits = "23456789";
		$punctuation = "-_=+&*%#@!/?";
		// get 2 of each type
		// get upper chars
		$password = $upper[mt_rand(0, strlen($upper)-1)];
		$password .= $upper[mt_rand(0, strlen($upper)-1)];
		// get lower chars
		$password .= $lower[mt_rand(0, strlen($lower)-1)];
		$password .= $lower[mt_rand(0, strlen($lower)-1)];
		// get digits
		$password .= $digits[mt_rand(0, strlen($digits)-1)];
		$password .= $digits[mt_rand(0, strlen($digits)-1)];
		// get punctuation
		$password .= $punctuation[mt_rand(0, strlen($punctuation)-1)];
		$password .= $punctuation[mt_rand(0, strlen($punctuation)-1)];
		// shuffle the string
		$password = str_shuffle($password);
		$member->changePassword($password);
		return $password;
	}

}