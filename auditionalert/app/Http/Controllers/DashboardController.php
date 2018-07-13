<?php

	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	
	class DashboardController extends Controller
	{
		public function getAuditionEvents()
		{
			$auditions = DB::table('Auditions')->where('auditionImage', '!=', null)
									    //->where('auditionDate', '!=', null)
									    ->where('auditionDate', '>=', date('Y/m/d'))
									    ->get();
			
			return json_encode($auditions);
		}
	}
?>