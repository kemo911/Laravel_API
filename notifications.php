<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\adminNotification;
use App\User;
use App\userGift;
use App\Notification;
use Carbon\Carbon;

class notifications extends Controller
{
  /**  
* API notifications 
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
*
* @author ಠ_ಠ Abdelrahman Mohamed 
*/
  public function notifications(Request $request ){
  try{
    $messages = [
        'page.required' => 'requiredPage',
        'page.numeric' => 'noPageFound',
        'page.min' => 'noPageFound',
        'apiToken.required' => 'requiredapiToken',
        'apiToken.exists' => 'noApiTokenFound',
    ];
    $rule=[
    'apiToken' => 'required|exists:users,apitoken',
    'page' => 'required|numeric|min:0',
    ];

  $Validator = \Validator::make($request->all(),$rule,$messages);

 if($Validator->fails()){
   foreach ($Validator->errors()->all() as  $error) {
        if($error =='requiredPage' || $error =='requiredapiToken' ){
              return response()->json(['status'=>'403']);
                }
        elseif($error == 'noApiTokenFound'){
              return response()->json(['status'=>'400']);
            }
            elseif($error == 'noPageFound'){
                  return response()->json(['status'=>'407']);
                }else{
                  break;
                }}}
  
   $user=User::where('apitoken',$request->apiToken)->select('id','name','is_active','is_verified')->first();
  if( $user->is_active == 0 ){
  return response()->json(['status'=>'405']);
}elseif ( $user->is_verified == 0 ) {
return response()->json(['status'=>'406']);
}else{
    $currentPage = $request->page+1; 
    Paginator::currentPageResolver(function () use ($currentPage) {
        return $currentPage;
    });

     

      $Notification=Notification::where('users_id',$user->id)->with(['users_gift'])->paginate(20);
  		$collection = collect();
	foreach ($Notification as $Notifications ){
    		if($Notifications->admin_notifications_id == null || empty($Notifications->admin_notifications_id) ){

           $type=0;
           $s=[
            'name'=>$Notifications->user->name,
            'dateTime'=>Carbon::parse($Notifications->users_gift->datetime)->timestamp,
            'type'=>$type,
            'giftPhoto'=>$Notifications->users_gift->gift->img_url,
                   ];
   			$collection->push($s); 
            }
        		elseif(!$Notifications->admin_notifications_id == null  ){
           $type=1;
    	$admin=adminNotification::where('id',$Notifications->admin_notifications_id)->select('content')->first();
           $s=[
            'dateTime'=>Carbon::parse($Notifications->users_gift->datetime)->timestamp,
            'type'=>$type,
           'content'=>$admin->content,
                   ];
   			$collection->push($s); 
                }
            }	
     if(!$collection->isEmpty()){
         $sorted  = $collection->sortByDesc('dateTime');
             return response()->json(['status'=>'200','message'=>$sorted->values()->all()]);
  }else{
  return response()->json(['status'=>'300']);
  }
  }}catch(Extention $e){
  return response()->json(['status'=>'404']);
  }
}
}
