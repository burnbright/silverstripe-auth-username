<?php

/**
 * UsernameSecurity is an extension of Security, to allow requesting and sending out lost usernames or passwords.
 * 
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 */

class UsernameSecurity extends Security{
	
	
	/**
	 * Show the "lost password / lost username" page
	 *
	 * @return string Returns the "lost password" page as HTML code.
	 */
	public function amnesia() {
		Requirements::javascript(THIRDPARTY_DIR . '/prototype.js');
		Requirements::javascript(THIRDPARTY_DIR . '/behaviour.js');
		Requirements::javascript(THIRDPARTY_DIR . '/loader.js');
		Requirements::javascript(THIRDPARTY_DIR . '/prototype_improvements.js');
		Requirements::javascript(THIRDPARTY_DIR . '/scriptaculous/effects.js');

		$tmpPage = new Page();
		$tmpPage->Title = _t('Security.LOSTUSERNAMEPASSWORDHEADER', 'Lost Username or Password');
		$tmpPage->URLSegment = 'UsernameSecurity';
		$tmpPage->ID = -1;
		$controller = new Page_Controller($tmpPage);
		$controller->init();

		$customisedController = $controller->customise(array(
			'Content' => 
				'<p>' . 
				_t(
					'Security.USERNAMENOTERESETPASSWORD', 
					'Enter your username to be sent a password reset link, or enter your email in the box below to be sent your username.'
				) . 
				'</p>',
			'Form' => $this->LostPasswordForm()->forTemplate().'<br/>'.$this->LostUsernameForm()->forTemplate()
		));
		
		return $customisedController->renderWith(array('UsernameSecurity_lostpassword', 'Security', $this->stat('template_main')));
	}
	
		/**
	 * Get a link to a security action
	 *
	 * @param string $action Name of the action
	 * @return string Returns the link to the given action
	 */
	public static function Link($action = null) {
		return "UsernameSecurity/$action";
	}
	


	/**
	 * Factory method for the lost password form
	 *
	 * @return Form Returns the lost password form
	 */
	public function LostPasswordForm() {
		
		
		return new UsernameLoginForm(
			$this,
			'LostPasswordForm',
			new FieldSet(
				new TextField('Username', _t('Member.USERNAME', 'Username'))
			),
			new FieldSet(
				new FormAction(
					'forgotPassword',
					_t('Security.BUTTONSEND', 'Send me the password reset link')
				)
			),
			false
		);
		
		
	}
	
	public function LostUsernameForm(){
		return new UsernameLoginForm(
			$this,
			'LostUsernameForm',
			new FieldSet(
				new TextField('Email', _t('Member.EMAIL', 'Email'))
			),
			new FieldSet(
				new FormAction(
					'forgotUsername',
					_t('Security.USERNAMESEND', 'Send me my usernames')
				)
			),
			false
		);
	}
	
	
		/**
	 * Show the "password sent" page, after a user has requested
	 * to reset their password.
	 *
	 * @param HTTPRequest $request The HTTPRequest for this action. 
	 * @return string Returns the "password sent" page as HTML code.
	 */
	public function passwordsent($request) {
		Requirements::javascript(THIRDPARTY_DIR . '/behaviour.js');
		Requirements::javascript(THIRDPARTY_DIR . '/loader.js');
		Requirements::javascript(THIRDPARTY_DIR . '/prototype.js');
		Requirements::javascript(THIRDPARTY_DIR . '/prototype_improvements.js');
		Requirements::javascript(THIRDPARTY_DIR . '/scriptaculous/effects.js');

		$tmpPage = new Page();
		$tmpPage->Title = _t('Security.LOSTPASSWORDHEADER');
		$tmpPage->URLSegment = 'UsernameSecurity';
		$tmpPage->ID = -1;
		$controller = new Page_Controller($tmpPage);
		$controller->init();

		$username = (Session::get('ForgotUsername')) ? Convert::raw2xml(Session::get('ForgotUsername')) : null;
		Session::clear('ForgotUsername');
		
		$customisedController = $controller->customise(array(
			'Title' => _t('Security.USERNAMEPASSWORDSENTHEADER', "Password reset link has been sent"),
			'Content' =>
				"<p>" . 
				sprintf(_t('Security.USERNAMEPASSWORDSENTTEXT', "Password reset link has been sent do the email associated with: '%s'"), $username) .
				"</p>",
		));
		
		//Controller::$currentController = $controller;
		return $customisedController->renderWith(array('Security_passwordsent', 'Security', $this->stat('template_main')));
	}
	
	
	public function usernamesent($request) {
		Requirements::javascript(THIRDPARTY_DIR . '/behaviour.js');
		Requirements::javascript(THIRDPARTY_DIR . '/loader.js');
		Requirements::javascript(THIRDPARTY_DIR . '/prototype.js');
		Requirements::javascript(THIRDPARTY_DIR . '/prototype_improvements.js');
		Requirements::javascript(THIRDPARTY_DIR . '/scriptaculous/effects.js');

		$tmpPage = new Page();
		$tmpPage->Title = _t('Security.LOSTPASSWORDHEADER');
		$tmpPage->URLSegment = 'UsernameSecurity';
		$tmpPage->ID = -1;
		$controller = new Page_Controller($tmpPage);
		$controller->init();
		
		$email = (Session::get('ForgotEmail')) ? Convert::raw2xml(Session::get('ForgotEmail')) : null;
		Session::clear('ForgotEmail');
		
		$customisedController = $controller->customise(array(
			'Title' => _t('Security.USERNAMESENTHEADER', "Username sent"),
			'Content' =>
				"<p>" . 
				sprintf(_t('Security.USERNAMESENTTEXT', "Username has been sent to: '%s'"), $email) .
				"</p>",
		));
		
		//Controller::$currentController = $controller;
		return $customisedController->renderWith(array('Security_passwordsent', 'Security', $this->stat('template_main')));
	}
	
	
	
}

?>
