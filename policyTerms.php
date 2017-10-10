<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class policyTerms extends Controller
{
	public function policyTerms(Request $request)
	{
		try{
		 $validator =Validator::make($request->all(),[
		

       'lang'=>'required|en,ar',


    ]);

		 $lang =$request['lang'];
		      $arrayName = array ('ar', 'en');        

if (!$request->lang) {
      	return response()->json(['status'=>403]);
      }

   
               elseif($lang !== 'ar' && $lang!=='en')
               {
               	               	return response()->json(['status'=>405]);

               }
       else{
                     if($lang =='ar')
                     {
                     	$terms = DB::table('app_settings')->select('policy_terms_ar')->get();
						
						return response()->json(['status'=>200,'terms'=>$terms[0]->policy_terms_ar]);  
                     }
                     else{
                     	$terms = DB::table('app_settings')->select('policy_terms_en')->get();
						return response()->json(['status'=>200,'terms'=>$terms[0]->policy_terms_en]);  
                     }
                      
					         

                  }
     }
      
            
  catch(Exception $e) {
          return response()->json(['status' =>404]);
                        } 
	}
    
}
