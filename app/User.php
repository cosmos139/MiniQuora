<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Request;
use Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function signup() {
        $has_username_and_password = $this->has_username_and_password();
        if(!$has_username_and_password)
            return ["status" => 0, "msg" => "Username and Passcode cannot be empty"];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
        $user_exists = $this
          ->where("username", $username)
          ->exists();
        if ($user_exists)
            return ["status" => 0, "msg" => "Username exists!"];
        $hashed_password = bcrypt($password);
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if ($user->save()) 
            return ["status" =>1, "id" => $user->id];

        else
            return ["status" =>0, "msg" => "Insert Failed"];
    }

    public function login() {
        $has_username_and_password = $this->has_username_and_password();
        if(!$has_username_and_password)
            return ["status" => 0, "msg" => "Username and Passcode cannot be empty"];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
        $user = $this->where("username", $username)->first();
        if (!$user)
            return ["status" => 0, "msg" => "User Doesn't Exist"];
        $hashed_password = $user->Password;
        if (!Hash::check($password, $hashed_password))
            return ["status" => 0, "msg" => "Incorrect Password"];
        session()->put("username", $user->username);
        session()->put("user_id", $user->id);

        return ["status" => 1, "id"=>$user-> id];
    }

    public function has_username_and_password() {
        $username = Request::get("username");
        $password = Request::get("password");
        if ($username && $password) 
            return [$username, $password];
        return false;
    }

    public function logout()
    {
        session()->forget("username");
        /*delete username*/
        session()->forget("user_id");
        /*delete user_id*/
        return ["status" => 1];
    }
    /*Chech if the user is logged in*/
    public function is_logged_in(){

        return session("user_id") ?: false;
    }

    public function vote(){
        if(!user_ins() -> is_logged_in())
            return ["status" => 0, "msg" => "login required"];
    }
    public function answers(){
        return $this
           ->belongsToMany("App\Answer")
           ->withPivot("vote")
           ->withTimestamps();

    }
}
