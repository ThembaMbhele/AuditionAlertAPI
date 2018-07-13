<?php
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	
	class AuditionController extends Controller
	{
		public function deleteAudition(Request $request)
		{
			$auditionToDelete = $request->json()->all();
			
			DB::table('Auditions')->where('auditionId', '=', $auditionToDelete['auditionId'])->delete();
			return json_encode(true);
		}
		
		public function uploadAuditionEvent(Request $request)
		{
			$audition = $request->json()->all();
			
			//return $audition;
			
			DB::insert('insert into Auditions ( auditionName, auditionDate, auditionDescription, userId, auditionUrl , auditionImage) 
					values (?, ?, ?, ?, ?, ?)', [ $audition['auditionName'], $audition['auditionDate'], $audition['auditionDescription'], $audition['userId'], $audition['auditionUrl'], $audition['auditionImage'] ]);
								    
		        $response = array('result' => true, 'errorMessage' => null, 'data' => null);
			return json_encode($response);
		}
		
		public function uploadImage(Request $request)
		{
			$file = $request->get('file', []);
			
			//$file->storeAs('photos', $file->getClientOriginalName());
			//return json_encode(true);
			
			list($mime, $data)   = explode(';', $file);
                        list(, $data)       = explode(',', $data);
                        $data = base64_decode($data);

                        $mime = explode(':',$mime)[1];
                        $ext = explode('/',$mime)[1];
                        $name = mt_rand().time();
                        $savePath = 'uploads/images/'.$name.'.'.$ext;

                        file_put_contents(public_path().'/'.$name, $data);
			
			return json_encode($name);
		}
	}
?> 