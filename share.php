<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\userAction;
use App\User;
use Exception;

class share extends Controller
{
  
  public function share(Request $request){
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
                 $day = DB::table('users_actions')
                 ->join('actions','users_actions.actions_id','=','actions.id')
                 ->where('users_actions.users_id',$user->id)
                 ->where('actions.action','share_app')
                 ->where('users_actions.created_at',date('y-m-d'))
                 ->first();

                    if (!$day) 
                    {
                      
                     $action = new userAction();
                     $action->users_id = $user->id;
                    $action->actions_id = DB::table('actions')
                      ->where('actions.action','share_app')
                      ->select('id')->first()->id;
                      $action->created_at = date("Y-m-d")  ;
                      $action->save();

                      return response()->json(["status"=>200]);
 
                    }
                  else
                  {
                       return response()->json(["status"=>300]);
                  }
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
