<?php

/**
 * MemberForgotUsernameEmail is similar to the various Emails found on the Member class. For the purpose of retrieving
 * a lost username.
 */
class MemberForgotUsernameEmail extends Email
{

    protected $from = '';  // setting a blank from address uses the site's default administrator email
    protected $subject = '';
    protected $ss_template = 'ForgotUsernameEmail';
    
    public function __construct()
    {
        parent::__construct();
        $this->subject = _t('Member.SUBJECTUSERNAME', "Your username", PR_MEDIUM, 'Email subject');
    }
}
