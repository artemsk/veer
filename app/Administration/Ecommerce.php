<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

trait Ecommerce {
	

	/*
	 * Shop Actions: 
	 * - Bills: update, delete, send|paid|cancel
	 * - Orders:
	 */
	protected function shopActions()
	{
		// orders
		// TODO: move to app(veershop)
		if(Input::has('pin'))
		{
			$pin = key(Input::get('pin'));
			\Veer\Models\Order::where('id','=',head(Input::get('pin')))
				->update(array('pin' => $pin == 1 ? 0 : 1));
		}
		
		if(Input::has('updateOrderStatus'))
		{
			$history = Input::get('history.'.Input::get('updateOrderStatus'));
			array_set($history, 'orders_id', Input::get('updateOrderStatus'));
			array_set($history, 'name', 
				\Veer\Models\OrderStatus::where('id','=', array_get($history, 'status_id', null))
					->pluck('name')
				);
			$sendEmail = array_pull($history, 'send_to_customer', null);
			
			$update = array('status_id' => array_get($history, 'status_id'));
		
			$progress = array_pull($history, 'progress');
			if(!empty($progress)) $update['progress'] = $progress;
			
			\Eloquent::unguard();
			\Veer\Models\OrderHistory::create($history);
			\Veer\Models\Order::where('id','=',Input::get('updateOrderStatus'))
				->update($update);
			
			if(!empty($sendEmail)) $this->sendEmailOrdersStatus(Input::get('updateOrderStatus'), array("history" => $history));
		}
		
		if(Input::has('updatePaymentHold'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updatePaymentHold')))
				->update(array('payment_hold' => key(Input::get('updatePaymentHold'))));
		}
		
		if(Input::has('updatePaymentDone'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updatePaymentDone')))
				->update(array('payment_done' => key(Input::get('updatePaymentDone'))));
		}
		
		if(Input::has('updateShippingHold'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateShippingHold')))
				->update(array('delivery_hold' => key(Input::get('updateShippingHold'))));
		}
		
		if(Input::has('updateOrderClose'))
		{
			\Eloquent::unguard();
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderClose')))
				->update(array('close' => key(Input::get('updateOrderClose')), "close_time" => now()));
		}
		
		if(Input::has('updateOrderHide'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderHide')))
				->update(array('hidden' => key(Input::get('updateOrderHide'))));
		}
		
		if(Input::has('updateOrderArchive'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderArchive')))
				->update(array('archive' => key(Input::get('updateOrderArchive'))));
		}
		
		// bills
		if(Input::has('updateBillStatus'))
		{
			$billUpdate = Input::get('billUpdate.'.Input::get('updateBillStatus'));
			
			\Veer\Models\OrderBill::where('id','=',Input::get('updateBillStatus'))
				->update(array('status_id' => array_get($billUpdate, 'status_id')));
						
			if(array_has($billUpdate, 'comments'))
			{
				$sendEmail = array_pull($billUpdate, 'send_to_customer', null);
				
				array_set($billUpdate, 'name', 
				\Veer\Models\OrderStatus::where('id','=', array_get($billUpdate, 'status_id'))
					->pluck('name')
				);
				\Eloquent::unguard();
				\Veer\Models\OrderHistory::create($billUpdate);
				
				if(!empty($sendEmail)) $this->sendEmailOrdersStatus(array_get($billUpdate, 'orders_id'), array("history" => $billUpdate));
			}
			
			//\Veer\Models\Order::where('id','=',array_get($billUpdate, 'orders_id'))
			//	->update(array('status_id' => array_get($billUpdate, 'status_id')));
		}		
		
		if(Input::has('updateBillSend'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillSend')))
				->update(array('sent' => true));
			
			$b = \Veer\Models\OrderBill::find(head(Input::get('updateBillSend')));

			if(is_object($b)) $this->sendEmailBillCreate($b, $b->order);
		}
		
		if(Input::has('updateBillPaid'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillPaid')))
				->update(array('paid' => key(Input::get('updateBillPaid'))));
		}	
		
		if(Input::has('updateBillCancel'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillCancel')))
				->update(array('canceled' => key(Input::get('updateBillCancel'))));
		}
		
		if(Input::has('deleteBill'))
		{
			\Veer\Models\OrderBill::where('id','=',Input::get('deleteBill'))
				->delete();
		}
		
		if(Input::has('addNewBill') && Input::has('billCreate.fill.orders_id'))
		{
			$fill = Input::get('billCreate.fill');
		
			$order = \Veer\Models\Order::find(array_get($fill, 'orders_id'));
			$status = \Veer\Models\OrderStatus::find(array_get($fill, 'status_id'));
			
			$payment = $payment_method = array_get($fill, 'payment_method');
			
			$sendEmail = array_pull($fill, 'sendTo', null);
			
			if(empty($payment))	
			{
				$payment = \Veer\Models\OrderPayment::find(array_get($fill, 'payment_method_id'));
				$payment_method = isset($payment->name) ? $payment->name : $payment_method;
			}
			
			$content = '';
			
			if(Input::has('billCreate.template'))
			{
                /* leave 'view' instead of 'viewx' because we always need (rendered) html representation of the bill */
				$content = view("components.bills.".Input::get('billCreate.template'), array(
					"order" => $order,
					"status" => $status,
					"payment" => $payment,
					"price" => array_get($fill, 'price')
				))->render();
			}
			
			\Eloquent::unguard();
			
			$b = new \Veer\Models\OrderBill;
			
			$b->fill($fill);
			$b->users_id = isset($order->users_id) ? $order->users_id : 0;
			$b->payment_method = $payment_method;
			$b->content = $content;
				
			if(!empty($sendEmail)) $b->sent = true;
			
			$b->save();
			
			if(!empty($sendEmail)) $this->sendEmailBillCreate($b, $order);
		}
	}
	
	
	/**
	 * send emails when creating bill
	 */
	protected function sendEmailBillCreate($b, $order)
	{
		$data['orders_id'] = app('veershop')->getOrderId($order->cluster, $order->cluster_oid);
		$data['name'] = $order->name;
		$data['bills_id'] = $b->id;
		$data['link'] = $order->site->url . "/order/bills/" . $b->id . "/" . $b->link;
				
		$subject = \Lang::get('veeradmin.emails.bill.new.subject', array('oid' => $data['orders_id']));

		(new \Veer\Commands\SendEmailCommand('emails.bill-create', 
			$data, $subject, $order->email, null, $order->sites_id))->handle();
	}
	
	
	/**
	 * send emails when updating order status
	 */
	protected function sendEmailOrdersStatus($orderId, $options = array())
	{
		$data = \Veer\Models\Order::where('id','=',$orderId)
					->select('sites_id', 'cluster', 'cluster_oid', 'name', 'email')->first();
				
		$data_array = $data->toArray();

		$data_array['orders_id'] = app('veershop')->getOrderId($data->cluster, $data->cluster_oid);
		$data_array['status'] = array_get($options, 'history');
		$data_array['link'] = $data->site->url . "/order/" . $orderId;

		$subject = \Lang::get('veeradmin.emails.order.subject', array('oid' => $data_array['orders_id']));

		if(!empty($data->email)) { (new \Veer\Commands\SendEmailCommand('emails.order-status', 
			$data_array, $subject, $data->email, null, $data->sites_id))->handle(); }
	}
	
	
	/*
	 * update Statuses
	 */
	public function updateStatuses()
	{
		\Eloquent::unguard();

		if(Input::has('updateGlobalStatus'))
		{
			$status_id = Input::get('updateGlobalStatus');
			
			$s = \Veer\Models\OrderStatus::find($status_id);
			
			if(is_object($s))
			{
				$this->addOrUpdateGlobalStatus($s, $status_id);
				Event::fire('veer.message.center', \Lang::get('veeradmin.status.update'));
				
			}
		}
		
		if(Input::has('deleteStatus'))
		{
			$this->deleteStatus(Input::get('deleteStatus'));
			Event::fire('veer.message.center', \Lang::get('veeradmin.status.delete'). 
				" " . app('veeradmin')->restore_link('OrderStatus', Input::get('deleteStatus')));
			
		}
		
		if(Input::has('addStatus'))
		{
			foreach(Input::get('InName') as $key => $value)
			{
				if(!empty($value))
				{
					$this->addOrUpdateGlobalStatus(new \Veer\Models\OrderStatus, $key);
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.status.new'));
						
		}
	}
	
	
	/**
	 * add or update global status (query)
	 * @param type $s
	 * @param type $status_id
	 */
	protected function addOrUpdateGlobalStatus($s, $status_id)
	{
		$s->name = Input::get('InName.'.$status_id);
		$s->manual_order = Input::get('InOrder.'.$status_id, $status_id);
		$s->color = Input::get('InColor.'.$status_id, '#000');
				
		$flag = Input::get('InFlag.'.$status_id);
				
		$flags = array('flag_first' => 0,'flag_unreg' => 0, 'flag_error' => 0,
				'flag_payment' => 0, 'flag_delivery' => 0, 'flag_close' => 0,
				'secret' => 0);
				
		if(!empty($flag)) $flags[$flag] = true;

		$s->fill($flags);
		$s->save();		
	}
	
	
	/**
	 * delete Status
	 */
	protected function deleteStatus($id)
	{
		\Veer\Models\Order::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderBill::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderHistory::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderStatus::destroy($id);
	}
	
	
	/**
	 * update Payment Methods
	 */
	public function updatePayment()
	{
		if(Input::has('deletePaymentMethod'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.delete') . 
				" " . app('veeradmin')->restore_link('OrderPayment', Input::get('deletePaymentMethod')));
			
			return $this->deletePaymentMethod(Input::get('deletePaymentMethod'));
		}
		
		if(Input::has('updatePaymentMethod'))
		{
			$p = \Veer\Models\OrderPayment::find(Input::get('updatePaymentMethod'));
			if(!is_object($p))
			{
				return Event::fire('veer.message.center', \Lang::get('veeradmin.payment.error'));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.update'));
				
		}
		
		if(Input::has('addPaymentMethod'))
		{
			$p = new \Veer\Models\OrderPayment;
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.new'));
				
		}	
		
		$func_name = Input::get('payment.fill.func_name');		
        $classFullName = starts_with($func_name, "\\") ? $func_name : "\\Veer\\Components\\Ecommerce\\" . $func_name;
        
		if(!empty($func_name) && !class_exists($classFullName)) 
		{
			return Event::fire('veer.message.center', \Lang::get('veeradmin.payment.error'));
		}
        		
		$fill = Input::get('payment.fill');
		
		$fill['commission'] = strtr( array_get($fill, 'commission'), array("%" => ""));
		$fill['discount_price'] = strtr( array_get($fill, 'discount_price'), array("%" => ""));
		$fill['enable'] = isset($fill['enable']) ? true : false;
		$fill['discount_enable'] = isset($fill['discount_enable']) ? true : false;
		
		\Eloquent::unguard();
		
		$p->fill($fill);
		$p->save();
	}

	
	/**
	 * delete Payment Method
	 */
	protected function deletePaymentMethod($id)
	{
		\Veer\Models\Order::where('payment_method_id','=',$id)
			->update(array('payment_method_id' => 0));
		
		\Veer\Models\OrderBill::where('payment_method_id','=',$id)
			->update(array('payment_method_id' => 0));
				
		\Veer\Models\OrderPayment::destroy($id);
	}
	

	/**
	 * update Shipping Methods
	 */
	public function updateShipping()
	{
		if(Input::has('deleteShippingMethod'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.delete') . 
				" " . app('veeradmin')->restore_link('OrderShipping', Input::get('deleteShippingMethod')));
			
			return $this->deleteShippingMethod(Input::get('deleteShippingMethod'));
		}
		
		if(Input::has('updateShippingMethod'))
		{
			$p = \Veer\Models\OrderShipping::find(Input::get('updateShippingMethod'));
			if(!is_object($p))
			{
				return Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.error'));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.update'));
				
		}
		
		if(Input::has('addShippingMethod'))
		{
			$p = new \Veer\Models\OrderShipping;
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.new'));
				
		}	
		
		$func_name = Input::get('shipping.fill.func_name');
        $classFullName = starts_with($func_name, "\\") ? $func_name : "\\Veer\\Components\\Ecommerce\\" . $func_name;
		
		if(!empty($func_name) && !class_exists($classFullName)) 
		{
			return Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.error'));
		}
		
		$fill = Input::get('shipping.fill');
		
		$fill['discount_price'] = strtr( array_get($fill, 'discount_price'), array("%" => ""));
		$fill['enable'] = isset($fill['enable']) ? true : false;
		$fill['discount_enable'] = isset($fill['discount_enable']) ? true : false;
		
		if(array_has($fill, 'address'))
		{
			$addresses = preg_split('/[\n\r]+/', array_get($fill, 'address') ); // TODO: redo
			foreach($addresses as $k => $address)
			{
				$parts = explode("|", $address);
				$parts = array_filter($parts, function($value) { if(!empty($value)) return $value; });
				$addresses[$k] = $parts;
			}
			
			$fill['address'] = json_encode($addresses);	
		}

		\Eloquent::unguard();
		
		$p->fill($fill);
		$p->save();
	}

	
	/**
	 * delete Shipping Method
	 */
	protected function deleteShippingMethod($id)
	{
		\Veer\Models\Order::where('delivery_method_id','=',$id)
			->update(array('delivery_method_id' => 0));
				
		\Veer\Models\OrderShipping::destroy($id);
	}	
	
	
	/**
	 * update Discounts
	 */
	public function updateDiscounts()
	{
		if(Input::has('deleteDiscount'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.delete') .
				" " . app('veeradmin')->restore_link('UserDiscount', Input::get('deleteDiscount')));
			
			return $this->deleteDiscount(Input::get('deleteDiscount'));
		}
		
		\Eloquent::unguard();
		
		if(Input::has('updateGlobalDiscounts'))
		{
			foreach(Input::get('discount', array()) as $key => $discount)
			{
				$fill = array_get($discount, 'fill');

				$fill['discount'] = strtr($fill['discount'], array("%" => ""));
				$fill['expires'] = isset($fill['expires']) ? true : false;

				if($key == "new") { //continue;
					if(array_get($fill, 'discount') > 0 && array_get($fill, 'sites_id') > 0) $d = new \Veer\Models\UserDiscount; 
				}
				
				else { $d = \Veer\Models\UserDiscount::find($key); }
				
				if(isset($d) && is_object($d))
				{
					$d->fill($fill);
					$d->save();
					unset($d);
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.update'));
					
		}
	}
	
	
	/**
	 * delete Discount
	 */
	protected function deleteDiscount($id)
	{
		return \Veer\Models\UserDiscount::destroy($id);
	}
	
	
	/**
	 * update Bills
	 */
	public function updateBills()
	{
		return $this->shopActions();	
	}
	
	
	/**
	 * update Orders
	 */
	public function updateOrders()
	{
		$this->shopActions();
		
		$editOneOrder = Input::get('id');
		
		if(!empty($editOneOrder)) 
		{ 	
			return $this->updateOneOrder($editOneOrder); 
		}		
	}
	
	
	/**
	 * update One Order
	 */
	public function updateOneOrder($id)
	{		
		$action = Input::get('action');
		$fill = Input::get('fill');
		
		$siteId = Input::get('fill.sites_id');						
		if(empty($siteId)) $fill['sites_id'] = app('veer')->siteId;
		
		$usersId = Input::get('fill.users_id');
		if(empty($usersId) && $action != "add") $fill['users_id'] = \Auth::id();
		
		$order = \Veer\Models\Order::find($id);
		
		if(!is_object($order)) $order = new \Veer\Models\Order;
		
		if($action == "delete")
		{
			$this->deleteOrder($order);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.order.delete') .
				" " . app('veeradmin')->restore_link('order', $order->id));
			
			$this->skipShow = true;
			return \Redirect::route('admin.show', array('orders'));
		}
		
		\Eloquent::unguard();
		
		$fill['free'] = isset($fill['free']) ? 1 : 0;
		$fill['close'] = isset($fill['close']) ? 1 : 0;
		$fill['hidden'] = isset($fill['hidden']) ? 1 : 0;
		$fill['archive'] = isset($fill['archive']) ? 1 : 0;
		$fill['delivery_free'] = isset($fill['delivery_free']) ? 1 : 0;
		$fill['delivery_hold'] = isset($fill['delivery_hold']) ? 1 : 0;
		$fill['payment_hold'] = isset($fill['payment_hold']) ? 1 : 0;
		$fill['payment_done'] = isset($fill['payment_done']) ? 1 : 0;
		
		if($fill['close'] == true) $fill['close_time'] = now();
		
		$fill['progress'] = isset($fill['progress']) ? strtr($fill['progress'], array("%" => "")) : 5;
		
		$deliveryPlan = array_get($fill, 'delivery_plan');
		$deliveryReal = array_get($fill, 'delivery_real');
		
		$fill['delivery_plan'] = !empty($deliveryPlan) ? parse_form_date($deliveryPlan) : NULL;
		$fill['delivery_real'] = !empty($deliveryReal) ? parse_form_date($deliveryReal) : NULL;
		
		if($order->cluster_oid != array_get($fill, 'cluster_oid') || $order->cluster != array_get($fill, 'cluster'))
		{
			$existingOrders = \Veer\Models\Order::where('sites_id','=',$fill['sites_id'])
				->where('cluster','=', array_get($fill, 'cluster'))
				->where('cluster_oid','=',array_get($fill, 'cluster_oid'))->first();
			
			// we cannot update cluster ids if they already exist
			if(isset($existingOrders) || array_get($fill, 'cluster_oid') == null) 
			{
				array_forget($fill, 'cluster_oid');
				array_forget($fill, 'cluster');
			}
		}
				
		if($order->status_id != array_get($fill, 'status_id', $order->status_id)) $addStatusToHistory = true;
		
		if($order->delivery_method_id != array_get($fill, 'delivery_method_id', $order->delivery_method_id)	&& 
			array_get($fill, 'delivery_method') == null)
		{
			$fill['delivery_method'] = \Veer\Models\OrderShipping::where('id','=',array_get($fill, 'delivery_method_id'))->pluck('name');
		}
		
		if($order->payment_method_id != array_get($fill, 'payment_method_id', $order->payment_method_id)	&& 
			array_get($fill, 'payment_method') == null)
		{
			$fill['payment_method'] = \Veer\Models\OrderPayment::where('id','=',array_get($fill, 'payment_method_id'))->pluck('name');
		}
		
		if($order->userbook_id != array_get($fill, 'userbook_id', $order->userbook_id))
		{
			$getBook = \Veer\Models\UserBook::find(array_get($fill, 'userbook_id'));
			if(is_object($getBook))
			{
				$order->userbook_id = $getBook->id;
				$order->country = $getBook->country;
				$order->city = $getBook->city;
				$order->address = trim( $getBook->postcode . " " . $getBook->address );	
			}
		}
		
		$order->fill($fill);
		
		if($action == "add")
		{
			$validator = \Validator::make($fill, array(
				'email' => 'required_without:users_id',
				'users_id' => 'required_without:email',
			));
			
			$validator_content = \Validator::make(Input::all(), array(
				'attachContent' => 'required'
			));
			
			if($validator->fails() || $validator_content->fails()) { 
				Event::fire('veer.message.center', \Lang::get('veeradmin.order.new.error'));
				
				return false;	
			}
			
			list($order, $checkDiscount) = app('veershop')->addNewOrder($order, $usersId, Input::get('userbook.0', array()));
			
			$addStatusToHistory = true;
		}
		
		// new book
		if($action == "addUserbook" || $action == "updateUserbook")
		{
			foreach(Input::get('userbook', array()) as $book)
			{
				$newBook = app('veershop')->updateOrNewBook($book);
				
				if(isset($newBook) && is_object($newBook)) { 
					$order->userbook_id = $newBook->id;
					$order->country = $newBook->country;
					$order->city = $newBook->city;
					$order->address = trim( $newBook->postcode . " " . $newBook->address );
				}
			}
		}
		
		// contents
		if(Input::has('editContent'))
		{
			$contentId = Input::get('editContent');			
			$ordersProducts = Input::get('ordersProducts.' . $contentId . ".fill");			
			$content = app('veershop')->editOrderContent( \Veer\Models\OrderProduct::find($contentId) , $ordersProducts, $order);			
			$content->save();
		}
		
		if(Input::has('attachContent'))
		{
			app('veershop')->attachOrderContent(Input::get('attachContent'), $order);
		}
		
		if(Input::has('deleteContent'))
		{
			\Veer\Models\OrderProduct::destroy(Input::get('deleteContent'));
		}
				
		// sums price & weight
		$order = app('veershop')->sumOrderPricesAndWeight($order);
		
		// recalculate delivery
		if($action == "recalculate" || $action == "add")
		{
			$order = app('veershop')->recalculateOrderDelivery($order);			
			$order = app('veershop')->recalculateOrderPayment($order);
		}
		
		// total
		if($order->delivery_free == true) {	$order->price = $order->content_price; }
		else { $order->price = $order->content_price + $order->delivery_price; }
			
		// history
		if(Input::has('deleteHistory'))
		{
			\Veer\Models\OrderHistory::where('id','=',Input::get('deleteHistory'))->forceDelete();
			
			$previous = \Veer\Models\OrderHistory::where('orders_id','=',$order->id)->orderBy('id','desc')->first();
			if(is_object($previous))
			{
				$order->status_id = $previous->status_id;
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.order.history.delete'));
			
		}
				
		$order->save();

		if(isset($addStatusToHistory))
		{
			$statusName = \Veer\Models\OrderStatus::where('id','=',$order->status_id)->pluck('name');
			\Veer\Models\OrderHistory::create(array(
				"orders_id" => $order->id,
				"status_id" => $order->status_id,
				"name" => !empty($statusName) ? $statusName : '',
				"comments" => "",
			));
			// TODO: cross with updateOrdersStatus in shop actions ? remove
		}
		
		if($action == "add" && $order->userdiscount_id > 0 && isset($checkDiscount))
		{
			app('veershop')->changeUserDiscountStatus($checkDiscount);
		}
		
		// communications
		if(Input::has('sendMessageToUser'))
		{
			(new \Veer\Commands\CommunicationSendCommand(Input::get('communication')))->handle();
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.sendmessage'));
		
		}	
		
		// redirect to new order
		if($action == "add")
		{
			$this->sendEmailOrderNew($order);
			
			$this->skipShow = true;
			Input::replace(array('id' => $order->id));
			return \Redirect::route('admin.show', array('orders', 'id' => $order->id));
		}
			
	}
	
	
	/**
	 * send email when creating new order
	 */
	protected function sendEmailOrderNew($order)
	{
		$data = $order->toArray();
		$data['orders_id'] = app('veershop')->getOrderId($order->cluster, $order->cluster_oid);
		$data['link'] = $order->site->url . "/order/" . $order->id;
		
		$subject = \Lang::get('veeradmin.emails.order.new.subject', array('oid' => $data['orders_id']));

		if(!empty($order->email)) 
		{ 
			(new \Veer\Commands\SendEmailCommand('emails.order-new', 
				$data, $subject, $order->email, null, $order->sites_id))->handle();
		}
	}
	
	
	/**
	 * delete Order
	 */
	protected function deleteOrder($order)
	{
		if(is_object($order))
		{
			\Veer\Models\OrderHistory::where('orders_id','=',$order->id)->delete();
			$order->orderContent()->delete();			
			$order->bills()->delete();			
			$order->secrets()->delete();
			// communications skip
			
			$order->delete();		
		}
	}
}
