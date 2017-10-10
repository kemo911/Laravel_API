<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\User;
use Exception;
class points extends Controller
{
  
    public function points(Request $request)
    {
    	try {
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
            //user's past month points
	      	 $points=DB::table('actions')
      		->join('users_actions','users_actions.actions_id','=','actions.id')
      		->where('users_actions.users_id',$user_id)
      		->whereMonth('users_actions.created_at',date('m'))
      		->select('actions.points')->sum('points');
           //user's past month share points 
      	    $sharePionts = DB::table('actions')
      		->join('users_actions','users_actions.actions_id','=','actions.id')
      		->where('users_actions.users_id',$user_id)
      		->whereMonth('users_actions.created_at',date('m'))
      		->where('actions.action',"share_app")
      		->sum('points');
      	     //user's past month visit points 
      	    $visitPoints = DB::table('actions')
      		->join('users_actions','users_actions.actions_id','=','actions.id')
      		->where('users_actions.users_id',$user_id)
      		->whereMonth('users_actions.created_at',date('m'))
      		->where('actions.action',"visit_promotion")
      		->sum('points');
             //user's past month wrong answer points 
      		$wrongPoints =  DB::table('actions')
      		->join('users_actions','users_actions.actions_id','=','actions.id')
      		->where('users_actions.users_id',$user_id)
      		->whereMonth('users_actions.created_at',date('m'))
      		->where('actions.action',"answer_question")
      		->sum('points');

          //getting the list of top users
          $winners = DB::select('SELECT 
			    users.name as name,
			    users.id as id,
			    sum(actions.points) as points
			      
			    from users

			    INNER join users_actions on 
			    users_actions.users_id=users.id

			    INNER join actions on 
			    users_actions.actions_id=actions.id
              
			    Where MONTH(users_actions.created_at)= MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
			    GROUP BY users.id
			    ORDER BY points DESC LIMIT 10');

			   if(!$winners)
			   {
			      return response()->json(["status"=>404]);
			   }
            else{
              foreach ($winners as $winner) 
              {
              	$_winner['name']=$winner->name;
              	$_winner['points']=$winner->points;
              	$_winner["giftUrl"] = DB::select('SELECT gifts.img_url as gift_url
                from gifts 
      		    join users_gifts on 
			         users_gifts.users_id=?
			    
                Where users_gifts.gifts_id=gifts.id
                and MONTH(users_gifts.datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)',[$winner->id]);
                $users[]=$_winner;
              }
               return response()->json(["status"=>200,"points"=>$points,"sharePionts"=>$sharePionts,"visitPoints"=>$visitPoints,"wrongPoints"=>$wrongPoints,"users"=>$users]);
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
