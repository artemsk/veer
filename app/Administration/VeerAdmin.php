<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class VeerAdmin extends Show {

	use Configuration, Structure, Ecommerce, Users;

	public $filtered = null;
	
	public $filtered_id = null;
	
	/*
	 *  
	 */
	
	/**
	 * Restore soft deleted entity
	 */
	public function restore($type = null, $id = null)
	{
		if(empty($type) || empty($id)) return;
		
		$type = "\\".elements($type);
		
		$type::withTrashed()->where('id', $id)->restore();
		
		Event::fire('veer.message.center', \Lang::get('veeradmin.restored'));
	}
	
	public function restore_link($type, $id)
	{
		return "<a href=". route('admin.update', array('restore', 'type' => $type, 'id' => $id)) .">".
			\Lang::get('veeradmin.undo')."</a>";
	}
}
