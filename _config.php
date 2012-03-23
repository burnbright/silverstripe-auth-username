<?php
/** set up authentication **/
Authenticator::register('UsernameAuthenticator');
Member::set_unique_identifier_field('Username');
DataObject::add_extension('Member', 'UsernameMemberDecorator');
DataObject::add_extension('LoginAttempt', 'UsernameLoginAttempt');

//add username field to MemberTableField
/*MemberTableField::addMembershipFields(array(
	'Username' => 'Username'
));*/