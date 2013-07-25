# l4-ldap-auth - Laravel 4 Auth Package

---

A simple ldap authentication package for Laravel 4

---

* Server Configurable
* Search for user from non dn field
* Authenticate users

---

## Installation

Add l4-ldap-auth to your composer.json file:

```
"require": {
  "djbarnes/l4-ldap-auth": "dev-master"
}

"repositories": [
  {
    "type": "vcs"
    "url": "git@github.com:DJBarnes/l4-ldap-auth"
  }
]
```

Now, run a composer update on the command line from the root of your project:

    composer update

### Registering the Package

Add the Verify Service Provider to your config in ``app/config/app.php``:

```php
'providers' => array(
  'Djbarnes\L4LdapAuth\L4LdapAuthServiceProvider'
),
```

### Change the driver

Then change your Auth driver to ``'verify'`` in ``app/config/auth.php``:

```php
'driver' => 'l4-ldap-auth',
```

### Publish the config

Run this on the command line from the root of your project:

    php artisan config:publish djbarnes/l4-ldap-auth

This will publish l4-ldap-auth's config to ``app/config/packages/djbarnes/l4-ldap-auth/``.

## Configuration

Fill in the missing fields for the configuration file at the locatoin mentioned above.

```php
return array(
  'ldapserver' => 'dir.example.com',
  'ldapadmindn' => 'uid=admin,ou=special,ou=people,o=example.com,dc=example,dc=com',
  'ldapadminpw' => 'AdminPassword',
  'searchbase' => 'ou=people,o=example.com,dc=example,dc=com',
  'searchfield' => 'username',
);
```
* **ldapserver** is the url to reach the ldap server
* **ldapadmindn** is the dn for the admin account that can do searches
* **ldapadminpw** is the password for the admin account that can do searches
* **searchbase** is the location in ldap that the search should occur in
* **search field** is the field at the end of the search base that should be used to find a specific user

Because it is possible that a user's dn is not the same as a field designated as thier username, a search for the user based on the username is done in order to obtain the dn. This username field is the one provided in the config's searchfield. Once the user is found, the auth package uses the found users dn and provided password to try to do a ldap bind. If the bind succeeds the user is authenticated. If the bind fails, or ldap can not find the user during the search, the authentication fails.

## Basic Example of Usage

### View - login.blade.php
```php
{{ Form::open(array('action'=>'HomeController@postLogin', 'method'=>'POST')) }}
 
    <p>
      {{ Form::label('username', 'Username:') }}<br />
      {{ Form::text('username') }}
    </p>
 
    <p>
      {{ Form::label('password', 'Password:') }}<br />
      {{ Form::password('password') }}
    </p>
 
    <p>{{ Form::submit('Login') }}</p>
 
    {{ Form::close() }}
```

### Controller - HomeController.php
```php
  public function showLogin()
  {
    return View::make('login');
  }

  public function postLogin()
  {
    if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password'))))
    {
      return 'Logged In';
    }
    else
    {
      return 'Not Authenticated';
    }
  }

}
```

### Routes - routes.php
```php
Route::get('/','HomeController@showLogin');
Route::post('/','HomeController@postLogin', array('before' => 'auth'));
```

Naviagating to the root of the website with these three files changed will present the user with a login screen. Once the user provides credentials, the app will try to authenticate them. If the credentials are correct, a message saying "Logged In" will show up. Otherwise, a message saying "Not Authenticated" will show up.
