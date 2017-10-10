<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception; 
use App\Company;
use App\User;
use Carbon\Carbon;

/*
  _____                                              
 / ____|                                             
| |      ___   _ __ ___   _ __    __ _  _ __   _   _ 
| |     / _ \ | '_ ` _ \ | '_ \  / _` || '_ \ | | | |
| |____| (_) || | | | | || |_) || (_| || | | || |_| |
 \_____|\___/ |_| |_| |_|| .__/  \__,_||_| |_| \__, |
                         | |                    __/ |
                         |_|                   |___/ 
*/
                         
class companies extends Controller
{
/**  
* companies
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param Request Illuminate\Http\Request
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
public function companies(Request $request)
   {
   	 try{
    $messages = [
        'apiToken.required'    => 'requiredapiToken',
        'apiToken.exists' 	   => 'noApiTokenFound',
    ];

    $rule=[
    'apiToken' => 'required|exists:users,apitoken',
];
  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
      foreach ($Validator->errors()->all() as  $error) {
      	if( $error =='requiredapiToken' ){
              return response()->json(['status'=>'403']);
                }elseif($error == 'noApiTokenFound'){
              return response()->json(['status'=>'400']);
            }}}
$user=User::where('apitoken',$request->apiToken)->first();

if($user->is_active == 0 ){
  return response()->json(['status'=>'405']);
}elseif ($user->is_verified == 0 ) {
return response()->json(['status'=>'406']);
}else{
	$data=Carbon::now();
	$companies=Company::select('id','name','img_url','is_gold')
	->where('expire_date','>=',$data)
	->where('is_active','=',1)
	->inRandomOrder()->take(20)->get();
	return response()->json(["status"=>"200","Ads"=>$companies]);
}


 }catch(Exception $e){
  return response()->json(['status'=>'404']);
}
}}
