<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use DB;

class code extends Controller
{
	public function code(Request $request){

		try {
		$validate=Validator::make($request->all(), [
						'apiToken'=>'required|min:64',
						'code'=>'required',
			      ]);
		if($validate->fails()){
		           return response()->json(['status'=>403]);
		  }
		$apiToken=$request->apiToken;
		$code=$request->code;
        
        
        $user_chk=DB::select('SELECT COUNT(*) as count from users WHERE users.apitoken=?',[$apiToken]);
		  	if($user_chk[0]->count ==0){
		  		 return response()->json(['status'=>400]);
		  	} 
        
		$user_info=DB::select('SELECT id from users WHERE users.apitoken=?',[$apiToken]);
		$user_id=$user_info[0]->id;


		$user_vert=DB::select('SELECT is_verified as isvert,
									      is_active as isact
									      from users WHERE users.apitoken=?',[$apiToken]);	

		if($user_vert[0]->isvert == 0){
			return response()->json(['status'=>406]);
		}elseif ($user_vert[0]->isact ==0) {
			return response()->json(['status'=>405]);
		}  	
		  $code_chk=DB::select('SELECT COUNT(*) as count from
									 codes
									 WHERE
										 codes.code=?
									 and codes.users_id IS NULL
									 and codes.is_used=0',[$code]);
		  if($code_chk[0]->count == 0){
		  	return response()->json(['status'=>401]);
		  }

        
        
        DB::table('codes')
            ->where('code',$code)
            ->update(['is_used' => 1,
                      'users_id'=>$user_id]);
        
		  	return response()->json(['status'=>200]);

	}
	
	catch(Exception $e) {
			return response()->json(['status' =>404]);
		}		  
 	}
}
