<?php
/**
 * Login form for username login
 */
class UsernameLoginForm extends MemberLoginForm {

	protected $authenticator_class = 'UsernameAuthenticator';
	
	
	function __construct($controller, $name, $fields = null, $actions = null,$checkCurrentUser = true) {
										 	
		
			parent::__construct($controller, $name, $fields = null, $actions = null, $checkCurrentUser = true);
			
			$this->Fields()->fieldByName('Email')->setTitle('Username');							 	
	}
	

  /**
   * Try to authenticate the user
   *
   * @param array Submitted data
   * @return Member Returns the member object on successful authentication
   *                or NULL on failure.
   */
	public function performLogin($data) {
		if($member = UsernameAuthenticator::authenticate($data, $this)) {
			$member->LogIn(isset($data['Remember']));
			return $member;

		} else {
			$this->extend('authenticationFailed', $data);
			return null;
		}
	}


}
?>