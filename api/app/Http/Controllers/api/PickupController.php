<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Entities\Carrier;
use App\Http\Entities\Pickup;
use App\Http\Entities\UserRole;
use App\Http\Entities\Notifications;
use Datetime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Validator;
use App\Common\Utility;
use App\User;

class PickupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = null;
        $userRoleObj = new UserRole();
        $user_role_details = $userRoleObj->getUserRole(Auth::user()->id);
        if (sizeof($user_role_details) > 0) {
            $role = $user_role_details[0]->role_name;
        }
        if ($role === "admin") {
            $result = Pickup::with(['carrier', 'carrier.user'])->orderBy('id', 'DESC')->get();
        } elseif (Auth::user()->carrier_id) { //carrier
            $result = Pickup::with(['carrier', 'carrier.user'])->where('carrier_id', Auth::user()->carrier_id)->get();
        } else { // other user
            $result = Pickup::with(['carrier', 'carrier.user'])->where('created_by', Auth::user()->id)->get();
        }
        return $this->success($result, "Success", 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => ['required', Rule::in(['COD', 'ONLINE'])],
            'payer' => ['required', Rule::in(['SENDER', 'RECEIVER'])],
            'distance' => ['required'],
            'cost_per_km' => ['required'],
            'payable_amount' => ['required'],
            'sender_latitude' => ['required'],
            'sender_longitude' => ['required'],
            'receiver_latitude' => ['required'],
            'receiver_longitude' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $carrier = Carrier::where('is_active', 1)->where('is_available', 1)->first();
            if ($carrier) {
                $data['status'] = 'ASSIGNED';
                $data['carrier_id'] = $carrier->id;
                $now = new DateTime();
                $data['assigned_on'] = $now->format('Y-m-d H:i:s');
            } else {
                $data['status'] = 'PENDING';
            }

            $data['created_by'] = Auth::user()->id;
            //echo print_r($data);exit;
            $result = Pickup::create($data);
            if ($result) {
                if ($carrier) {
                    $carrier->update(array(
                        'is_available' => 0,
                    ));
                }
                $newRecord = Pickup::where('id', $result->id)->first();
                return $this->success($newRecord, "Created successfully", 201);
            } else {
                return $this->error('Unable to create', $data);
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => ['required', Rule::in(['COD', 'ONLINE'])],
            'payer' => ['required', Rule::in(['SENDER', 'RECEIVER'])],
            'distance' => ['required'],
            'cost_per_km' => ['required'],
            'payable_amount' => ['required'],
            'sender_latitude' => ['required'],
            'sender_longitude' => ['required'],
            'receiver_latitude' => ['required'],
            'receiver_longitude' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $data['status'] = 'PENDING';
            $data['created_by'] = Auth::user()->id;

            $result = Pickup::create($data);
            if ($result) {
                $newRecord = Pickup::where('id', $result->id)->first();
                return $this->success($newRecord, "Created successfully", 201);
            } else {
                return $this->error('Unable to create', $data);
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => ['required'],
            'id' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $carrier = Carrier::where('id', $data['carrier_id'])->first();
            $pickup = Pickup::where('id', $data['id'])->first();
            if ($carrier && $pickup) {
                $pickup['status'] = 'ASSIGNED';
                $pickup['carrier_id'] = $carrier->id;
                $now = new DateTime();
                $pickup['assigned_on'] = $now->format('Y-m-d H:i:s');
                $pickup->save();
				
				// sending Notification carrier
				$carrierObj = new Carrier();
				$carrier_details = $carrierObj->getCarrierById($carrier->id);
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['pickup_assigned_carrier']['title'];
				$message = $notification['pickup_assigned_carrier']['message'];
				if(sizeof($carrier_details)>0){
					$device_id = $carrier_details[0]->device_id;
				}
				if($device_id != ''){					
					$notification_data = array(
												//"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"pickup_id" => $pickup->id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
				}
				// Admin Notification	
				$NotificationsObj = new Notifications();	
				$NotificationsObj->addNotification($message, $title);
				
				// NOtification to Customer
				$customer_userid = $pickup->created_by;
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['pickup_assigned_customer']['title'];
				$message = str_replace("carrier_name",$carrier_details[0]->name,$notification['pickup_assigned_customer']['message']);
				if($customer_userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($customer_userid);
				}
				if($device_id != ''){					
					$notification_data = array(
												//"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"pickup_id" => $pickup->id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
				}
				// Admin Notification	
				$NotificationsObj = new Notifications();	
				$NotificationsObj->addNotification($message, $title);
				
                return $this->success($pickup, "Assigned successfully", 201);
            } else {
                return $this->error('ValidationError', 'Unable to assign');
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Entities\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show($pickupid)
    {
        $result = Pickup::where('id', $pickupid)->with([
            'carrier',
            'carrier.user',
        ])->get();
        return $this->success($result, "Success", 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Entities\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pickup $pickup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Entities\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pickup $pickup)
    {
        //
    }

    public function accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $order = Pickup::where('id', $data['id'])->where('status', 'ASSIGNED')->first();
            if ($order) {
                $order['status'] = 'IN_TRANSIT';
                $now = new DateTime();
                $order['pickup_on'] = $now->format('Y-m-d H:i:s');
                $order->save();
				
				// NOtification to Customer
				$carrierObj = new Carrier();
				$carrier_details = $carrierObj->getCarrierById($order->carrier_id);
				$customer_userid = $order->created_by;
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['pickuped_customer']['title'];
				$message = str_replace("carrier_name",$carrier_details[0]->name,$notification['pickuped_customer']['message']);
				if($customer_userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($customer_userid);
				}
				if($device_id != ''){			
					
					$notification_data = array(
												//"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"pickup_id" => $order->id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
				} 
				// Admin Notification	
				$NotificationsObj = new Notifications();	
				$NotificationsObj->addNotification($message, $title);
				
                return $this->success($order, "Updated successfully", 200);
            } else {
                return $this->error('ValidationError', 'Order does not exists');
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    public function complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'status' => ['required', Rule::in(['DELIVERED', 'REJECTED'])],
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $order = Pickup::where('id', $data['id'])->where('status', 'IN_TRANSIT')->first();
            if ($order) {
                $order['status'] = $data['status'];
                $now = new DateTime();
                $order['completed_on'] = $now->format('Y-m-d H:i:s');
                $order->save();
				
				// NOtification to Customer
				$carrierObj = new Carrier();
				$carrier_details = $carrierObj->getCarrierById($order->carrier_id);
				$customer_userid = $order->created_by;
				$device_id = '';
				$globalconfig = \config('globalconfig');
				$notification = $globalconfig['notification'];
				$title = $notification['pickup_delivered_customer']['title'];
				$message = str_replace("carrier_name",$carrier_details[0]->name,$notification['pickup_delivered_customer']['message']);
				if($customer_userid != ''){
					$userObj = new User();
					$device_id = $userObj->getDeviceId($customer_userid);
				}
				if($device_id != ''){					
					
					$notification_data = array(
												//"click_action" => "FLUTTER_NOTIFICATION_CLICK",
												"pickup_id" => $order->id
											  );				
					$utility = new Utility;
					$utility->sendNotification($title,$message,$notification_data,$device_id);
				}
				// Admin Notification	
				$NotificationsObj = new Notifications();	
				$NotificationsObj->addNotification($message, $title);
				
                $carrier = Carrier::where('id', $order->carrier_id)->first();
                if ($carrier) {
                    $carrier->update(array(
                        'is_available' => 1,
                    ));
                }
                return $this->success($order, "Updated successfully", 200);
            } else {
                return $this->error('ValidationError', 'Order does not exists');
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }
}
