<?php namespace Djbarnes\L4LdapAuth;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\GenericUser;

class L4LdapAuthUserProvider implements UserProviderInterface
{
    /**
     * Create a new database user provider.
     *
     * @param  string  $ldapServer
     * @param  string  $ldapadmindn
     * @param  string  $ldapadminpw
     * @param  string  $searchbase
     * @param  string  $searchfield
     * @return void
     */
    public function __construct($ldapServer, $ldapadmindn, $ldapadminpw, $searchbase, $searchfield)
    {
        $this->model = new GenericUser(['username' => '']);
        $this->ldapServer = $ldapServer;
        $this->ldapAdminDn = $ldapadmindn;
        $this->ldapAdminPw = $ldapadminpw;
        $this->searchBase = $searchbase;
        $this->searchField = $searchfield;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Illuminate\Auth\UserInterface|null
     */
    public function retrieveByID($identifier)
    {
      $connectionString = $this->ldapserver;
      $connection = ldap_connect($connectionString[0]);
      $adminBind = ldap_bind($connection, $this->ldapadmindn, $this->ldapadminpw);
      if(!$adminBind)
        return null; //server down or admin account unavailable

      $result = ldap_search($conection, $this->searchbase,'(' . $this->searchfield . '=' . $credentials['username'] . ')');
      $information = ldap_get_entries($connection,$result);
      if ($information['count']==0)
        null;

      ldap_close($connection);

      //bind successful
      $this->model->id = array_get($information[0],'dn');
      return $this->model;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Illuminate\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
      $connection = ldap_connect($this->ldapServer);
      $adminBind = ldap_bind($connection, $this->ldapAdminDn, $this->ldapAdminPw);
      if(!$adminBind)
        return null; //server down or admin account unavailable

      $result = ldap_search($connection, $this->searchBase, '(' . $this->searchField . '=' . $credentials['username'] . ')');
      $information = ldap_get_entries($connection,$result);
      if ($information['count']==0)
        return null;

      ldap_close($connection);

      //bind successful
      $this->model->id = array_get($information[0],'dn');
      return $this->model;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Illuminate\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */    
    public function validateCredentials(UserInterface $user, array $credentials)
    {
      if($user == null)
        return false;
      if($credentials['password'] == '')
        return false;
      $connection = ldap_connect($this->ldapServer);
      //$result = array();
        if(!$result = @ldap_bind($connection,$user->id,$credentials['password']))
          return false;
      ldap_close($connection);
      return true;
    }
    
}