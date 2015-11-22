<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

trait Users {
	
	/**
	 * update Roles
	 */
	public function updateRoles()
	{
		\Eloquent::unguard();
		
		$action = Input::get('action');
		
		if($action == "updateRoles")
		{
			foreach(Input::get('role', array()) as $roleId => $role)
			{
				if($roleId != "new") 
				{
					\Veer\Models\UserRole::where('id','=', $roleId)
						->update($role);					
				}
				
				elseif( $roleId == "new" && !empty($role['role']))
				{
					$r = \Veer\Models\UserRole::firstOrNew(array("role" => $role['role'], "sites_id" => Input::get('InSite')));
					$r->fill($role);
					$r->sites_id = Input::get('InSite');
					$r->save();
					$newId = $r->id;
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.role.update'));
			
		}
		
		if(!empty($action) && starts_with($action, "deleteRole"))
		{
			list($act, $id) = explode(".", $action);
			$this->deleteUserRole($id);
			Event::fire('veer.message.center', \Lang::get('veeradmin.role.delete'));
		
		}
		
		if(Input::has('InUsers'))
		{
			$users = Input::get('InUsers');
			
			$parseAttach = explode("[", $users);
			
			if(starts_with($users, "NEW")) { $rolesId = $newId; } 
			
			else {	$rolesId = trim(array_get($parseAttach, 0));	}
			
			$usersIds = $this->parseIds( substr( array_get($parseAttach, 1) ,0,-1) );
		
			$this->associate("users", $usersIds, $rolesId, "roles_id");								
		}
	}
	
	
	/**
	 * delete User Role
	 * @param type $id
	 */
	protected function deleteUserRole($id)
	{
		$u = \Veer\Models\UserRole::find($id);
		if(is_object($u)) {
			$u->users()->update(array('roles_id' => null));
			$u->delete();			
		}
	}
	
	
	/**
	 * Associate (belongTo, hasMany relationships)
	 * - updating parents (parent field) in childs tables
	 * 
	 * @param string $relation Child model, ex: page, user, product etc.
	 * @param array $childs Ids 
	 * @param string $childsField 
	 * @param int $parentId
	 * @param string $parentField
	 * @param string $raw Raw where Sql
	 * @return void
	 */
	protected function associate($relation, $childs, $parentId, $parentField, $childsField = "id", $raw = null)
	{
		$relation = "\\" . elements(str_singular($relation));
		$r = $relation::whereIn($childsField, $childs);
		if (!empty($raw)) { $r->whereRaw($raw); }
		$r->update(array($parentField => $parentId));
	}

	
	/**
	 * update Communications
	 */
	public function updateCommunications()
	{
		if(Input::get('action') == "addMessage")
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.new'));
		
			return (new \Veer\Commands\CommunicationSendCommand(Input::get('communication')))->handle();
		}
		
		if(Input::has('hideMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('hideMessage')))
				->update(array('hidden' => true));
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.hide'));
			
		}
		
		if(Input::has('unhideMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('unhideMessage')))
				->update(array('hidden' => false));
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.unhide'));
		
		}
		
		if(Input::has('deleteMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('deleteMessage')))
				->delete();
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.delete'));
			
		}
	}
	
	
	/**
	 * update Comments
	 */
	public function updateComments()
	{
		if(Input::get('action') == "addComment")
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.new'));
			
			return (new \Veer\Commands\CommentSendCommand(Input::all()))->handle();
		}
		
		if(Input::has('hideComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('hideComment')))
				->update(array('hidden' => true));
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.hide'));
		
		}
		
		if(Input::has('unhideComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('unhideComment')))
				->update(array('hidden' => false));
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.unhide'));
			
		}
		
		if(Input::has('deleteComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('deleteComment')))
				->delete();
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.delete'));
			
		}
	}
	
	
	/**
	 * update Searches
	 */
	public function updateSearches()
	{
		if(Input::has('deleteSearch'))
		{
			$this->deleteSearch(head(Input::get('deleteSearch')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.search.delete'));
			
			return null;
		}
		
		if(Input::get('action') == "addSearch" && Input::has('search'))
		{
			$q = trim( Input::get('search') );
			if(!empty($q))
			{
				$search = \Veer\Models\Search::firstOrCreate(array("q" => $q));
				$search->increment('times');                  
				$search->save();
				
				$users =  Input::get('users');
				
				if(starts_with($users, ':')) 
				{
					$users = substr($users, 1);
					
					if( !empty($users) )
					{
						$users = explode(",", trim($users) );

						if(count($users) > 0) $search->users()->attach($users);
					}	
				}
								
				Event::fire('veer.message.center', \Lang::get('veeradmin.search.new'));
				
			}
		}	
	}
	
	
	/**
	 * delete Search
	 * @param int $id
	 */
	protected function deleteSearch($id)
	{
		$s = \Veer\Models\Search::find($id);
		if(is_object($s)) {
			$s->users()->detach();
			$s->delete();			
		}
	}
	
	
	/**
	 * update Lists
	 */
	public function updateLists()
	{
		if(Input::has('deleteList'))
		{
			$this->deleteList(head(Input::get('deleteList')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.list.delete'));
			
			return null;
		}
		
		if(Input::get('action') == "addList" && ( Input::has('products') || Input::has('pages') ))
		{
			\Eloquent::unguard();
			
			$all = Input::all();
			
			if(array_get($all, 'fill.users_id') == null && array_get($all, 'fill.session_id') == null)
			{
				array_set($all, 'fill.users_id', \Auth::id());
				array_set($all, 'fill.session_id', \Session::getId());
			}
			
			if(array_get($all, 'fill.name') == null) array_set($all, 'fill.name', '[basket]');				
			if(array_get($all, 'checkboxes.basket') != null) array_set($all, 'fill.name', '[basket]');	
				
			$p = preg_split('/[\n\r]+/', trim( array_get($all, 'products') )); // TODO: redo
			
			if(is_array($p)) { $this->saveAndAttachLists ($p, '\\'.elements('product'), array_get($all, 'fill')); }					
			
			$pg = preg_split('/[\n\r]+/', trim( array_get($all, 'pages') )); // TODO: redo
			
			if(is_array($pg)) { $this->saveAndAttachLists ($pg, '\\'.elements('page'), array_get($all, 'fill')); }		
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.list.new'));
			
		}	
	}
	
	
	/**
	 * Save and Attach Lists
	 * @param type $p
	 * @param type $model
	 * @param type $fill
	 */
	protected function saveAndAttachLists($p, $model, $fill)
	{
		foreach($p as $element)
		{
			$parseElements = explode(":", $element);

			$id = array_get($parseElements, 0);
			$qty = array_get($parseElements, 1, 1);
			$attrStr = array_get($parseElements, 2);

			$attrs = explode(",", $attrStr);

			$item = $model::find( trim($id) );

			if(is_object($item))
			{
				$cart = new \Veer\Models\UserList;
				$cart->fill( $fill );
				$cart->quantity = !empty($qty) ? $qty : 1;

				if(is_array($attrs) && !empty($attrStr) ) 
				{
					$cart->attributes = json_encode($attrs); 
				}
				$cart->save();
				$item->userlists()->save($cart);
			}
		}
	}
	
	
	/**
	 * delete List
	 * @param int $id
	 */
	protected function deleteList($id)
	{
		\Veer\Models\UserList::where('id','=',$id)->delete();
	}
	
	
	/**
	 * update Books
	 */
	public function updateBooks()
	{
		if(Input::has('deleteUserbook'))
		{
			$this->deleteBook(head(Input::get('deleteUserbook')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.delete') . 
				" " . app('veeradmin')->restore_link('UserBook', head(Input::get('deleteUserbook'))));
			
			return null;
		}
		
		$all = Input::all();
		$action = array_get($all, 'action');
		
		if($action == "addUserbook" || $action == "updateUserbook" )
		{
			app('veershop')->updateOrNewBook( head(array_get($all, 'userbook', array())) );
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.update'));
			
		}
	}
	
	
	/**
	 * delete Book
	 * @param int $id
	 */
	protected function deleteBook($id)
	{
		\Veer\Models\UserBook::where('id','=',$id)->delete();
	}
	
	
	/**
	 * update Users
	 */
	public function updateUsers()
	{
		Event::fire('router.filter: csrf');
			
		$restrictions = Input::get('changeRestrictUser');
		$ban = Input::get('changeStatusUser');
		$delete = Input::get('deleteUser');
		
		if(!empty($restrictions))
		{
			\Veer\Models\User::where('id','=', key($restrictions))
				->update(array('restrict_orders' => head($restrictions)));			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.update'));
			
			return null;
		}
		
		if(!empty($ban) && key($ban) != \Auth::id())
		{
			\Veer\Models\User::where('id','=', key($ban))
				->update(array('banned' => head($ban)));
			
			if(head($ban) == true) {
				\Veer\Models\UserAdmin::where('users_id','=', key($ban))
				->update(array('banned' => head($ban)));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.ban'));
			
			return null;
		}
		
		if(!empty($delete) && key($delete) != \Auth::id())
		{
			$this->deleteUser(key($delete));
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.delete') .
				" " . app('veeradmin')->restore_link("user", key($delete)));
			
			return null;
		}
		
		// if we're working with one user then call another function
		//
		$editOneUser = Input::get('id');
		if(!empty($editOneUser)) 
		{ 	
			return $this->updateOneUser($editOneUser); 
		}
		
		if(Input::get('action') == "Add")
		{
			$freeForm = Input::get('freeForm');
			$parseForm = !empty($freeForm) ? preg_split('/[\n\r]+/', trim($freeForm)) : array() ; // TODO: redo
			
			$freeFormKeys = array(
				'username', 'phone', 'firstname', 'lastname', 'birth', 'gender', 'roles_id',
				'newsletter', 'restrict_orders', 'banned'
			);
			
			$siteId = Input::get('siteId');						
			if(empty($siteId)) $siteId = app('veer')->siteId;
			
			$rules = array(
				'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . $siteId,
				'password' => 'required|min:6',
			);			

			$validator = \Validator::make(Input::all(), $rules);
			 
			if(!$validator->fails())
			{
				$user = new \Veer\Models\User;
				
				$user->email = Input::get('email');
				$user->password = Input::get('password');
				$user->sites_id = $siteId;
				
				foreach($parseForm as $key => $value)
				{
					if(!empty($value)) $user->{$freeFormKeys[$key]} = $value;
				}
				$user->save();		
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.new'));
						
			}

		}
	}
	
	
	/**
	 * delete User and update connections
	 * @param int $id
	 */
	protected function deleteUser($id)
	{
		$u = \Veer\Models\User::find($id);
		if(is_object($u)) 
		{
			$u->discounts()->update(array("status" => "canceled"));
			$u->userlists()->update(array("users_id" => false));
			$u->books()->update(array("users_id" => false));
			$u->images()->detach();
			$u->searches()->detach();
			$u->administrator()->delete();
			// don't update: orders, bills, pages, comments, communications
			// do not need: site, role	
			$u->delete();
		}
	}
	
	
	/**
	 * update One user
	 */
	public function updateOneUser($id)
	{	
		$action = Input::get('action');
		$fill = Input::get('fill');
		
		$siteId = Input::get('fill.sites_id');						
		if(empty($siteId)) $siteId = app('veer')->siteId;
		
		$fill['sites_id'] = $siteId;
		if(array_has($fill, 'password') && empty($fill['password'])) array_forget($fill, 'password');
		
		if($action == "add") 
		{	
			$rules = array(
				'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . $siteId,
				'password' => 'required|min:6',
			);			

			$validator = \Validator::make($fill, $rules);
			
			if($validator->fails()) { 
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.new.error'));
				
				return false;	
			}
			
			$user = new \Veer\Models\User;
			$user->save();		
			$id = $user->id;
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.new'));
			
		} 
		
		else 
		{
			$user = \Veer\Models\User::find($id);
		}
		
		$fill['restrict_orders'] = isset($fill['restrict_orders']) ? true : false;
		$fill['newsletter'] = isset($fill['newsletter']) ? true : false;
		
		$fill['birth'] = parse_form_date(array_get($fill, 'birth'));
		
		\Eloquent::unguard();
		$user->fill($fill);
		$user->save();
		
		if(Input::has('addAsAdministrator'))
		{
			$admin = \Veer\Models\UserAdmin::withTrashed()->where('users_id','=',$id)->first();
			if(!is_object($admin))
			{
				\Veer\Models\UserAdmin::create(array('users_id' => $id));
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.admin'));
						
			}
			
			else { $admin->restore(); }
		}
		
		if(Input::has('administrator')) $this->updateOneAdministrator(Input::get('administrator'), $id);

		// images
		if(Input::hasFile('uploadImage')) 
		{
			$this->upload('image', 'uploadImage', $id, 'users', 'usr', null);
		}			
		
		$this->attachElements(Input::get('attachImages'), $user, 'images', null);
		
		$this->detachElements($action, 'removeImage', $user, 'images', null);

                $this->detachElements($action, 'removeAllImages', $user, 'images', null, true);

		// pages
		if(Input::has('attachPages'))
		{
			$pages = $this->parseIds(Input::get('attachPages'));
			$this->associate("pages", $pages, $id, "users_id");
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.attach'));
						
		}
		
		if(starts_with($action, 'removePage'))
		{
			$p = explode(".", $action);
			$this->associate("pages", array($p[1]), 0, "users_id");
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.detach'));
			
		}
		
		if(starts_with($action, "deletePage")) 
		{
			$p = explode(".", $action); 
			$this->deletePage($p[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.delete'));
			
			return null;
		}
		
		// books
		if($action == "addUserbook" || $action == "updateUserbook" )
		{
			foreach(Input::get('userbook', array()) as $book)
			{
				app('veershop')->updateOrNewBook($book);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.update'));
			
		}		
		
		if(Input::has('deleteUserbook'))
		{
			$this->deleteBook(head(Input::get('deleteUserbook')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.delete'));
			
			return null;
		}
		
		// discounts
		if(Input::has('cancelDiscount'))
		{
			\Veer\Models\UserDiscount::where('id','=', head(Input::get('cancelDiscount')))
				->update(array('status' => 'canceled'));
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.cancel'));
			
		}
		
		if(Input::has('attachDiscounts'))
		{
			$discounts = $this->parseIds(Input::get('attachDiscounts'));
			$this->associate("UserDiscount", $discounts, $id, "users_id", "id", "users_id = 0 and status = 'wait'");
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.attach'));
						
		}
		
		// orders & bills
		$this->shopActions();
		
		// communications
		if(Input::has('sendMessageToUser'))
		{
			(new \Veer\Commands\CommunicationSendCommand(Input::get('communication')))->handle();
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.sendmessage'));
			
		}

		if($action == "add") {
			$this->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('users', 'id' => $id));	
		}	
	}
	
	
	/** 
	 * update Administrator
	 * TODO: use ranks to determine who can update whom
	 */
	protected function updateOneAdministrator($administrator, $id)
	{
		$administrator['banned'] = array_get($administrator, 'banned', false) ? true : false;
		
		if($id == \Auth::id()) array_forget($administrator, 'banned');
			
		\Veer\Models\UserAdmin::where('users_id','=',$id)
			->update($administrator);
	}
	
}
