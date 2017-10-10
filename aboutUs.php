<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Exception;
use App\Setting;
use App\User;


class aboutUs extends Controller
{
/**  
* API AboutUS 
* status message
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
   public function aboutUs(Request $request)
   {
   	  	try{
    $messages = [
        'lang.required' => 'requiredlang',
        'apiToken.required' => 'requiredapiToken',
        'apiToken.exists' => 'noApiTokenFound',
    ];

    $rule=[
    'apiToken' => 'required|exists:users,apitoken',
    'lang' => [
        'required',
        Rule::in(['ar', 'en']),
    ],
];

  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
   foreach ($Validator->errors()->all() as  $error) {
        if($error =='requiredlang' || $error =='requiredapiToken' ){
              return response()->json(['status'=>'403']);
                }elseif($error == 'noApiTokenFound'){
              return response()->json(['status'=>'400']);
            }elseif($request->lang !="ar" || $request->lang !="en"){
                  return response()->json(['status'=>'407']);
                }else{
                	break;
                }}}

$api=User::where('apitoken',$request->apiToken)->select('is_active','is_verified')->first();

if($api->is_active == 0 ){

  return response()->json(['status'=>'405']);
}elseif ($api->is_verified == 0 ) {
return response()->json(['status'=>'406']);
}else{
      if ($request->lang === 'ar'){
     	$Setting=Setting::select('about_us_ar')->first();
     	return response()->json(['status'=>'200','message'=>$Setting->about_us_ar]);
     }else{
     	$Setting=Setting::select('about_us_en')->first();
     	return response()->json(['status'=>'200','message'=>$Setting->about_us_en]);
     }}
   }
catch(Exception $e) {
return response()->json(['status'=>'404']);
}

}
}
