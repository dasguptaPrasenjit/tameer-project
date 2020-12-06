<?php
namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Entities\Category;
use App\Http\Entities\CategoryDetails;
use App\Http\Entities\Product;
use App\Http\Entities\Menu;
use Validator;

class CategoryController extends Controller 
{
	public $successStatus = 200;
	public $internalErrorStatus = 500;
	
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getParentCategories()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$categories = new Category();
			if(sizeof($categories->getParentCategory())>=1){
				$data = $categories->getParentCategory();
				$code = $this->successStatus;
				$message = 'Success';
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    }
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getAllCategories()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$categories = new Category();
			if(sizeof($categories->getAllCategory())>=1){
				$data = $categories->getAllCategory();
				$code = $this->successStatus;
				$message = 'Success';
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    }
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getCategories()
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$categories = new Category();
			$parent_categories = $categories->getParentCategory();
			$cat_arr = array();
			if(sizeof($parent_categories)>=1){
				$i = 0;
				foreach($parent_categories as $parent){
					$cat_arr[$i] = array(
											"categoryname" => $parent->name,
											"id" => $parent->category_id,
											"status"=> $parent->status,
											"category_image"=> $parent->category_image,
											"description"=> $parent->description,
											"slug"=> $parent->slug
										);
					
					$subcategories = $categories->getCategoryById($parent->category_id);
					if(sizeof($subcategories)>=1){
						$sub_cat_arr = array();
						foreach($subcategories as $child){
							$sub_cat_arr[] = array(
												"subcategoryname" => $child->name,
												"id" => $child->category_id,
												"status"=> $child->status,
												"category_image"=> $child->category_image,
												"description"=> $child->description,
												"slug"=> $child->slug
											 );
						}
						$cat_arr[$i]["subcategory"] = $sub_cat_arr;
					}
					
					$i++;
				}
				
				$code = $this->successStatus;
				$message = 'Success';
				
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'data' => $cat_arr, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
    }
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function getCategoryById(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		try{
			$request_arr = $request->all();
			$category_id = $request_arr['categoryid'];
			$categories = new Category();
			
			if(sizeof($categories->getCategoryById($category_id))>=1){
				$data = $categories->getCategoryById($category_id);
				$result = array();
				/*foreach($data as $res){
					if($res->parent_id != null){
						$result['category'] = (object) $res;
					}else{
						$result['parent'] = (object) $res;
					}
				}*/
				$code = $this->successStatus;
				$message = 'Success';
			}else{
				$code = $this->successStatus;
				$message = 'No records founds';
			}
			
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							);
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function addCategories(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		$category_details = new CategoryDetails();
		$category_details_table = $category_details->getTable();
		try{			
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|unique:'.$category_details_table,
			]);
			
			if ($validator->fails()) {
				$errors = $validator->errors();
				$name_messages = $errors->get('name');
				if (!empty($name_messages)) {
					$response = array(
									'error' => true,
									'message' => $name_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			
			$name = $request_arr['name'];
			$parent_id = isset($request_arr['parentid']) ? $request_arr['parentid'] : null;
			$slug = isset($request_arr['slug']) ? $request_arr['slug'] : null;
			$description = isset($request_arr['description']) ? $request_arr['description'] : null;
			$meta_title = isset($request_arr['meta_title']) ? $request_arr['meta_title'] : null;
			$meta_description = isset($request_arr['meta_description']) ? $request_arr['meta_description'] : null;
			$meta_keywords = isset($request_arr['meta_keywords']) ? $request_arr['meta_keywords'] : null;
			$category_image = isset($request_arr['category_image']) ? $request_arr['category_image'] : null;

			$categories = new Category();			
			$categories->parent_id = $parent_id;
			$categories->save();
			$category_id = $categories->id;
			if($category_id){
				$category_details->name = $name;
				$category_details->category_id = $category_id;
				$category_details->slug = $slug;
				$category_details->description = $description;
				$category_details->meta_title = $meta_title;
				$category_details->meta_description = $meta_description;
				$category_details->meta_keywords = $meta_keywords;
				$category_details->category_image = $category_image;
				
				if($category_details->save()){	
					if($parent_id != null){
						$cat_details_parent = new Category();
						$parent_details = $cat_details_parent->getCategory($parent_id);
						
						$menuObj = new Menu();
						$menuObj->parent_category = $parent_details[0]->name;
						$menuObj->sub_category = $name;
						$menuObj->parent_cat_id = $parent_id;
						$menuObj->category_id = $category_id;
						$menuObj->category_image = $category_image;
						
						$menuObj->save();
					}
					$code = $this->successStatus;
					$message = 'Success';
					$category_obj= new Category();
					$data = $category_obj->getCategory($category_id);					
					$response = array(
										'data' => $data, 
										'message' => $message,
										'status' => $code
									 );
				}
			}else{
				$response = array(
									'error' => true,
									'message' => "Can not insert Category.",
									'status' => 401
								 );
				return response()->json($response);
			}
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
	}
	
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function editCategories(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		$category_details = new CategoryDetails();
		$category_details_table = $category_details->getTable();
		try{
			$request_arr = $request->all();
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|unique:'.$category_details_table,
			]);
			
			if ($validator->fails()) {
				$errors = $validator->errors();
				$name_messages = $errors->get('name');
				if (!empty($name_messages)) {
					$response = array(
									'error' => true,
									'message' => $name_messages[0],
									'status' => 401
								 );
					return response()->json($response);
				}
			}
			
			$name = $request_arr['name'];
			$id = $request_arr['id'];
			$parent_id = isset($request_arr['parentid']) ? $request_arr['parentid'] : null;
			$slug = isset($request_arr['slug']) ? $request_arr['slug'] : null;
			$description = isset($request_arr['description']) ? $request_arr['description'] : null;
			$meta_title = isset($request_arr['meta_title']) ? $request_arr['meta_title'] : null;
			$meta_description = isset($request_arr['meta_description']) ? $request_arr['meta_description'] : null;
			$meta_keywords = isset($request_arr['meta_keywords']) ? $request_arr['meta_keywords'] : null;
			$category_image = isset($request_arr['category_image']) ? $request_arr['category_image'] : null;

			$category_details
            ->where('category_id', $id)
            ->update([
					  'name' => $name,
					  'category_image' => $category_image,
					  'slug' => $slug,
					  'description' => $description,
					  'meta_title' => $meta_title,
					  'meta_keywords' => $meta_keywords,
					  'meta_description' => $meta_description,
					 ]);
					 
			$code = $this->successStatus;
			$message = 'Category updated successfully.';
			$category_obj= new Category();
			$data = $category_obj->getCategory($id);					
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							 );
			
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
		
	}
	
	/**
    * Display a listing of the resource.
    * @return Response
    */
    public function deleteCategories(Request $request)
    {
		$data = array();
		$code = '';
		$message = '';
		$category_obj = new Category();
		$product_obj = new Product();
		try{
			$request_arr = $request->all();			
			$id = $request_arr['id'];
			
			$activeSubCategories = $category_obj->getActiveCategoryById($id);
			$sub_cat_association_count = count($activeSubCategories);
			if($sub_cat_association_count == 0){
				$products = $product_obj->getActiveProductByCatId($id);
				$product_association_count = count($products);
				if($product_association_count == 0){
					$category_obj
					->where('id', $id)
					->update([
							  'status' => 0
							 ]);
							 
					$code = $this->successStatus;
					$message = 'Category deleted successfully.';
				}else{
					$code = 401;
					$message = 'Can not delete Category. Product already associated with it.';
				}
			}else{
				$code = 401;
				$message = 'Can not delete Category. Active sub categories assciated with it.';
			}				
			
			$data = $category_obj->getCategory($id);					
			$response = array(
								'data' => $data, 
								'message' => $message,
								'status' => $code
							 );
			
		}catch (Exception $e) {
            $response = array(
								'message'=>'Some internal error. Try after sometime.', 
								'status' => $internalErrorStatus
							 ); 
        }
		
		return response()->json($response,200);
		
	}
}