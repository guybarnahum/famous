<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = [ 'email'     ,
                            'emails'    ,
                            'password'  ,
                            'name'      ,
                            'slogan'    ,
                            'providers' ,
                            'pri_photo_large' ,
                            'pri_photo_medium',
                            'pri_photo_small' ,
                            'created_at',
                            'updated_at',
                            ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
}

/*
 App\Models\User Object ( [table:protected] => users [fillable:protected] => Array ( [0] => email [1] => emails [2] => password [3] => name [4] => slogan [5] => pri_photo_large [6] => pri_photo_medium [7] => pri_photo_small [8] => created_at [9] => updated_at ) [hidden:protected] => Array ( [0] => password [1] => remember_token ) [connection:protected] => [primaryKey:protected] => id [perPage:protected] => 15 [incrementing] => 1 [timestamps] => 1 [attributes:protected] => Array ( [id] => 1 [email] => guy@barnahum.com [emails] => guy@barnahum.com [password] => $2y$10$sPshFN4bP9B/XGiNG5.hiesyyIMhPk9Z8zdQnNRK.pD8t279NgUnC [name] => Guy Bar-Nahum [slogan] => [personality_desc] => [skill_desc] => [bio_id] => [bio_url] => [wiki_url] => [opt_out] => 0 [pri_photo_large] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [pri_photo_medium] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [pri_photo_small] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [remember_token] => 9XQwSkiuJOhxBMOfer6rxmM68utQw4gt01hRH0QI2QHYBavAUVumii0EuDYW [created_at] => 2015-06-08 18:13:58 [updated_at] => 2015-06-08 23:02:37 [accounts] => Illuminate\Database\Eloquent\Collection Object ( [items:protected] => Array ( [0] => App\Models\Account Object ( [table:protected] => accounts [fillable:protected] => Array ( [0] => uid [1] => provider [2] => provider_uid [3] => access_token [4] => email [5] => username [6] => name [7] => state [8] => provider_state ) [connection:protected] => [primaryKey:protected] => id [perPage:protected] => 15 [incrementing] => 1 [timestamps] => 1 [attributes:protected] => Array ( [id] => 2 [uid] => 1 [provider] => facebook [provider_uid] => 10153349360058427 [access_token] => CAAUaFvFKzEYBAHJ8ZA7s4f6P5OhZC86BpdXnTUP58ucGowHdDnvO1cPzsZByQ9iqmn814T5NGmV4uNxusvvk5loBOJq9ZBZAYBOXYmWhfl1K2ZBZAOwJ4EDhB9Q2cYMYFtysIed4PPFmMR45ZCqZBZBRaCHGsamUfL3lT7K22ZCCZCzrzSAB3ISiU1SIZAIOoRi3ZAopUKq79pf38MsutVFGtYPoLh [avatar] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [email] => guy@barnahum.com [username] => [name] => Guy Bar-Nahum [state] => pending [provider_state] => pending [created_at] => 2015-06-08 19:12:56 [updated_at] => 2015-06-08 19:13:40 ) [original:protected] => Array ( [id] => 2 [uid] => 1 [provider] => facebook [provider_uid] => 10153349360058427 [access_token] => CAAUaFvFKzEYBAHJ8ZA7s4f6P5OhZC86BpdXnTUP58ucGowHdDnvO1cPzsZByQ9iqmn814T5NGmV4uNxusvvk5loBOJq9ZBZAYBOXYmWhfl1K2ZBZAOwJ4EDhB9Q2cYMYFtysIed4PPFmMR45ZCqZBZBRaCHGsamUfL3lT7K22ZCCZCzrzSAB3ISiU1SIZAIOoRi3ZAopUKq79pf38MsutVFGtYPoLh [avatar] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [email] => guy@barnahum.com [username] => [name] => Guy Bar-Nahum [state] => pending [provider_state] => pending [created_at] => 2015-06-08 19:12:56 [updated_at] => 2015-06-08 19:13:40 ) [relations:protected] => Array ( ) [hidden:protected] => Array ( ) [visible:protected] => Array ( ) [appends:protected] => Array ( ) [guarded:protected] => Array ( [0] => * ) [dates:protected] => Array ( ) [casts:protected] => Array ( ) [touches:protected] => Array ( ) [observables:protected] => Array ( ) [with:protected] => Array ( ) [morphClass:protected] => [exists] => 1 ) [1] => App\Models\Account Object ( [table:protected] => accounts [fillable:protected] => Array ( [0] => uid [1] => provider [2] => provider_uid [3] => access_token [4] => email [5] => username [6] => name [7] => state [8] => provider_state ) [connection:protected] => [primaryKey:protected] => id [perPage:protected] => 15 [incrementing] => 1 [timestamps] => 1 [attributes:protected] => Array ( [id] => 3 [uid] => 1 [provider] => twitter [provider_uid] => 25172413 [access_token] => 25172413-A5gVKulSMbFOEitsbCaX9qGvFOMd7lwuMlFT9LgsO [avatar] => http://pbs.twimg.com/profile_images/596769562000949249/_LXgBz9y_normal.jpg [email] => [username] => guybarnahum [name] => Guy Bar-Nahum [state] => pending [provider_state] => pending [created_at] => 2015-06-08 19:50:59 [updated_at] => 2015-06-08 19:50:59 ) [original:protected] => Array ( [id] => 3 [uid] => 1 [provider] => twitter [provider_uid] => 25172413 [access_token] => 25172413-A5gVKulSMbFOEitsbCaX9qGvFOMd7lwuMlFT9LgsO [avatar] => http://pbs.twimg.com/profile_images/596769562000949249/_LXgBz9y_normal.jpg [email] => [username] => guybarnahum [name] => Guy Bar-Nahum [state] => pending [provider_state] => pending [created_at] => 2015-06-08 19:50:59 [updated_at] => 2015-06-08 19:50:59 ) [relations:protected] => Array ( ) [hidden:protected] => Array ( ) [visible:protected] => Array ( ) [appends:protected] => Array ( ) [guarded:protected] => Array ( [0] => * ) [dates:protected] => Array ( ) [casts:protected] => Array ( ) [touches:protected] => Array ( ) [observables:protected] => Array ( ) [with:protected] => Array ( ) [morphClass:protected] => [exists] => 1 ) ) ) ) [original:protected] => Array ( [id] => 1 [email] => guy@barnahum.com [emails] => guy@barnahum.com [password] => $2y$10$sPshFN4bP9B/XGiNG5.hiesyyIMhPk9Z8zdQnNRK.pD8t279NgUnC [name] => Guy Bar-Nahum [slogan] => [personality_desc] => [skill_desc] => [bio_id] => [bio_url] => [wiki_url] => [opt_out] => 0 [pri_photo_large] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [pri_photo_medium] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [pri_photo_small] => https://graph.facebook.com/v2.2/10153349360058427/picture?type=normal [remember_token] => 9XQwSkiuJOhxBMOfer6rxmM68utQw4gt01hRH0QI2QHYBavAUVumii0EuDYW [created_at] => 2015-06-08 18:13:58 [updated_at] => 2015-06-08 23:02:37 ) [relations:protected] => Array ( ) [visible:protected] => Array ( ) [appends:protected] => Array ( ) [guarded:protected] => Array ( [0] => * ) [dates:protected] => Array ( ) [casts:protected] => Array ( ) [touches:protected] => Array ( ) [observables:protected] => Array ( ) [with:protected] => Array ( ) [morphClass:protected] => [exists] => 1 ) 1
*/