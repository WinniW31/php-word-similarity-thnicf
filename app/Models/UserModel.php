<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{

  protected $LDAP_URI = "ldaps://ldap.kon.in.th";
  protected $LDAP_DEBUG = TRUE;
  //protected $ldap_base_dn = "ou=".$oudomain.",ou=domains,dc=eai,dc=th";
  protected $ldap_admin_bind_dn = "cn=radmin,dc=eai,dc=th";
  protected $ldap_admin_bind_pwd = "3T7w2Mpwex3gzxNe";
  protected $log_prefix = " - LDAP manager ";
  //protected $ldap_group_dn = "ou=".$this->group_ou.",".$this->ldap_base_dn;
  //protected $ldap_user_dn = "ou=".$this->user_ou.",".$this->ldap_base_dn;
  protected $ldap_account_attribute = 'cn';
  protected $ldap_cano_account_attribute = 'cano';


  public function open_ldap_connection() {
          $ldap_connection = @ ldap_connect($this->LDAP_URI);
          if (!$ldap_connection) {
              print "Problem: Can't connect to the LDAP server at ${LDAP['uri']}";
              die("Can't connect to the LDAP server at ${LDAP['uri']}");
              exit(1);
          }
          ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
          if(!preg_match("/^ldaps:/", $this->LDAP_URI)) {
              $tls_result = @ ldap_start_tls($ldap_connection);
              if ($tls_result != TRUE) {
                  error_log("$log_prefix Failed to start STARTTLS connection to ".$this->LDAP_URI.": " . ldap_error($ldap_connection),0);
                  if ($LDAP["require_starttls"] == TRUE) {
                      print "<div style='position: fixed;bottom: 0;width: 100%;' class='alert alert-danger'>Fatal:  Couldn't create a secure connection to ".$this->LDAP_URI." and LDAP_REQUIRE_STARTTLS is TRUE.</div>";
                      exit(0);
                  } else {
                      if ($SENT_HEADERS == TRUE) {
                          print "<div style='position: fixed;bottom: 0px;width: 100%;height: 20px;border-bottom:solid 20px yellow;'>WARNING: Insecure LDAP connection to ".$this->LDAP_URI."</div>";
                      }
                      ldap_close($ldap_connection);
                      $ldap_connection = @ ldap_connect($this->LDAP_URI);
                      ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                  }
              } else if ($this->LDAP_DEBUG == TRUE) {
                  error_log(date('Y-m-d H:i:s') ."$log_prefix Start STARTTLS connection to ".$this->LDAP_URI,0);
              }
          }
          $bind_result = @ ldap_bind( $ldap_connection, $this->ldap_admin_bind_dn, $this->ldap_admin_bind_pwd);

          if ($bind_result != TRUE) {
              $this_error = "Failed to bind to ".$this->LDAP_URI." as".$this->ldap_admin_bind_dn ;
              if ($this->LDAP_DEBUG == TRUE) {
                  $this_error .= " with password".$this->ldap_admin_bind_pwd;
              }
              $this_error .= ": " . ldap_error($ldap_connection);
              print "Problem: Failed to bind as".$this->ldap_admin_bind_dn;
              error_log(date('Y-m-d H:i:s') ."$this->log_prefix $this_error",0);
              exit(1);
          } else if ($this->LDAP_DEBUG == TRUE) {
              error_log(date('Y-m-d H:i:s') ."$this->log_prefix Bound to ".$this->LDAP_URI." as".$this->ldap_admin_bind_dn,0);
          }
          return $ldap_connection;
  } //open_ldap_connection

  public function ldap_auth_username($ldap_connection,$username, $password, $oudomain) {
      # Search for the DN for the given username.  If found, try binding with the DN and user's password.
      # If the binding succeeds, return the DN.
      //$group_ou = (getenv('LDAP_GROUP_OU') ? getenv('LDAP_GROUP_OU') : 'groups');
      //$user_ou = (getenv('LDAP_USER_OU') ? getenv('LDAP_USER_OU') : 'people');

      $text = explode("@", $username);
      $username = $text[0];
      $ldap_search_query="(|(".$this->ldap_account_attribute."=" . ldap_escape($username, "", LDAP_ESCAPE_FILTER).")
                      (".$this->ldap_cano_account_attribute."=" . ldap_escape($username, "", LDAP_ESCAPE_FILTER)."))";

      $ldap_user_ou = (getenv('LDAP_USER_OU') ? getenv('LDAP_USER_OU') : 'people');
      $ldap_base_dn = "ou=".$oudomain.",ou=domains,dc=eai,dc=th";
      $ldap_user_dn = "ou=".$ldap_user_ou.",". $ldap_base_dn;


      $ldap_search = @ ldap_search( $ldap_connection, $ldap_user_dn, $ldap_search_query );
      if ($this->LDAP_DEBUG == TRUE) {
          date('Y-m-d H:i:s') ."$this->log_prefix Running LDAP search: $ldap_search_query";
      }

      if (!$ldap_search) {
          error_log(date('Y-m-d H:i:s') ."$this->log_prefix Couldn't search for ${username}: " . ldap_error($ldap_connection),0);
          return FALSE;
      }

      $result = ldap_get_entries($ldap_connection, $ldap_search);
      if ($this->LDAP_DEBUG == TRUE) {
          error_log(date('Y-m-d H:i:s') ."$this->log_prefix LDAP search returned ${result["count"]} records for $username",0);
      }

      if ($result["count"] == 1) {
          $auth_ldap_connection = $this->open_ldap_connection();
          $can_bind = @ldap_bind( $auth_ldap_connection, $result[0]['dn'], $password);
          ldap_close($auth_ldap_connection);

          if ($can_bind) {
              preg_match("/{$this->ldap_account_attribute}=(.*?),/",$result[0]['dn'],$dn_match);
              return $dn_match[1];
              ldap_unbind($auth_ldap_connection);
              if ($this->LDAP_DEBUG == TRUE) {
                  error_log(date('Y-m-d H:i:s') ."$this->log_prefix Able to bind as $username",0);
              }
          } else {
              if ($this->LDAP_DEBUG == TRUE) {
                  error_log(date('Y-m-d H:i:s') ."$this->log_prefix Unable to bind as ${username}: " . ldap_error($ldap_connection),0);
              }
              return FALSE;
          }
      }
  }//ldap_auth_username

  public function ldap_is_group_member($ldap_connection,$group_name,$username, $oudomain) {

      $ldap_search_query  = "(cn=" . ldap_escape($group_name, "", LDAP_ESCAPE_FILTER) . ")";
      $ldap_group_ou = (getenv('LDAP_GROUP_OU') ? getenv('LDAP_GROUP_OU') : 'groups');
      $ldap_base_dn = "ou=".$oudomain.",ou=domains,dc=eai,dc=th";
      $ldap_group_dn = "ou=".$ldap_group_ou.",". $ldap_base_dn;
      $ldap_user_ou = (getenv('LDAP_USER_OU') ? getenv('LDAP_USER_OU') : 'people');
      $ldap_user_dn = "ou=".$ldap_user_ou.",". $ldap_base_dn;

      $ldap_search        = ldap_search($ldap_connection, $ldap_group_dn, $ldap_search_query);
      $result             = ldap_get_entries($ldap_connection, $ldap_search);

      $ldap_nis_schema = ((strcasecmp(getenv('LDAP_USES_NIS_SCHEMA'),'TRUE') == 0) ? TRUE : FALSE);
      if ($ldap_nis_schema == TRUE) {
          $default_membership_attribute = 'memberuid';
          $default_group_membership_uses_uid = TRUE;
      } else {
          $default_membership_attribute = 'uniquemember';
          $default_group_membership_uses_uid = FALSE;
      }
      $ldap_group_membership_attribute = (getenv('LDAP_GROUP_MEMBERSHIP_ATTRIBUTE') ? getenv('LDAP_GROUP_MEMBERSHIP_ATTRIBUTE') : $default_membership_attribute);
      $ldap_group_membership_uses_uid = ((strcasecmp(getenv('LDAP_GROUP_MEMBERSHIP_USES_UID'),'TRUE') == 0) ? TRUE : $default_group_membership_uses_uid);


      if ($ldap_group_membership_uses_uid == FALSE) {
         $username = $this->ldap_account_attribute."=$username,".$ldap_user_dn;

      //    $text = explode("@", $username);
      //    $username = $text[0];
      //    $username = "(|(${LDAP['account_attribute']}=" . ldap_escape($username, "", LDAP_ESCAPE_FILTER).",${LDAP['user_dn']})
      //                    (${LDAP['cano_account_attribute']}=" . ldap_escape($username, "", LDAP_ESCAPE_FILTER)."),${LDAP['user_dn']})";
      }

      if (preg_grep ("/^${username}$/i", $result[0][$ldap_group_membership_attribute])) {
          return TRUE;
      } else {
          return FALSE;
      }
  }//ldap_is_group_member

  public function ldap_update_login_time($ldap_connection, $username, $oudomain){

    $ldap_base_dn = "ou=".$oudomain.",ou=domains,dc=eai,dc=th";
    $ldap_user_ou = (getenv('LDAP_USER_OU') ? getenv('LDAP_USER_OU') : 'people');
    $ldap_user_dn = "ou=".$ldap_user_ou.",". $ldap_base_dn;
    $to_update["lastlogintime"] = time();

    var_dump($to_update);

    $updated_login_log = ldap_mod_replace($ldap_connection, "cn=".$username.",".$ldap_user_dn, $to_update);

    return $updated_login_log;

  }//ldap_update_login_time
}
