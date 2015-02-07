<?php namespace Veer\Administration;

class Show {
	
	/** 
	 * Search
	 * @param type $t
	 * @return boolean|object
	 */
	public function search($t)
	{
		$q = Input::get('SearchField');
		
		$model = null;
		$id = null;
		
		if(starts_with($q, '!'))
		{
			$parseSearch = explode(":", substr($q,1));
			
			$model = strtolower(array_get($parseSearch, 0));
			$id = array_get($parseSearch, 1);
		}
		
		$field = "id";
		
		if(!empty($model) && !empty($id))
		{
			switch ($model) {
				case "product": $t = "products"; break;
				case "page": $t = "pages"; break;
				case "category": $t = "categories"; $field = "category"; break;					
				case "user": $t = "users"; break;
				case "order": $t  = "orders"; break;
				default: return false; break;
			}
		}
		
		if(empty($model)) { 
			switch ($t) {
				case "books": $model = "UserBook"; break;
				case "lists": $model = "UserList"; break;
				case "roles": $model = "UserRole"; break;					
				case "statuses": $model = "OrderStatus"; break;
				case "payment": $model = "OrderPayment"; break;
				case "shipping": $model = "OrderShipping"; break;
				case "discounts": $model = "UserDiscount"; break;
				case "bills": $model = "OrderBill"; break;

				case "jobs": return false;
				case "etc": return false;

				default: $model = $t; break;
			}
		}
			
		$model = elements($model);
		
		if(!empty($id))
		{
			return \Redirect::route('admin.show',
				array($t, $field => $id));
		}
		
		$view = $t;
		
		switch ($t) {
				case "users": 
					$searchfields = array("email", "username", "firstname", "lastname", "phone"); break;
				case "books": 
					$searchfields = array("name", "country", "region", "city", 
						"postcode", "address", "nearby_station", "b_bank", "b_bik", "b_others"); break; 
				case "searches":
					$searchfields = array("q"); break;
				case "comments":
					$searchfields = array("author", "txt", "rate"); break;
				case "pages":
					$searchfields = array("title", "small_txt", "txt"); break;
				case "products":
					$searchfields = array("title", "descr", "production_code"); break;
				case "tags":
					$searchfields = array("name"); break;
				case "orders":
					$searchfields = array("id", "cluster_oid", "email", "phone"); break;
				case "bills":
					$searchfields = array("id", "orders_id"); break;
				
				// TODO: leftovers: categories, attributes,
				//case "attributes":
				//	$searchfields = array("name", "val", "descr"); break;
				//case "communications":
					//$searchfields = array("sender", "sender_phone", "sender_email",
					//	"message", "recipients", "theme"); break;
					//	TODO: recipients
				//case "lists":
				//	$searchfields = array("name");
				//	$view = "userlists"; break;
				// TODO: regroup
				default: 
					return false;
					//$searchfields = array("id"); break;
			}
		
		if(isset($searchfields))
		{
			$items = $model::whereNested(function($query) use($q, $searchfields) {
							foreach($searchfields as $s)
							{
								$query->orWhere($s, 'like', '%'.$q.'%');
							}
						})->paginate(25);	
		}
		
		if(isset($items) && is_object($items))
		{
			return view(app('veer')->template.'.'.$view, array(
				"items" => $items,
				"data" => app('veer')->loadedComponents,
				"template" => app('veer')->template
			));
		}
			
		return false;
	}
}
