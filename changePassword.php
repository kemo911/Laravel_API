<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\User;
use Exception;

class changePassword extends Controller
{ 
    public function changePassword(Request $request)
    {
       try{
      $validator = validator::make($request->all(),[
       'password'=>'required|min:3',
       'tempApiToken' =>'required|unique'
    ]);

           //parameters checking 
	    if(!$request->password || !$request->tempApiToken)
		   {
		     return response()->json(["status"=>403]);
		   }
         //password validation 
	    elseif (strlen($request->password)<3)
	       {
	         return response()->json(["status"=>405]);
	       }
	    else
	       {
	          //check if the acoount is registered with the tempApiToken
	          $user= User::where('temp_apitoken',$request->tempApiToken)->first();
	          if(!$user)
	          {
	              return response()->json(["status"=>400]);
	           }
	           //success
	          else 
	           {
			      $user->password=bcrypt($request->password);
			      $user->save();
			       return response()->json(["status"=>200]);
	           }
             }
         }
         catch(\Exception $e)
          {
          return response()->json(['status' =>404]);
           }
    }
}
