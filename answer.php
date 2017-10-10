<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Action;
use App\userAction;
use App\userGift;
use App\Notification;
use App\Monthly_pulls;
use App\Setting;
use DB;
class answer extends Controller
{
	public function answer(Request $request){
		
		try{

		$validate=Validator::make($request->all(), [
						'apiToken'=>'required|min:64',
						'companyId'=>'required|integer',
						'ansId'=>'required|integer',
			      ]);

		if($validate->fails())
		           return response()->json(['status'=>403]);
		  

		  $apiToken=$request->apiToken;
		  $companyId=$request->companyId;
		  $ansId=$request->ansId;

		  $user_chk=DB::select('SELECT COUNT(*) as count from users WHERE users.apitoken=?',[$apiToken]);
		  	if($user_chk[0]->count ==0)
		  		 return response()->json(['status'=>400]);
		  	
		  	$user_vert=DB::select('SELECT is_verified as isvert,
		  								      is_active as isact
		  								      from users WHERE users.apitoken=?',[$apiToken]);	

		  	if($user_vert[0]->isvert == 0)
		  		return response()->json(['status'=>406]);
		  	elseif ($user_vert[0]->isact ==0)
		  		return response()->json(['status'=>405]);
		  	


		 $user_info=DB::select('SELECT id,email from users WHERE users.apitoken=?',[$apiToken]);
		 $user_id=$user_info[0]->id;
		 $user_email=$user_info[0]->email;


		  $company_chk=DB::select('SELECT COUNT(*) as count from companies where companies.id=?',[$companyId]);
		  	if($company_chk[0]->count ==0)
		  			return response()->json(['status' =>401]);
		  	


		 $company_chk_data=DB::select('SELECT count(*) as count from companies
		 							 WHERE companies.is_active=1
		 							 AND companies.expire_date >= now()
		 							 AND companies.id=?',[$companyId]);
		 if($company_chk_data[0]->count ==0)
		 	return response()->json(['status' =>407]);
		 

		 $answer_chk=DB::select('SELECT COUNT(*) as count from answers WHERE answers.id=?',[$ansId]);
		 	if($answer_chk[0]->count ==0)
		 		return response()->json(['status'=>402]);
		 	


		 $gifts_chk=DB::select('SELECT COUNT(*) as count from users_gifts 
								WHERE
									users_gifts.users_id=? 
									and users_gifts.is_delivered=1
									and MONTH(users_gifts.datetime) = MONTH(CURRENT_DATE())',[$user_id]);	
		
		 $users_actions=DB::select('SELECT count(users_actions.users_id) as count
		 							 from users_actions WHERE
		 							 users_actions.users_id=? 
		 					         and  WEEK(users_actions.created_at)=WEEK(?)',[$user_id,Carbon::now()]);
		

		 		if($gifts_chk[0]->count >0 || $users_actions[0]->count >0){
		 				$user_codes=DB::select('SELECT
		 									count(codes.is_used) as count
		 									from codes WHERE codes.users_id=? and codes.is_used=0',[$user_id]);
		 				if($user_codes[0]->count ==0){
		 					return response()->json(['status'=>300]);
		 				}else{
		 					$code=DB::select('SELECT codes.code from codes where codes.users_id=?',[$user_id]);
		 					DB::table('codes')
		 					    ->where('code',$code[0]->code)
		 					    ->update(['is_used' => 1,
		 					              'users_id'=>$user_id]);
		 				}
		 		}

		 		$correct_answer_chk=DB::select('SELECT COUNT(*) as count
		 										 from  correct_answer
		 										 WHERE correct_answer.questions_id=?
		 										  and correct_answer.answers_id=?',[$companyId,$ansId]);
		 		if($correct_answer_chk[0]->count ==0){
		 				$act=DB::select('SELECT id from actions where action="answer_question"');
		 				$user_act=new userAction();
		 				$user_act->users_id=$user_id;
		 				$user_act->actions_id=$act[0]->id;
		 				$user_act->created_at=Carbon::now();
		 				$user_act->save();
		 				return response()->json(['status'=>301]);
		 		}
		 		elseif ($correct_answer_chk[0]->count >=1) {
		 			
		 				
		 				$chk_part=DB::select('SELECT COUNT(*) as count
		 									   from users_actions
												INNER JOIN actions on 
	                    					    users_actions.actions_id=actions.id
						 					     WHERE
			 									 users_actions.users_id=?
		 							    	 	 and  WEEK(users_actions.created_at) = WEEK(?)
											     and actions.action="share_app"',	[$user_id,Carbon::now()]);
		 				
		 				if($chk_part[0]->count ==0){
		 					$dt = Carbon::now();
						
                        	$current_hour=$dt->hour;
                        		if($current_hour > 12)
                                		$current_hour=$current_hour-12;
                        
		 					$giftId =$companyId/$current_hour;
		 		$gift_quan=DB::select('SELECT  IFNULL(sum(gifts.quantity),0) as quan from gifts WHERE gifts.id=?',[$giftId]);
		 		$users_gift_quan=DB::select('SELECT COUNT(users_gifts.gifts_id) as count FROM users_gifts WHERE users_gifts.gifts_id=?',[$giftId]);
		 				
		 				$result=($gift_quan[0]->quan)-($users_gift_quan[0]->count);
		 								if($result >0){
		 							$user_won=new userGift();
		 							$user_won->users_id=$user_id;
		 							$user_won->gifts_id=$giftId;
		 							$user_won->datetime=Carbon::now();
		 							$user_won->save();
									
		 							$notification=new Notification();
		 							$notification->users_id=$user_id;
		 							$notification->users_gifts_id=$user_won->id;
		 							$notification->save();
		 							$gift_url=DB::select('SELECT name as name ,
		 														 img_url as url
		 												from gifts where id=?',[$giftId]);
									
		 							$gift_templete=Setting::all();
								
		 							Mail::send('email.send',
		 												["name" =>$gift_url[0]->name,
		 												 "image" =>$gift_url[0]->url,
		 												 "gift_template"=>$gift_templete[0]->gift_template],
		 										function($message)  use ($user_email){
		 							           $message->from('admin@test.com','Admin');
		 							           $message->to($user_email);
		 							           $message->subject('test email');
		 							     });
		 							return response()->json(['status'=>200,
		 													 'giftImgUrl'=>$gift_url[0]->url]);
		 							

		 						}else{
		 							$now = Carbon::now();
		 							$monthly_pulls_chk=DB::select('SELECT count(*) as count from monthly_pulls
		 												where
		 												month=? and year=? and users_id=?'
		 												,[$now->month, $now->year,$user_id]);
		 							if($monthly_pulls_chk[0]->count ==0 ){
		 							$monthly_pulls=new Monthly_pulls();
		 							$monthly_pulls->month=$now->month;
		 							$monthly_pulls->year= $now->year;
		 							$monthly_pulls->users_id=$user_id;
		 							$monthly_pulls->save();
		 							}

		 							$users_action=DB::select('SELECT id from actions where action="answer_question"');
		 							$us_action=new userAction();
		 							$us_action->users_id=$user_id;
		 							$us_action->actions_id=$users_action[0]->id;
		 							$us_action->created_at=Carbon::now();
		 							$us_action->save();
		 							return response()->json(['status'=>302]); 
		 						}


		 				}
		 			
		 		}
		 }
		 catch(Exception $e) {
		 		return response()->json(['status' =>404]);
		 }		


	}

}
