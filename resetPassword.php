<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\response;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

class resetPassword extends Controller
{
	public function resetPassword(Request $request){

			try{

			$validate=Validator::make($request->all(), [
			        'phone' => 'required|min:9|regex:/^[0-9]+$/',
			        ]);

			if($validate->fails())
			           return response()->json(['status'=>403]);


			       $phone=$request->phone;

			 $chk_phone=DB::select('SELECT COUNT(*) as count from users WHERE users.phone=?',[$phone]);
			    if($chk_phone[0]->count ==0)
			        return response()->json(['status'=>400]);

			  $is_active=DB::select('SELECT count(*) as count from users WHERE users.is_active!=1 and users.phone=?',[$phone]);
			  		if($is_active[0]->count==1)
			  			return response()->json(['status'=>405]);


			  		$dt=Carbon::now();
			  		


			  		  session()->put($request->phone,['code'=>"123456",'time'=>$dt->addHour()->toTimeString()]);
			  			return response()->json(['status'=>200]);



			    }
			 catch(Exception $e) {
			      return response()->json(['status' =>404]);
			} 

	}
}
