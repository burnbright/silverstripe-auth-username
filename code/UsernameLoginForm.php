<?php
/**
 * Login form for username login
 * 
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 */
class UsernameLoginForm extends MemberLoginForm {

	protected $authenticator_class = 'UsernameAuthenticator';
	
	function __construct($controller, $name, $fields = null, $actions = null,$checkCurrentUser = true) {
		parent::__construct($controller, $name, $fields = null, $actions = null, $checkCurrentUser = true);
			
	/*	$this->actions->push(new LiteralField(
					'forgotUsername',
					'<p id="ForgotUsername"><a href="UsernameLoginForm/lostusername">' ."I've lost my username" . '</a></p>'
				));*/	
		
		if($field = $this->Fields()->fieldByName('Email')){
			$field->setTitle('Username');
		}							 	
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

	//TODO: add "lost username link"
	//TODO: modify lost password to ask for either email or username

	
}
?>