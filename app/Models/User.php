<?php namespace Veer\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Hash;

class User extends \Eloquent implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	
	use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];

	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
        
        
	public function getRememberToken()
	{
		return $this->remember_token;
	}


	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}


	public function getRememberTokenName()
	{
		return 'remember_token';
	}


	public function setPasswordAttribute($pass)
	{
		$this->attributes['password'] = Hash::make($pass);
	}

	// Many Users <- One

	public function site() {
		return $this->belongsTo('\Veer\Models\Site','sites_id','id');
	}

	public function role() {
		return $this->belongsTo('\Veer\Models\UserRole','roles_id','id');
	}

	// One User -> Many

	public function comments() {
		return $this->hasMany('\Veer\Models\Comment', 'users_id');
	}

	public function books() {
		return $this->hasMany('\Veer\Models\UserBook', 'users_id');
	}

	public function discounts() {
		return $this->hasMany('\Veer\Models\UserDiscount', 'users_id');
	}

	public function userlists() {
		return $this->hasMany('\Veer\Models\UserList', 'users_id'); 
	}

	public function orders() {
		return $this->hasMany('\Veer\Models\Order', 'users_id'); 
	}

	public function bills() {
		return $this->hasMany('\Veer\Models\OrderBill', 'users_id', 'id'); 
	}        

	public function communications() {
		return $this->hasMany('\Veer\Models\Communication', 'users_id');
	}

	public function administrator() {
		return $this->hasOne('\Veer\Models\UserAdmin', 'users_id');
	}

	public function searches() {
		return $this->belongsToMany('\Veer\Models\Search','searches_connect', 'users_id', 'searches_id');  
	}    

	public function pages() {
		return $this->hasMany('\Veer\Models\Page', 'users_id');
	}       
	
	// Many-To-Many
	
    public function images() {
        return $this->morphToMany('\Veer\Models\Image', 'elements', 'images_connect', 'elements_id', 'images_id');
    } 	
        
}