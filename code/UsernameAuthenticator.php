<?php
/**
 * Alternative username authentication method.
 *
 * @author Jeremy Shipman <jeremy@burnbright.co.nz> www.burnbright.co.nz
 **/
class UsernameAuthenticator extends Authenticator {
	
	/**
	 * Attempt to find and authenticate member if possible from the given data
	 *
	 * @param array $data
	 * @param Form $form
	 * @param bool &$success Success flag
	 * @return Member Found member, regardless of successful login
	 */
	protected static function authenticate_member($data, $form, &$success) {
		// Default success to false
		$success = false;

		// Attempt to identify by temporary ID
		$member = null;
		$username = null;
		if(!empty($data['tempid'])) {
			// Find user by tempid, in case they are re-validating an existing session
			$member = Member::member_from_tempid($data['tempid']);
			if($member) $username = $member->Username;
		}

		// Otherwise, get email from posted value instead
		if(!$member && !empty($data['Username'])) {
			$username = $data['Username'];
		}

		// Check default login (see Security::setDefaultAdmin())
		$asDefaultAdmin = $username === Security::default_admin_username();
		if($asDefaultAdmin) {
			// If logging is as default admin, ensure record is setup correctly
			$member = Member::default_admin();
			$success = Security::check_default_admin($username, $data['Password']);
			if($success) return $member;
		}

		// Attempt to identify user by email
		if(!$member && $username) {
			// Find user by email
			$member = Member::get()
				->filter(Member::config()->unique_identifier_field, $username)
				->first();
		}

		// Validate against member if possible
		if($member && !$asDefaultAdmin) {
			$result = $member->checkPassword($data['Password']);
			$success = $result->valid();
		} else {
			$result = new ValidationResult(false, _t('Member.ERRORWRONGCRED'));
		}

		// Emit failure to member and form (if available)
		if(!$success) {
			if($member) $member->registerFailedLogin();
			if($form) $form->sessionMessage($result->message(), 'bad');
		}

		return $member;
	}
	
	/**
	 * Log login attempt
	 * TODO We could handle this with an extension
	 *
	 * @param array $data
	 * @param Member $member
	 * @param bool $success
	 */
	protected static function record_login_attempt($data, $member, $success) {
		if(!Security::config()->login_recording) return;

		// Check email is valid
		$username = isset($data['Username']) ? $data['Username'] : null;
		if(is_array($username)) {
			throw new InvalidArgumentException("Bad email passed to UsernameAuthenticator::authenticate(): $username");
		}

		$attempt = new LoginAttempt();
		if($success) {
			// successful login (member is existing with matching password)
			$attempt->MemberID = $member->ID;
			$attempt->Status = 'Success';

			// Audit logging hook
			$member->extend('authenticated');

		} else {
			// Failed login - we're trying to see if a user exists with this email (disregarding wrong passwords)
			$attempt->Status = 'Failure';
			if($member) {
				// Audit logging hook
				$attempt->MemberID = $member->ID;
				$member->extend('authenticationFailed');

			} else {
				// Audit logging hook
				singleton('Member')->extend('authenticationFailedUnknownUser', $data);
			}
		}

		$attempt->Username = $username;
		$attempt->IP = Controller::curr()->getRequest()->getIP();
		$attempt->write();
	}
	
	/**
	 * Method to authenticate an user
	 *
	 * @param array $RAW_data Raw data to authenticate the user
	 * @param Form $form Optional: If passed, better error messages can be
	 *														 produced by using
	 *														 {@link Form::sessionMessage()}
	 * @return bool|Member Returns FALSE if authentication fails, otherwise
	 *										 the member object
	 * @see Security::setDefaultAdmin()
	 */
	public static function authenticate($data, Form $form = null) {
		// Find authenticated member
		$member = static::authenticate_member($data, $form, $success);
		
		// Optionally record every login attempt as a {@link LoginAttempt} object
		static::record_login_attempt($data, $member, $success);
		
		// Legacy migration to precision-safe password hashes.
		// A login-event with cleartext passwords is the only time
		// when we can rehash passwords to a different hashing algorithm,
		// bulk-migration doesn't work due to the nature of hashing.
		// See PasswordEncryptor_LegacyPHPHash class.
		if($success && $member && isset(self::$migrate_legacy_hashes[$member->PasswordEncryption])) {
			$member->Password = $data['Password'];
			$member->PasswordEncryption = self::$migrate_legacy_hashes[$member->PasswordEncryption];
			$member->write();
		}

		if($success) Session::clear('BackURL');

		return $success ? $member : null;
	}
	
	
	/**
	 * Method that creates the login form for this authentication method
	 *
	 * @param Controller The parent controller, necessary to create the
	 *									 appropriate form action tag
	 * @return Form Returns the login form to use with this authentication
	 *							method
	 */
	public static function get_login_form(Controller $controller) {
		return Object::create("UsernameLoginForm", $controller, "LoginForm");
	}


	/**
	 * Get the name of the authentication method
	 *
	 * @return string Returns the name of the authentication method.
	 */
	public static function get_name() {
		return _t('UsernameAuthenticator.TITLE', "Username &amp; Password");
	}
}
