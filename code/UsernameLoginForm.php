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
			$this->Fields()->insertBefore(
				new TextField("Username", _t('Member.USERNAME', 'Username'), Session::get('SessionForms.MemberLoginForm.Username'), null, $this),
				'Password'
			);
			$this->Fields()->removeByName('Email');
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
	
	
	
	/**
	 * Login form handler method
	 *
	 * This method is called when the user clicks on "Log in"
	 *
	 * @param array $data Submitted data
	 */
	public function dologin($data) {
		if($this->performLogin($data)) {
			Session::clear('SessionForms.MemberLoginForm.Username');
			Session::clear('SessionForms.MemberLoginForm.Remember');
			if(Member::currentUser()->isPasswordExpired()) {
				if(isset($_REQUEST['BackURL']) && $backURL = $_REQUEST['BackURL']) {
					Session::set('BackURL', $backURL);
				}

				$cp = new ChangePasswordForm($this->controller, 'ChangePasswordForm');
				$cp->sessionMessage('Your password has expired.  Please choose a new one.', 'good');
				
				$this->Controller()->redirect('Security/changepassword');
			} elseif(
				isset($_REQUEST['BackURL']) 
				&& $_REQUEST['BackURL'] 
				// absolute redirection URLs may cause spoofing 
				&& Director::is_site_url($_REQUEST['BackURL'])
			) {
				$this->Controller()->redirect($_REQUEST['BackURL']);
			} else {
				$member = Member::currentUser();
				if($member) {
					$firstname = Convert::raw2xml($member->FirstName);
					
					if(!empty($data['Remember'])) {
						Session::set('SessionForms.MemberLoginForm.Remember', '1');
						$member->logIn(true);
					} else {
						$member->logIn();
					}
					
					Session::set('Security.Message.message',
						sprintf(_t('Member.WELCOMEBACK', "Welcome Back, %s"), $firstname) 
					);
					Session::set("Security.Message.type", "good");
				}
				$this->Controller()->redirectBack();
			}
		} else {
			Session::set('SessionForms.MemberLoginForm.Username', $data['Username']);
			Session::set('SessionForms.MemberLoginForm.Remember', isset($data['Remember']));

			if(isset($_REQUEST['BackURL'])) $backURL = $_REQUEST['BackURL']; 
			else $backURL = null; 

		 	if($backURL) Session::set('BackURL', $backURL);			
			
			if($badLoginURL = Session::get("BadLoginURL")) {
				$this->Controller()->redirect($badLoginURL);
			} else {
				// Show the right tab on failed login
				$loginLink = Director::absoluteURL(Security::Link("login")); 
				if($backURL) $loginLink .= '?BackURL=' . urlencode($backURL); 
				$this->Controller()->redirect($loginLink . '#' . $this->FormName() .'_tab');
			}
		}
	}
	
	

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
			Session::set('ForgotUsername',$data['Username']);
			$this->Controller()->redirect('UsernameSecurity/passwordsent/');
		} elseif($data['Username']) {
			$this->sessionMessage(
				_t('Member.ERRORSIGNUP', 'Sorry, but I don\'t recognise the username. Try again, or contact us to resolve this.'
				),
				'bad'
			);
			
			$this->Controller()->redirectBack();
		} else {
			$this->sessionMessage(
				_t('Member.ENTEREMAIL', 'Please enter a username address to get a password reset link.'),
				'bad'
			);
			
			$this->Controller()->redirect('UsernameSecurity/amnesia');
		}
	}
	
	function forgotUsername($data) {
		$SQL_data = Convert::raw2sql($data);
		$SQL_email = $SQL_data['Email'];
		$members = DataObject::get('Member', "Email = '{$SQL_email}'");

		if($members) {

			$member = $members->First();
			$e = Object::create('MemberForgotUsernameEmail');
			
			$e->Members = $members;
			$e->populateTemplate($member);
			$e->setTo($member->Email);
			$e->send();
			Session::set('ForgotEmail',$data['Email']);
			$this->Controller()->redirect('UsernameSecurity/usernamesent/');
		} elseif($data['Email']) {
			$this->sessionMessage(
				_t('Member.ERRORSIGNUP', 'Sorry, but I don\'t recognise the email. Try again, or contact us to resolve this.'
				),
				'bad'
			);
			
			$this->Controller()->redirectBack();
		} else {
			$this->sessionMessage(
				_t('Member.ENTERUSERNAMEEMAIL', 'Please enter an email address to get usernames.'),
				'bad'
			);
			
			$this->Controller()->redirect('UsernameSecurity/amnesia');
		}
	}

	
}
?>