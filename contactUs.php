<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\User;
use Exception;

class contactUs extends Controller
{
	public function contactUs(Request $request)
	{

	 try{

   $apiToken = $request['apiToken']; 
      
      if(!$apiToken)
      {
        return response()->json(["status"=>403]);
      }
     
      else 
     {  
      	$user= User::where('apitoken',$apiToken)->first();
      	if (!$user) 
      	{
      		return response()->json(["status"=>400]);
      	}
      	else

      	{ 
               if($user->is_active == 0)
             {
              return response()->json(["status"=>405]);
             }
             elseif ($user->is_verified == 0) {

              return response()->json(["status"=>406]);
             }
             else
             { 
                $contact = DB::table('app_settings')->select('app_settings.*')->first();
                $contact_phone = $contact->contact_phone;
                $contact_email = $contact->contact_email;
                $complain_phone= $contact->complain_phone;
                $complain_email= $contact->complain_email;
                 return response()->json(["status"=>200, "contactPhone"=>$contact_phone, "contactMail"=>
                 $contact_email,"complainsPhone"=>$complain_phone,"complainsMail"=> $complain_email]);
             
             }

         }
     }
     }
     catch(\Exception $e)
     {
          return response()->json(['status' =>404]);
     }
 }
}

