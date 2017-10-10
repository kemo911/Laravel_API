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


class validatePhone extends Controller
{
   public function validatePhone(Request $request){
	

      try{

      $validate=Validator::make($request->all(), [
              'phone' => 'required|min:9|regex:/^[0-9]+$/',
              'code' =>'required|min:6|',
              ]);
      if($validate->fails())
                 return response()->json(['status'=>403]);

        $phone=$request->phone;
        $code=$request->code;

	   	

        $chk_phone=DB::select('SELECT COUNT(*) as count from users WHERE users.phone=?',[$phone]);
            if($chk_phone[0]->count ==0)
                  return response()->json(['status'=>400]);

                $dt=Carbon::now();

                
      		 $v_code = $request->session()->get($request->phone);     
      				
				     
         	
          if(count($v_code) ==0)
              return response()->json(['status'=>401]);

			$code_session=$v_code["code"];
			$created_at_session=$v_code["time"];

            
       
           if($code !=$code_session)
                return response()->json(['status'=>401]);

           if($created_at_session <= $dt->toTimeString()){
           			 $request->session()->forget($request->phone);     
         			  return response()->json(['status'=>401]);
				 
           }

                $user_info=DB::select('SELECT id from users WHERE users.phone=?',[$phone]);
                $update_user=User::find($user_info[0]->id);
                $update_user->is_verified=1;
                $update_user->temp_apitoken=str_random(64);
                $update_user->save();
                 $request->session()->forget($request->phone);
                 return response()->json(['status'=>200,'tempApiToken'=>$update_user->temp_apitoken]);


      }
       catch(Exception $e) {
            return response()->json(['status' =>404]);
      } 

   }  

}