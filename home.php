<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\User;
use App\Gift;
use App\userGift;
use Exception;



class home extends Controller
{
    public function home (Request $request)

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
             else{

      	     $user_id=$user->id;
      	     $ads = DB::table('ads')
             ->select('ads.*')
      	     ->whereDate('expire_date', '>=', date('Y-m-d'))
             ->where('is_active',1)->first();


             //get the sum of monthly points of the user 
             $points=DB::table('actions')
      		->join('users_actions','users_actions.actions_id','=','actions.id')
      		->where('users_actions.users_id',$user_id)
      		->whereMonth('users_actions.created_at',date('m'))
      		->select('actions.points')->sum('points');
      		
      	
             //get the sum of available gifts this month
             
            $gift_quantity=DB::select('SELECT count(users_gifts.gifts_id) as users_quantity from users_gifts
            join gifts on
             users_gifts.gifts_id=gifts.id 
             where gifts.type ="monthly"
             and  gifts.is_active=1
             and MONTH(users_gifts.datetime) = MONTH(CURRENT_DATE)
             ');
           $giftsCount_quantity=DB::select('SELECT sum(quantity) as giftquan from gifts
            where gifts.is_active=1 and gifts.type="monthly"
            ');
         $availble_gifts=$giftsCount_quantity[0]->giftquan-$gift_quantity[0]->users_quantity;
      		 

             //display random ad 
      		if (!$ads)
      		{
      			 return  response()->json(["status"=>200 ,"gifts"=>$availble_gifts,"points"=>$points]);
      		}
      		else
      		{
             $ad= DB::table('ads')
      		->whereDate('expire_date', '>=', date('Y-m-d'))
      		->where('is_active',1)
      		->select('img_url')->inRandomOrder()->first()->img_url;
            return  response()->json(["status"=>200 ,"gifts"=>$availble_gifts,"points"=>$points,"ad"=>$ad]);
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