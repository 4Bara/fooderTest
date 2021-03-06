<?php
namespace App\Http\Controllers;
use Faker\Provider\DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

use DB;
use Illuminate\View\View;
use Symfony\Component\Console\Helper\Table;

/**
 * Created by PhpStorm.
 * User: Bara
 * Date: 12/3/2015
 * Time: 2:06 AM
 */

Class Backend extends controller {
        public static $aRestaurantsParams = array("restaurant_name","opening_days","location","username","logo","email","telephone","id_restaurant","rating","opening_days","price_range","id_country");

        public static function uploadPhotos($oImage,$sfile){
                $sDestination=  'images/photos/';
                //$sName =$oImage->getClientOriginalName();
                // dd($sDestination);
                $sDate=date('YmdHis');
                if(Request::file($sfile)->move($sDestination, $sDate.$oImage->getClientOriginalName())){
                        //Asset will give you the directory of public folders, that will be used in saving photos to it!
                        //  dd(asset($oImage->getClientOriginalName()));
                        return asset($sDestination.$sDate.$oImage->getClientOriginalName());
                }
                else {
                        return false;
                }
        }
        public static function checkifOpen($sJSON){
                $aJSON = json_decode($sJSON);
                $sDay = strtolower(Date("l",time()));
                $sTime= strtotime(Date("h:ma"));

               // dd(strtolower($sDay));

                if(isset($aJSON->$sDay)){

                        if(isset($aJSON->$sDay->from) && isset($aJSON->$sDay->to)){
                                $from = strtotime($aJSON->$sDay->from);
                                $to   = strtotime($aJSON->$sDay->to);
                                if(($sTime >=  $from) && ($sTime <= $to)){
                                        return true;
                                }else{
                                        return false;
                                }
                        }
                }
        }
        public static function getImage($sLocation,$dWidth,$dHeight,$sResizeMethod){
                if(empty($sLocation)){
                        return 'noimage.png';
                }else{
                        return $sLocation;
                }
        }
        public static function validateUser(){
                $aSession = \Session::all();
                if(isset($aSession['loggedin']) && $aSession['loggedin']==true){
                        return true;
                }else{
                        return false;
                }
        }
        public static function getAge($then) {
                $then = date('Ymd', strtotime($then));
                $diff = date('Ymd') - $then;
                return substr($diff, 0, -4);
        }
        public static function getDayName(){
               echo date("l",time());
        }
        /*
         * This function will be used everytime the user will open a view
         * it will populate a variable with the needed login data.
         *
         */
        public static function LoginData(){
                $aSession = \Session::all();
                $aData = array();

                $aData['items_count']=isset($aSession['cart']['items'])?count($aSession['cart']['items']):0;
                if(isset($aSession['loggedin']) && $aSession['loggedin']==true){
                        $aData['username']=$aSession['username'];
                        $aData['id_user']=$aSession['id_user'];
                        $aData['logged']='yes';
                        $aData['user_type']=$aSession['user_type'];
                }else{
                        //Not logged in logic
                        $aData['logged']='no';
                }
                return $aData;
        }
        /*
         * This function will add to the activites table with the needed data
         */
        public static function  addActivity($dIdUser,$sType,$dIdOther){
                DB::table("activities")->insert(array(
                    "id_user"=>$dIdUser,
                    "type"=>$sType,
                    "id_other"=>$dIdOther,
                    "date_inserted"=>date('Y-m:d h:i:s'))
                );
        }
        /*
   * This function was taken from PHP Documentation website
   */
        public static function pluralize( $count, $text )
        {
                return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
        }

        public static function ago( $datetime )
        {
                $interval = date_create('now')->diff( $datetime );
                $suffix = ( $interval->invert ? ' ago' : '' );
                if ( $v = $interval->y >= 1 ) return Backend::pluralize( $interval->y, 'year' ) . $suffix;
                if ( $v = $interval->m >= 1 ) return Backend::pluralize( $interval->m, 'month' ) . $suffix;
                if ( $v = $interval->d >= 1 ) return Backend::pluralize( $interval->d, 'day' ) . $suffix;
                if ( $v = $interval->h >= 1 ) return Backend::pluralize( $interval->h, 'hour' ) . $suffix;
                if ( $v = $interval->i >= 1 ) return Backend::pluralize( $interval->i, 'minute' ) . $suffix;
                return Backend::pluralize( $interval->s, 'second' ) . $suffix;
        }

}