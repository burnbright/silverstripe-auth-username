<?php
/** set up authentication **/
Authenticator::register('UsernameAuthenticator');
Member::set_unique_identifier_field('Username');
DataObject::add_extension('Member', 'UsernameMemberDecorator');
DataObject::add_extension('LoginAttempt', 'UsernameLoginAttempt');

MemberTableField::addMembershipFields(array(
	'Username' => 'Username'
));


?>