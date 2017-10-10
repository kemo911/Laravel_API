<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
class questions extends Controller
{
	public function questions(Request $request){

			try{
		$validate=Validator::make($request->all(), [
						'apiToken'=>'required|min:64',
						'companyId'=>'required|integer',
						'lang'=>'required',

			      ]);
			if($validate->fails()){
			           return response()->json(['status'=>403]);
			  }
			$apiToken=$request->apiToken;
			$companyId=$request->companyId;
			$lang=$request->lang;
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
			$company_question=DB::select('SELECT count(*) as count from questions WHERE  questions.companies_id=?',[$companyId]);
				if($company_question[0]->count ==0){
					return response()->json(['status'=>300]);
				}
			$company_chk_data=DB::select('SELECT count(*) as count from companies
										 WHERE companies.is_active=1
										 AND companies.expire_date >= now()
										 AND companies.id=?',[$companyId]);
			if($company_chk_data[0]->count ==0){
				return response()->json(['status' =>407]);
			}	

			if($lang !='en')
				if($lang !='ar')
				return response()->json(['status' =>408]);
			$vidUrl=DB::select('SELECT video_url from  promotions where promotions.companies_id=?',[$companyId]);
			$question=DB::select('SELECT * from questions WHERE questions.companies_id=?',[$companyId]);
			$question_content="";
			$question_answers=array();
			if($lang=="en"){
			 $question_content=$question[0]->content_en;
			 $question_answers=DB::select('SELECT id ,content_en as content from answers WHERE questions_id=?',[$companyId]);
			}
			elseif($lang == "ar"){
			  $question_content=$question[0]->content_ar;
			  $question_answers=DB::select('SELECT id ,content_ar as content from answers WHERE questions_id=?',[$companyId]);
			}
			return response()->json(['status'=>200,
									'vidUrl'=>$vidUrl[0]->video_url,
									 'question'=>$question_content,
									 'answers'=>$question_answers]);
		}
			catch(Exception $e) {
					return response()->json(['status' =>404]);
				}			
	}
}
