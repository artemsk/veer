<?php namespace Veer\Services\Traits;

trait HomeTraits {
	
	/* get only home entities */
	public function getHomeEntities($model, $siteId, $homeId)
	{
		$items = $model::homepages($siteId, $homeId)->with(
				array('categories' => function($query) use ($siteId, $homeId) {
					$query->where('sites_id', '=', $siteId)->where('categories.id', '!=', $homeId);
				}))->with(array('images' => function($query) {
			$query->orderBy('pivot_id', 'asc');
		}));

		if ($model == "\Veer\Models\Page") {
			$items->excludeHidden();
		} else {
			$items->checked();
		}

		return $items;
	}

}
