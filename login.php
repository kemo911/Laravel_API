<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\User;

class login extends Controller
{
   /**  
* API Login
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
   public function login(Request $request)
   {
   	  	try{
    $messages = [
    	'phone.required'=>'phoneRequired',
    	'phone.numeric' =>'notvalidPhone',
    	'phone.min'=>'notvalidPhone',
    	'phone.exists'=>'notvalidPhone',
    	'password.required'=>'passwordRequired',
    	'password.min'=>'notvalidpassword',
    ];

    $rule=[
    'phone' => 'required|numeric|min:9|exists:users,phone',
    'password' => 'required|min:3',
];

  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
   foreach ($Validator->errors()->all() as  $error) {
   	if($error == 'phoneRequired' || $error == 'passwordRequired' ){
   		return response()->json(['status'=>'403']);
   	}elseif($error == 'notvalidPhone' || $error == 'notvalidpassword'){
   		return response()->json(['status'=>'401']);
   	}else{
   		break;
   	}
}
}
$user=User::where('phone',$request->phone)->select('is_active','is_verified','name','apitoken','email','country','city','town','password')->first();
if(! Hash::check($request->password,$user->password)){
	return response()->json(['status'=>'401']);
}
elseif($user->is_verified == 0){
	return response()->json(['status'=>'300']);
}elseif($user->is_active == 0){
	return response()->json(['status'=>'405']);
}else{
$user=User::where('phone',$request->phone)->select('name','apitoken','email','country','city','town')->first();
return response()->json(['status'=>'200','user'=>$user]);
}
}catch(Exception $e){
	return response()->json(['status'=>'400']);
}}}

// namespace App\Http\Controllers\API;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Http\response;
// use App\User;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;
// use Session;
// use Carbon\Carbon;

// class login extends Controller
// {

// 	    public function login(Request $request)
//         {


//           try {

//          $validator=Validator::make($request->all(),[
//         'phone'=>'required|min:9|numeric|unique:users',
//         'password'=>'required|min:3'
//         ]);



//         $phone =$request['phone'];
//         $password =$request['password'];
        
//         $code= session()->put('code', ['code'=>str_random(6),'time'=>]);
//         $token_time_out=strtotime('+1 hour');


//          $user =User::where('phone', $phone)->first();


        
//         if(!$request->password || !$request->phone)
//         {
//             return response()->json(['status'=>403]);
//         }

//         elseif (strlen($phone)<9 || !is_numeric($phone)||strlen($password)<3)
//          {         return response()->json(['status'=>401]);

//          }
       
//        elseif($user->is_verified==0){

//         return response()->json(['status'=>300,"code"=>\Session::get('code')]);
//        }

//       elseif ($user->is_active==0) {
//           return response()->json(['status'=>405]);
//       }

      
             
           

//               elseif($user->is_active == 1 && $user->is_verified == 1 && $token_time_out < time()  )
//                {

//                      return response()->json(['status'=>200,
                                              
//                                                 'name'=>$user->name,
//                                                 'email'=>$user->email,
//                                                 'country'=>$user->country,
//                                                 'city'=>$user->city,
//                                                 'town'=>$user->town,
//                                                 'apiToken'=>$user->api_token]);
//                 }

        
//        }
//        catch(Exception $e){
//           return response()->json(['status' =>404]);
//        }   

            
// }
// }

