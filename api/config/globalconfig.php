<?php 
return [
    'paginator_data_per_page' => 15,
    
    'mail_headers' => "MIME-Version: 1.0" . "\r\nContent-type:text/html;charset=UTF-8" . "\r\nFrom: <no-reply@fooddelivery.com>" . "\r\n",
	'sms_config' => [
	   'sid' => 'ACa79ae2fc9e381d1784b206d63df34121',
	   'token' => '0f9b4451cfe1047051e10db991297565',
	   'from' => '+15868008671',
	],
	'no_of_orders_per_carrier' => 3,
	'notification_config' => [
	   'SERVER_KEY' => 'AAAA7i1Kuyk:APA91bHlWTbKwOhYMZpsmp23naZEZqbJZAh5yD_wjxo-EFQML2e4SxpfdK72bPDrWOjTju9qX7XsY18dh1b6ghJchPwTbCKyiDWbX0h-1WsCNtyzWs3Pqa-ID0az3zGPJMdhO9IfmljY',
	   'CARRIER_ACCEPT_ORDER' => [
									"title" => "Order is in queue.",
									"message" => "Please accept the order."
								 ]
	],
	'foodCategoryId' => 1,
	'APP_CONFIG_ID' => 1,
	'payment_credential' => [
		'key_id' => 'rzp_test_PBQnbzep71IxJC',
		'key_secret' => 'cjvWmJ1FRkcM10DtawN5Bjwf'
	],
	'notification' => [
		'place_order_admin' => [
			'title' => 'ORDER PLACED',
			'message' => 'Order placed sucessfully.Waiting for vendor to accept or reject the order.'
		],
		'place_order_vendor' => [
			'title' => 'ORDER PLACED',
			'message' => 'Order placed sucessfully.Please accept or reject the order.'
		],
		'vendor_accept_order' => [
			'title' => 'ORDER APPROVED',
			'message' => 'Order approved by the seller.A delivery person will be assigned sortly.'
		],
		'vendor_reject_order' => [
			'title' => 'ORDER NOT APPROVED',
			'message' => 'Order not approved by the seller. Please try again.'
		],
		'order_assign_delivery_boy' => [
			'title' => 'ORDER ASSIGNED',
			'message' => 'You have a open order.Please accept or reject order.'
		],
		'order_assign_customer' => [
			'title' => 'ORDER ASSIGNED',
			'message' => 'Your order status has been updated. delivery_boy_name is assigned and will update you on your order'
		],
		'delivery_boy_accept_customer' => [
			'title' => 'ORDER ACCEPTED',
			'message' => 'delivery_boy_name accepted your order and en route to shop_name to pickup your order.'
		],
		'delivery_boy_reached' => [
			'title' => 'ORDER STATUS UPDATED',
			'message' => 'delivery_boy_name reached your address.Please collect your order.'
		],
		'product_delivered_customer' => [
			'title' => 'ORDER DELIVERED',
			'message' => 'Order has been delivered successfully. Thank you for choosing us and keep ordering.'
		],
		'product_delivered_vendor' => [
			'title' => 'ORDER DELIVERED',
			'message' => 'Order delivered by delivery_boy_name.'
		],
		'pickup_assigned_carrier' => [
			'title' => 'PICKUP ASSIGNED',
			'message' => 'You have a open pickup. Please accept your pickup.'
		],
		'pickup_assigned_customer' => [
			'title' => 'YOUR PICKUP ASSIGNED',
			'message' => 'Your pickup assigned. carrier_name will pickup from your given address.'
		],
		'pickuped_customer' => [
			'title' => 'PACKAGE PICKED UP',
			'message' => 'Your package picked up successfully, carrier_name will deliver your package soon.'
		],
		'pickup_delivered_customer' => [
			'title' => 'PACKAGE DELIVERED!!',
			'message' => 'Your pickupage delivered successfully by carrier_name.'
		]
	]
];