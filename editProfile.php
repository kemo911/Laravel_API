<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\User;
use Exception; 

class editProfile extends Controller
{
/**  
* EditProfile
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param Request Illuminate\Http\Request
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/

   public function editProfile(Request $request)
   {
   	 try{
    $messages = [
        'email.required'    => 'requiredemail',
        'name.required'     => 'requiredname',
        'country.required'  => 'requiredcountry',
        'city.required'     => 'requiredcity',
        'town.required'     => 'requiredtown',
        'apiToken.required' => 'requiredapiToken',
        'email.email'       =>'emailNotValid',
        'email.min'         =>'emailNotValid',
        'email.unique'      =>'emailIsExists',
        'apiToken.exists'   => 'noApiTokenFound',

    ];
    $rule=[
    'apiToken' => 'required|exists:users,apitoken',
    'email'=>[
        'required',
        'email',
         'min:5',
       Rule::unique('users')->ignore($request->apiToken, 'apitoken'),

    ],
    'name'=>'required',
    'country'=>'required',
    'city'=>'required',
    'town'=>'required',
];
  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
foreach ($Validator->errors()->all() as  $error) {
		if(
		$error == 'requiredemail'    ||
	  $error == 'requiredapiToken' || 
		$error == 'requiredname' 	   ||
		$error == 'requiredcountry'  ||
		$error == 'requiredtown' 	   ||
		$error == 'requiredcity' 	 
		)
	{
       
return response()->json(['status'=>'403']);
	}
	elseif($error == "noApiTokenFound"){
		return response()->json(['status'=>'400']);
	}
	elseif ($error == "emailIsExists") {
		return response()->json(['status'=>'401']);
	}

	elseif ($error =='emailNotValid') {
		return response()->json(['status'=>'407']);
	}
	else{
		break;
	}
 }
}
  $user=User::where('apitoken',$request->apiToken)->first();
if($user->is_active == 0 ){
  return response()->json(['status'=>'405']);
}elseif ($user->is_verified == 0 ) {
return response()->json(['status'=>'406']);
}else{


  $user->name=$request->name;
  $user->email=$request->email;
  $user->country=$request->country;
  $user->city=$request->city;
  $user->town=$request->town;
  
 if($user->save()){
return response()->json(['status'=>'200']);
}}

}catch(Exception $e) {
  return response()->json(['status'=>'404']);
  }
}
}