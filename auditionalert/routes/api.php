<?php

use Illuminate\Http\Request;
use Mailgun\Mailgun;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* User Routes */
Route::post('/createUser', 'UserController@createUser');
Route::post('/loginUser', 'UserController@loginUser');
Route::post('/getUserById', 'UserController@getUserById');
Route::post('/updateUserById', 'UserController@updateUserById');

/* Dashboard Routes */
Route::get('/getAuditionEvents', 'DashboardController@getAuditionEvents');

/* Get countries */
Route::get('/getCountries', 'UserController@getCountries');

/* Audition Routes */
Route::post('/deleteAudition', 'AuditionController@deleteAudition');
Route::post('/uploadAuditionEvent', 'AuditionController@uploadAuditionEvent');
Route::post('/uploadImage', 'AuditionController@uploadImage');
Route::post('/sendPassword', function(Request $request)
{
	$userData = $request->json()->all();
	require_once(dirname(__FILE__) .'/..'.'/vendor'.'/autoload.php');
	
	$user = DB::table('Users')->select('password')->where('emailAddress', $userData['emailAddress'])->first();
	$user = json_encode($user);
	$user = json_decode($user, true);
	$password = $user['password'];
	
	$mgClient = new Mailgun('aa41289e786981f511b203cccb6db907-8b7bf2f1-de4f9703');
	$domain = "sandbox107e2da105014f918a1c969ba51f0592.mailgun.org";

	# Make the call to the client.
	$result = $mgClient->sendMessage("$domain",
	array('from'    => 'Mailgun Sandbox <postmaster@sandbox107e2da105014f918a1c969ba51f0592.mailgun.org>',
                'to'      => 'Themba Mbhele <'.$userData['emailAddress'].'>',
                'subject' => 'Password',
                'html'    => '<html>
				Hi, <br><br>
					Your password from Audition Alert is '.$password.'
				   </html>'));
	
	return json_encode($result);
	
});

Route::post('/contactUs', function(Request $request)
{
	$userData = $request->json()->all();
	require_once(dirname(__FILE__) .'/..'.'/vendor'.'/autoload.php');
	
	$mgClient = new Mailgun('aa41289e786981f511b203cccb6db907-8b7bf2f1-de4f9703');
	$domain = "sandbox107e2da105014f918a1c969ba51f0592.mailgun.org";
	
	$message = $userData['message'];

	# Make the call to the client.
	$result = $mgClient->sendMessage("$domain",
	array('from'    => 'Mailgun Sandbox <postmaster@sandbox107e2da105014f918a1c969ba51f0592.mailgun.org>',
                'to'      => 'Jessse Okeleye <auditionsalertsa@gmail.com>',
                'subject' => 'Hello Jessse Okeleye',
		'html' => '<html>
					Hi Jessse,<br><br>
					You have a message from the following person:<br>'.
					'User Name: '.$userData['fullName'].'<br>'.
					'Email Address: '.$userData['emailAddress'].'<br>'.
					'Subject: '.$userData['subject'].'<br><br>'.
					'The message is as follows: <br>'.$message.'
				</hmtl>'));
	
	return json_encode($result);
	
});

