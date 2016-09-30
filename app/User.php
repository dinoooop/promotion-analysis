<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;

class User extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'role', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $guarded = [
        'id', 'password'
    ];
    public static $rule_create = [
        'username' => 'required|unique:users',
        'email' => 'required|unique:users',
        'password' => 'required',
        'confirm_password' => 'required|same:password'
    ];
    
    public static function rule_edit($user_id) {
        return [
            'username' => 'required|unique:users,username,' . $user_id,
            'email' => 'required|unique:users,email,' . $user_id,
        ];
    }

    public static function hasrole($capability, $param = null, $condition = 'AND') {

        if ($capability == null) {
            return Auth::check();
        }

        if (Auth::check()) {
            $role = Auth::user()->role;
            $current_user_id = Auth::user()->id;
        } else {
            $role = 'public';
            $current_user_id = 0;
        }

        $system_roles = Stock::get('system_roles');

        if (is_array($capability)) {

            if ($condition == 'OR') {
                foreach ($capability as $value) {
                    if (in_array($value, $system_roles[$role]['capabilities'])) {
                        return true;
                    }
                }
            }

            if ($condition == 'AND') {
                $have = array();
                foreach ($capability as $key => $value) {

                    if (in_array($value, $system_roles[$role]['capabilities'])) {
                        $have[] = 1;
                    }
                }

                if (count($capability) == count($have)) {
                    return true;
                }
            }
        } else {

            switch ($capability) {

                // Special cases for role capability check
                case 'user_delete':
                case 'user_edit_role':
                    $user_id = $param;
                    if ($user_id != $current_user_id && in_array($capability, $system_roles[$role]['capabilities'])) {
                        return true;
                    }

                    break;

                default :
                    if (in_array($capability, $system_roles[$role]['capabilities'])) {
                        return true;
                    }
            }
        }


        return false;
    }

    public static function get_role_name($role) {
        $system_roles = Stock::get('system_roles');
        return (isset($system_roles[$role]['name'])) ? $system_roles[$role]['name'] : '';
    }

}
