<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Entities\Menu;

class MenuController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllMenu()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$menu_obj = new Menu();
			$menu_arr =  $menu_obj->findAllMenu();
			$temp_parent_id = '';
			
			$cat_arr = array();
			$i = 0;
			foreach($menu_arr as $key=>$value){
				if($temp_parent_id != $value->parent_cat_id){					
					$sub_cat_arr = array();
					$cat_arr[$i] = array(
										"categoryname" => $value->parent_category,
										"id" => $value->parent_cat_id
									);
					$sub_cat_arr[] = array(
										"subcategoryname" => $value->sub_category,
										"id" => $value->category_id
									);
					$cat_arr[$i]["subcategory"] = $sub_cat_arr;
					$temp_parent_id = $value->parent_cat_id;
					$i++;
				}else{
					$sub_cat_arr[] = array(
										"subcategoryname" => $value->sub_category,
										"id" => $value->category_id
									);
					$cat_arr[$i-1]["subcategory"] = $sub_cat_arr;
				}
			}
			$response = array(
								'data' => $cat_arr, 
								'message' => $message,
								'code' => $this->successStatus
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'code' => $this->internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
}