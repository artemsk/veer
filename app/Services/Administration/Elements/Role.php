<?php

namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Role {

    use HelperTrait, AttachTrait;
    
    protected $action;
    protected $role = [];
    protected $role_id = 0;
    protected $site_id;
    protected $users;
    protected $type = 'role';

    public function __construct()
    {
        \Eloquent::unguard();
        $this->action = Input::get('action');
        $this->role = Input::get('role', []);
        $this->site_id = Input::get('InSite');
        $this->users = Input::get('InUsers');
    }

    public function run()
    {
        if($this->action == 'updateRoles') $this->updateRoles();

        if(starts_with($this->action, 'deleteRole')) $this->deleteRole();

        if(!empty($this->users)) $this->attachUsers();
    }

    protected function updateRoles()
    {
        foreach ($this->role as $roleId => $role) {

            if($roleId != "new") {

                \Veer\Models\UserRole::where('id', '=', $roleId)
                        ->update($role);                             
                
            } elseif($roleId == "new" && !empty($role['role'])) {

                $r = \Veer\Models\UserRole::firstOrNew([
                    "role" => $role['role'],
                    "sites_id" => $this->site_id
                ]);
                
                $r->fill($role);
                $r->sites_id =  $this->site_id;
                $r->save();
                $this->role_id = $r->id;
            }
        }

        event('veer.message.center', trans('veeradmin.role.update'));
    }

    protected function deleteRole()
    {
        list(, $id) = explode(".", $this->action);

        $this->deleteUserRole($id);
        
        event('veer.message.center', trans('veeradmin.role.delete'));
    }

    protected function attachUsers()
    {
        $parseAttach = explode("[", $this->users);

        if (starts_with($this->users, "NEW")) {
            $rolesId = $this->role_id;
        } else {
            $rolesId = trim(array_get($parseAttach, 0));
        }

        $usersIds = $this->parseIds(substr(array_get($parseAttach, 1), 0, -1));

        if(!empty($usersIds)) $this->associate("users", $usersIds, (int)$rolesId, "roles_id");
    }

    /**
	 * delete User Role
	 * @param type $id
	 */
	protected function deleteUserRole($id)
	{
		$u = \Veer\Models\UserRole::find($id);
        
		if(is_object($u)) {
			$u->users()->update(['roles_id' => null]);
			$u->delete();
		}
	}
}
