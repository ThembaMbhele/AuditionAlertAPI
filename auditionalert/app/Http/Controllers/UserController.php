<?php
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Mailgun\Mailgun;
	
	class UserController extends Controller
	{
		public function getCountries()
		{
			$countries = DB::table('Country')->get();
			
			return json_encode($countries);
		}
		
		public function sendPassword(Request $request)
		{
			$requestData = $request->json()->all();
			require_once(dirname(__FILE__) .'/..'.'/vendor'.'/autoload.php');

			$mgClient = new Mailgun('aa41289e786981f511b203cccb6db907-8b7bf2f1-de4f9703');
			$domain = "sandbox107e2da105014f918a1c969ba51f0592.mailgun.org";

			# Make the call to the client.
			$result = $mgClient->sendMessage("$domain",
			array('from'    => 'Mailgun Sandbox <postmaster@sandbox107e2da105014f918a1c969ba51f0592.mailgun.org>',
				'to'      => 'Themba Mbhele <mbhelethemba4@gmail.com>',
				'subject' => 'Hello Themba Mbhele',
				'text'    => 'Yolo'));
			
			return json_encode($result);
		}
		
		public function updateUserById(Request $request)
		{
			$user = $request->json()->all();
			DB::table('Users')->where('userId', $user['userId'])
						->update(['firstName' => $user['firstName'], 
								'lastName' => $user['lastName'], 
								'emailAddress' => $user['emailAddress'], 
								'country' => $user['country'], 
								'dateOfBirth' => $user['dateOfBirth'],
								'gender' => $user['gender'],
								'career' => $user['career'],
								'cellPhone' => $user['cellPhone']
								]);
								
			$response = array('result' => true, 'errorMessage' => null, 'data' => null);
			return json_encode($response);
		}
		
		public function getUserById(Request $request)
		{
			$userId = $request->json()->all();
			
			$user = DB::table('Users')->where('userId', $userId['id'])->first();
			
			return json_encode($user);
		}
		
		public function createUser(Request $request)
		{
			$userData = $request->json()->all();
			
			/*check if a user with the same email already exist*/
			$isDuplicate = DB::table('Users')->select('userId')->where('emailAddress', $userData['emailAddress'])->first();
			if(count($isDuplicate) <= 0)
			{
				/*insert new user*/
				DB::insert('insert into Users (firstName, lastName, emailAddress, password, country, firstLogin) 
						values (?, ?, ?, ?, ?, ?)', [$userData['firstName'], $userData['lastName'], $userData['emailAddress'], $userData['password'], $userData['country'], true]);
				
				/*query db to get user id that will be placed in session storage*/
				$user = DB::table('Users')->select('userId', 'userType')->where('emailAddress', $userData['emailAddress'])->first();
				
				/*create json response and return it*/
				$response = array('result' => true, 'errorMessage' => null, 'data' => $user);
				return json_encode($response);
			}
			else
			{
				$response = array('result' => false, 'errorMessage' => 'user already exists', 'data' => null);
				return json_encode($response);
			}
		}
		
		public function loginUser(Request $request)
		{
			$userData = $request->json()->all();
			
			/*query database for provided credentials*/
			$user = DB::table('Users')->select('userId', 'firstLogin', 'userType')->where([ ['emailAddress', $userData['emailAddress']], ['password', $userData['password']]])->get();
			
			/*determine whether user exists*/
			if(count($user) == 0)
			{
				/*user does not exist. return appropriate response*/
				$response = array('result' => false, 'errorMessage' => 'user does not exist', 'data' => null);
				return json_encode($response);
			}
			else if(count($user) == 1)
			{
				/*return user object*/
				$response = array('result' => true, 'errorMessage' => null, 'data' => $user[0]);
				return json_encode($response);
			}
			else
			{
				$response = array('result' => false, 'errorMessage' => 'contact admin', 'data' => null);
				return json_encode($response);
			}
		}
	}
?>
