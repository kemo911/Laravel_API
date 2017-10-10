<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use App\User;

class promotions extends Controller
{
		public function promotions(Request $request){
			try {
				$validate=Validator::make($request->all(), [
								'apiToken'=>'required|min:64',
								'companyId'=>'required|integer',
					      ]);
					if($validate->fails()){
					           return response()->json(['status'=>403]);
					  }




				$apiToken=$request->apiToken;
				$companyId=$request->companyId;
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



				$company_chk=DB::select('SELECT COUNT(*) as count from companies where companies.id=?',[$companyId]);
					if($company_chk[0]->count ==0){
						return response()->json(['status' =>401]);
					}
				$company_chk_data=DB::select('SELECT count(*) as count from companies
											 WHERE companies.is_active=1
											 AND companies.expire_date >= now()
											 AND companies.id=?',[$companyId]);
				if($company_chk_data[0]->count ==0){
					return response()->json(['status' =>407]);
				}
				$company_promotions=DB::select('SELECT COUNT(*) as count from promotions WHERE promotions.companies_id=?',[$companyId]);
				if($company_promotions[0]->count ==0){
					return response()->json(['status' =>300]);
				}
				$vidUrl=DB::select('SELECT
						 promotions.video_url as url 
						 FROM promotions WHERE promotions.companies_id=?',[$companyId]);

				return response()->json(['status' =>200,'vidUrl'=>$vidUrl[0]->url]);			
		}		
		catch(Exception $e) {
				return response()->json(['status' =>404]);
			}							 									
	}  
}
