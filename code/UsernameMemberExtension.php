<?php

/**
 * Member decorator to add a Username database field.
 */
class UsernameMemberExtension extends DataExtension
{
    
    private static $db = array(
        'Username' => 'Varchar'
    );
    
    /**
     * Generates a username in the form: FSurname , where F is the first letter of the member's first name
     * More of the first name is added if that username is already taken
     */
    public function generateUsername()
    {
        $member = $this->getOwner();
        $count = 1;
        do {
            $username = strtolower(substr($member->FirstName, 0, $count).$member->Surname);
            if (strlen($member->FirstName) < $count) {
                $username .= $count;
            }
            $username = preg_replace("/[^a-zA-Z0-9]/", "", $username);
            $count++;
        } while (Member::get()->filter("Username", $username)->exists());

        return $username;
    }
    
    public function GenerateAndSetPassword()
    {
        if (!Permission::check("ADMIN")) {
            return false;
        }
        $member = $this->getOwner();
        $password = substr(md5(microtime()), 0, 6);
        $member->changePassword($password);
        
        return $password;
    }
}
