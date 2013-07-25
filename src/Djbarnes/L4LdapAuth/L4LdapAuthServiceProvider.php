<?php namespace Djbarnes\L4LdapAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Guard;

class L4LdapAuthServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
      public function boot()
      {
        $this->package('djbarnes/l4-ldap-auth');

        \Auth::extend('l4-ldap-auth', function()
        {
            return new Guard(
                new L4LdapAuthUserProvider(
                	\Config::get('l4-ldap-auth::ldapserver'),
                	\Config::get('l4-ldap-auth::ldapadmindn'),
                	\Config::get('l4-ldap-auth::ldapadminpw'),
                	\Config::get('l4-ldap-auth::searchbase'),
                	\Config::get('l4-ldap-auth::searchfield')
                	),
                \App::make('session')
            );
        });
      }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth');
	}

}