<?php
/**
 * Login form for username login
 * 
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 */
class UsernameLoginForm extends MemberLoginForm {

	protected $authenticator_class = 'UsernameAuthenticator';
	
	
	function __construct($controller, $name, $fields = null, $actions = null,$checkCurrentUser = true) {
		
		$removeemail = ($fields && $fields->fieldByName('Email')) ? false : true;
		
		parent::__construct($controller, $name, $fields, $actions, $checkCurrentUser);
				
		
		if($removeemail && $field = $this->Fields()->fieldByName('Email')){
			$field->setTitle('Username');
		}
		
		if($field = $this->Actions()->fieldByName('forgotPassword')){
			$forgotusernamefield = new LiteralField('forgotUsername','<p id="ForgotPassword"><a href="UsernameSecurity/amnesia">lost username or password</a></p>');
			$this->Actions()->insertAfter($forgotusernamefield,'forgotPassword');
			$this->Actions()->removeByName('forgotPassword');
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


	/**
	 * Forgot password form handler method
	 *
	 * This method is called when the user clicks on "I've lost my password"
	 *
	 * @param array $data Submitted data
	 */
	function forgotPassword($data) {
		$SQL_data = Convert::raw2sql($data);
		$SQL_username = $SQL_data['Username'];
		$member = DataObject::get_one('Member', "Username = '{$SQL_username}'");

		if($member) {
			$member->generateAutologinHash();

			$member->sendInfo(
				'forgotPassword',
				array(
					'PasswordResetLink' => Security::getPasswordResetLink($member->AutoLoginHash)
				)
			);

			Director::redirect('UsernameSecurity/passwordsent/' . urlencode($data['Username']));
		} elseif($data['Username']) {
			$this->sessionMessage(
				_t('Member.ERRORSIGNUP', 'Sorry, but I don\'t recognise the username. Try again, or contact us to resolve this.'
				),
				'bad'
			);
			
			Director::redirectBack();
		} else {
			$this->sessionMessage(
				_t('Member.ENTEREMAIL', 'Please enter a username address to get a password reset link.'),
				'bad'
			);
			
			Director::redirect('UsernameSecurity/amnesia');
		}
	}
	
	
	
	function forgotUsername($data) {
		$SQL_data = Convert::raw2sql($data);
		$SQL_email = $SQL_data['Email'];
		$members = DataObject::get('Member', "Email = '{$SQL_email}'");

		if($members) {

			$member = $members->First();
			
			$e = Object::create('MemberForgotUsernameEmail');
			
			//$e->Usernames = implode($members->map('ID','Username'),',');
			$e->Members = $members;
			$e->populateTemplate($member);
			$e->setTo($member->Email);
			$e->send();

			Director::redirect('UsernameSecurity/usernamesent/'. urlencode($data['Email']));
		} elseif($data['Email']) {
			$this->sessionMessage(
				_t('Member.ERRORSIGNUP', 'Sorry, but I don\'t recognise the email. Try again, or contact us to resolve this.'
				),
				'bad'
			);
			
			Director::redirectBack();
		} else {
			$this->sessionMessage(
				_t('Member.ENTERUSERNAMEEMAIL', 'Please enter an email address to get usernames.'),
				'bad'
			);
			
			Director::redirect('UsernameSecurity/amnesia');
		}
	}

	
}
?>