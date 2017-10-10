<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Exception;
use App\User;
use Session;

class register extends Controller
{
    /**  
* API register
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
   public function register(Request $request)
   {
   		// try{
    $messages = [
    'name.required'=>'nameRequired',
    'email.required'=>'emailRequired',
    'password.required'=>'passwordRequired',
    'city.required'=>'cityRequired',
    'country.required'=>'countryRequired',
    'town.required'=>'townRequired',
    'phone.required'=>'phoneRequired',
    'reference.required'=>'referenceRequired',
    'email.email'=>'emailNotValide',
    'email.min'=>'emailNotValide',
    'password.min'=>'passwordNotvalid',
    'phone.numeric'=>'phoneNotValid',
    'phone.min'=>'phoneNotValid',
    'email.unique'=>'emailExists',
    'phone.unique'=>'phoneIsExists',
    'reference.in'=>'referenceIn',
    ];

    $rule=[
    'name'=>'required',
    'email'=>'required|email|min:5|unique:users,email',
    'password'=>'required|min:3',
    'country'=>'required',
    'city'=>'required',
    'town'=>'required',
    'phone' => 'required|numeric|min:9|unique:users,phone',
    'reference'=>[
        'required',
        Rule::in(['social_media', 'friend','our_accounts','other' ]),
    ],
];

  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
   foreach ($Validator->errors()->all() as  $error) {
   	if(
   		$error =='nameRequired' || $error=='emailRequired' || $error =='passwordRequired'||
   		$error =='cityRequired' || $error =='countryRequired' || $error =='townRequired' ||
   		$error =='referenceRequired' ||$error =='phoneRequired')
   	{
return response()->json(['status'=>'403']);
   	}elseif ($error == 'emailExists') {
   return response()->json(['status'=>'400']);
   	}elseif ($error == 'phoneIsExists') {
   	return response()->json(['status'=>'401']);	
   	}elseif ($error == 'phoneNotValid') {
   	return response()->json(['status'=>'405']);
   	}elseif ($error == 'passwordNotvalid') {
   	return response()->json(['status'=>'407']);
   	}elseif ($error =="emailNotValide") {
   	return response()->json(['status'=>'406']);
   	}elseif ($error =="referenceIn") {
   	return response()->json(['status'=>'408']);
   	}
   }	
}
    $user=new User;
   $user->name=$request->name;
   $user->email=$request->email;
   $user->password=Hash::make($request->password);
   $user->country=$request->country;
   $user->city=$request->city;
   $user->phone=$request->phone;
   $user->town=$request->town;
   $user->hear_us=$request->reference;
   $user->apitoken=str_random(64);
   
   if($user->save()){
  // $request->session()->put('phone',mt_rand(100000, 999999));
	  		$dt=Carbon::now();
			  		  session()->put($request->phone,['code'=>"123456",'time'=>$dt->addHour()->toTimeString()]);
   	return response()->json(['status'=>'200','apiToken'=>$user->apitoken]);
   }
        // }catch (Exception $e){
        // return response()->json(['status'=>'404']);
        // }
   }
}

// namespace App\Http\Controllers\API;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Http\response;
// use App\User;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;
// use Session;
// use Carbon\Carbon;

// class register extends Controller
// {
    
//    public function register(Request $request)
//    {

//    	try{
//       $validator=Validator::make($request->all(),[
//       	'name' => 'required',
//         'email' => 'required|email|min:5|unique:users',
//         'password'=>'required|min:3',
//         'country' => 'required',
//         'city' => 'required',
//         'town' => 'required',
//         'phone'=>'required|min:9|numeric|unique:users',
//        'reference'=>'required',
//         ]);
                    
//      $arrayName = array('reference' =>  ['friend', 'social_media','our_accounts','others']);        
//           $name=$request['name'];
//           $email=$request['email'];
//           $password=$request['password'];
//           $country=$request['country'];
//           $city=$request['city'];
//           $town=$request['town'];
//           $phone=$request['phone'];
//           $reference=$request['reference'];
//           $code= session()->put(['code'=>str_random(6)]);
//                 //  $token_time_out=strtotime('+1 hour');

//           $user_email =User::where('email',$email)->first();
//           $user_phone=User::where('phone',$phone)->first();
//           if (count($user_email)>0)
//                         {
//        	             return response()->json(['status'=>400]);

//                          }
//         elseif (count($user_phone)>0) 
//                           {
//        	             return response()->json(['status'=>401]);

//                          }
     
//          elseif(strlen($phone)<9 ||!is_numeric($phone) )
//                         {
        
//                           return response()->json(['status'=>405]);
//                        }  
//           elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))  
//                       {
//                           return response()->json(['status'=>406]);
//                       }
//            elseif(strlen($password)<3)

//                       {
//                           return response()->json(['status'=>407]);
//                        }
            
 
//          elseif($request->reference == $arrayName)
//                {
//                                return response()->json(['status'=>408]);

//                }
//                                   elseif ($validator->fails()) 
//                                   {
//                                     return response()->json(['status'=>403]);
//                                   }

//             else
//          { 
              
//           $user = new user();
//           $user->name=$request['name'];
//           $user->email=$request['email'];
//           $user->password=bcrypt($request['password']);
//           $user->country=$request['country'];
//           $user->city=$request['city'];
//           $user->town=$request['town'];
//           $user->phone=$request['phone'];
//           $user->created_at = Carbon::now();


//          $user->hear_us=$request['reference'];
//           $user->apitoken= str_random(64);
//          // $time=Carbon::createFromTime('H');
//        //  $dt = Carbon::now();
//       //echo $dt->toDateTimeString(); 
//      // echo $dt->addHour(); 
        

//            $user->save();
           
//             return response()->json(['status'=>200,'api_token'=>$user->apitoken]); 
//               $v_code= \Session::get('code');
//               return response()->json($v_code);        
          
//              }
//          }
     
//           catch(Exception $e) {
//           return response()->json(['status' =>404]);
//                         }   
//    }
// }




