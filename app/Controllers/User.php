<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    public function login()
    {
        $data = [];

        if ($this->request->getMethod() == 'post') {

					  $user_model = new UserModel();
						$username = ($this->request->getVar('email') !== false && $this->request->getVar('email') !="") ? $this->request->getVar('email') : return redirect()->to(base_url('login'));;
						$password = ($this->request->getVar('password') !== false && $this->request->getVar('password') !="") ? $this->request->getVar('password') : return redirect()->to(base_url('login'));;

						//getouDomain
						list($local, $domain) = explode('@', $username);
						$returnResp="";
						$resp="";
						exec('ldapsearch -H ldaps://ldap.kon.in.th -D "cn=radmin,dc=eai,dc=th" -w "3T7w2Mpwex3gzxNe" -b "ou=domains,dc=eai,dc=th" -s sub "(virtualDomain='.$domain.')" | grep "namespace:"', $resp, $returnResp);
						if($returnResp==0) {
								$domain=substr($resp[0],11);

								//$username = $this->local.'@'.$this->domain;
						}
						$oudomain = $domain;


						$ldap_connection = $user_model->open_ldap_connection();
						$user_authentication = $user_model->ldap_auth_username($ldap_connection, $username, $password, $oudomain);
						list($local, $domain1) = explode('@', $username);
				    $is_admin_user = $local;

				    $returnResp="";
				    $resp="";
				    exec('ldapsearch -H ldaps://ldap.kon.in.th -D "cn=radmin,dc=eai,dc=th" -w "3T7w2Mpwex3gzxNe" -b "ou='.$domain1.',ou=domains,dc=eai,dc=th" -s sub "(canoASCII==?UTF-8?B?'.base64_encode($local).'?=)" | grep "cn:"', $resp, $returnResp);
				    if($returnResp == 0) {
				        $is_admin_user = substr($resp[0],4);
				    }

						$is_admin = $user_model->ldap_is_group_member($ldap_connection,"admin", $is_admin_user, $oudomain);

						//$update_time = $user_model->ldap_update_login_time($ldap_connection, $username, $oudomain);

            $errors = [
                'password' => [
                    'validateUser' => "Email or Password don't match",
                ],
            ];


						if (!empty($user_authentication)){
                    $this->setUserSession($user_authentication);
                    return redirect()->to(base_url('dashboard'));
            } else {
								return redirect()->to(base_url('login'));
            }

        }

    }

    private function setUserSession($user)
    {
        $data = [
            'isLoggedIn' => true,
        ];

        session()->set($data);
        return true;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
