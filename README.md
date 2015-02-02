# SilverStripe Auth Username Module

This module introduces username authentication to SilverStripe.
It also provides additional functionality for retrieving usernames and password.

##  Set Up

Copy the module folder to your SilverStripe site's root directory.

The module's _config.php file automatically:

 * adds the UsernameAuthenticator to the set of registered authenticators
 * decorates Member with a 'Username' DB field, and functions for generating a username, and for generating a password

In your site _config.php optionally add either of the following:

```
Authenticator::set_default_authenticator("UsernameAuthenticator"); // makes username authentication default
Authenticator::unregister("MemberAuthenticator"); // removes default email + password authentication
```

## Maintainer

 * Jeremy Shipman <jeremy@burnbright.net>