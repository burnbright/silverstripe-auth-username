<?php
class MemberForgotUsernameEmail extends Email {
		
		
    protected $from = '';  // setting a blank from address uses the site's default administrator email
    protected $subject = '';
    protected $ss_template = 'ForgotUsernameEmail';
    
    function __construct() {
		parent::__construct();
    	$this->subject = _t('Member.SUBJECTUSERNAME', "Your username", PR_MEDIUM, 'Email subject');
    }
		
}
?>
