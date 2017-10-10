<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\User;
use App\userAction;
use App\Action;
  

class finishPromotions extends Controller
{
	public function finishPromotions(Request $request){

			try{
		$validate=Validator::make($request->all(), [
						'apiToken'=>'required|min:64',
						'companyId'=>'required|integer',
			      ]);
		if($validate->fails()){
			return response()->json(['status' =>403]);
		}
		$apiToken=$request->apiToken;
		$companyId=$request->companyId;


		$company_chk=DB::select('SELECT COUNT(*) as count from companies where companies.id=?',[$companyId]);
			if($company_chk[0]->count ==0){
				return response()->json(['status' =>401]);
			}


		$company_promotions=DB::select('SELECT COUNT(*) as count from promotions WHERE promotions.companies_id=?',[$companyId]);
			if($company_promotions[0]->count ==0){
				return response()->json(['status' =>300]);
			}
		$user_chk=DB::select('SELECT COUNT(*) as count from users WHERE users.apitoken=?',[$apiToken]);
			if($user_chk[0]->count ==0){
				 return response()->json(['status'=>400]);
			}

			$user_vert=DB::select('SELECT is_verified as isvert,
									      is_active as isact
									      from users WHERE users.apitoken=?',[$apiToken]);	

			if($user_vert[0]->isvert == 0){
				return response()->json(['status'=>406]);
			}elseif ($user_vert[0]->isact ==0) {
				return response()->json(['status'=>405]);
			}


			$company_chk_data=DB::select('SELECT count(*) as count from companies
										 WHERE companies.is_active=1
										 AND companies.expire_date >= now()
										 AND companies.id=?',[$companyId]);
			if($company_chk_data[0]->count ==0){
				return response()->json(['status' =>407]);
			}
			$images=DB::select('SELECT promotions_images.img_url as url
								 from promotions
								 inner join promotions_images on
								 promotions.companies_id=promotions_images.promotions_id
								 WHERE promotions.companies_id=?',[$companyId]);
			
		


			$action=DB::select('SELECT id from actions where action="visit_promotion"');

			     if(count($action) ==0 )
								return response()->json(['status'=>404]);


			$user_id=DB::select('SELECT id from users where apitoken=?',[$apiToken]);
			$user_action=new userAction();
			$user_action->users_id=$user_id[0]->id;
			$user_action->actions_id=$action[0]->id;
			$user_action->created_at=Carbon::now();
			$user_action->save();
			$company_info=DB::select('SELECT *
									 from contact_info
									 WHERE contact_info.companies_id=?',[$companyId]);
			$company_social=DB::select('SELECT * from accounts where companies_id=?',[$companyId]);
				 return response()->json(['status'=>200,
				 						  'images'=> $images,
				 						  'email'=>$company_info[0]->email,
				 						  'phone' =>$company_info[0]->phone,
				 						  'social'=>$company_social]);

		}	
			catch(Exception $e) {
					return response()->json(['status' =>404]);
				}		
	}
}
