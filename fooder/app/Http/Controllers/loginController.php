<?php
/**
 * Created by PhpStorm.
 * User: Bara
 * Date: 11/11/2015
 * Time: 3:31 AM
 */
namespace App\Http\Controllers;
use App\Http\Requests\Request;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends controller
{
   public function login(){
      $aRequest = \Input::all();
      $sUsername = $aRequest['username'];
      $sPassword = $aRequest['password'];
       //Check if there's a user with this name, if yes, get all needed information for them
       $aResult = DB::table('users')->where(array('username'=>$sUsername))->get(array('*'));

       if(!empty($aResult)) {
           //If execution reached here then the user is normal user.
           $sUserType = 'user';
       }
       if(empty($aResult)){
           //Check if the user is a restaurant
           $aResult= DB::table('restaurants')->where(array('username'=>$sUsername))->get(array('*'));
           $sUserType = 'restaurant';
           \Session::put('loggedin','true');
           \Session::put('username',$sUsername);
           \Session::put('id_user',$aResult[0]->id_restaurant);
           \Session::put('user_type',$sUserType);
           //PUT THE SESSION FOR THE RESTAURANT
           return redirect()->intended('/');
       }
       if(empty($aResult)){
           return redirect('login')->withErrors('User not found!');
       }
        if(Hash::check($sPassword,$aResult[0]->password)){
            \Session::put('loggedin','true');
            \Session::put('username',$sUsername);
            \Session::put('id_user',$aResult[0]->id_user);
            \Session::put('user_type',$sUserType);
            return redirect()->intended('/');
        }else{
            return redirect('login')->withErrors(['Password is wrong!!','Error']);
        }
   }
}