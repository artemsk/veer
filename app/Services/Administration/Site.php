<?php namespace Veer\Services\Administration;

class Site
{

        /**
	 * Update Configuration
	 * @return void | (ajax?)view
	 */
	public function updateConfiguration()
	{
		\Eloquent::unguard();

		$siteid = Input::get('siteid');
		$confs = Input::get('configuration');
		$new = Input::get('new');

		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }

		if(!empty($siteid)) {

			$save = Input::get('save');
			$delete = Input::get('dele');

			if(!empty($save) && !empty($confs[$cardid]['key'])) {
				$newc = \Veer\Models\Configuration::firstOrNew(array("conf_key" => $confs[$cardid]['key'], "sites_id" => $siteid));
				$newc->sites_id = $siteid;
				$newc->conf_key = $confs[$cardid]['key'];
				$newc->conf_val = $confs[$cardid]['value'];
                                $newc->theme = $confs[$cardid]['theme'];
				$newc->save();

				$cardid = $newc->id;
				$this->action_performed[] = "UPDATE configuration";
			}

			if(!empty($delete)) {
				\Veer\Models\Configuration::destroy($cardid);
				$this->action_performed[] = "DELETE configuration";
			}

			\Illuminate\Support\Facades\Artisan::call('cache:clear');
			$this->action_performed[] = "CLEAR cache";

			// for ajax calls
			if(app('request')->ajax()) {

				$items = ( new \Veer\Services\Show\Site )->getConfiguration($siteid, array('id','desc'));

				return view(app('veer')->template.'.lists.configuration-cards', array(
					"configuration" => $items[0]->configuration,
					"siteid" => $siteid,
				));
			}

		} else {
			Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload'));
		}
	}
}
