<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Exception; 
use App\User;

class updatePassword extends Controller
{
/**  
* updatePassword
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param Request Illuminate\Http\Request
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
    public function updatePassword(Request $request)
   {
   	 try{
    $messages = [
        'apiToken.required'    => 'requiredapiToken',
        'apiToken.exists' 	   => 'noApiTokenFound',
        'oldPassword.required' =>'requiredoldPassword',
        'oldPassword.min'		   =>'minoldPassword',
        'newPassword.required' =>'requirednewPassword',
        'newPassword.min'		   =>'minNewPassword',
    ];

    $rule=[
    'apiToken' => 'required|exists:users,apitoken',
    'newPassword'=>'required|min:3',
    'oldPassword'=>'required|min:3'
];
  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
      foreach ($Validator->errors()->all() as  $error) {
	              if(
              		$error == 'requiredapiToken' 	||
              		$error =='requiredoldPassword'  ||
              		$error =='requirednewPassword' ){
		                return response()->json(['status'=>'403']);
	                 }elseif($error == 'noApiTokenFound'){
	                         return response()->json(['status'=>'400']);
                         }elseif($error == 'minNewPassword'){
	                           return response()->json(['status'=>'407']);
                              }else{
                              	break;}}}
$user=User::where('apitoken',$request->apiToken)->select('apitoken','password','is_active','is_verified')->first();

if($user->is_active == 0 ){
  return response()->json(['status'=>'405']);
}elseif ($user->is_verified == 0 ) {
return response()->json(['status'=>'406']);
}elseif (! Hash::check($request->oldPassword,$user->password))
{
  return response()->json(['status'=>'401']);
}else{
  
  $user=User::where('apitoken',$request->apiToken)->select('password')->update(['password' => bcrypt($request->newPassword)]);
  return response()->json(['status'=>'200']);
}

}catch(Exception $e){
 return response()->json(['status'=>'404']);
 }
}
}