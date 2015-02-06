<?php namespace Veer\Services\Show;

class Filter {
	//put your code here
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Filtered Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | filter/{id[0].id[1].id[2].id[3]}
	 * 
	 * @params: FILTER_ATTRS key|value 
	 */
	public function filterShowQuery($siteId, $id, $queryParams)
	{
		$p = null;

		$id = explode('.', $id);

		$category_id = $id[0] ? $id[0] : 0;

		// If the value of first key of array is "null", we will change
		// condition operator from "=" to ">" (0) to include all categories in our
		// filter which exist for our site.               
		$c = "=";
		if ($category_id <= 0) {
			$c = ">";
		}

		$a = array();

		// Next we will walk through $id array to collect all filtered
		// model attributes; we will skip blank and 0 values, then we'll 
		// add collected data to our new array variable which we'll use twice - 
		// for products and for pages filtering.
		if (count($id) > 1) {
			foreach ($id as $k => $filter) {
				if ($k <= 0 || $filter <= 0) {
					continue;
				} // skip category

				$a[$k] = Attribute::find($filter)->toArray();
			}
		}

		// Queries for products model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden and future products, and limits.
		$p['products'] = Product::whereHas('categories', function($q) use ($category_id, $siteId, $c) {
				$q->where('categories_id', $c, $category_id)->where('sites_id', '=', $siteId);
			})->where(function($q) use ($a) {
				if (count($a) > 0) {
					foreach ($a as $filter) {
						$q->whereHas('attributes', function($query) use ($filter) {
							$query->where('name', '=', $filter['name'])->where('val', '=', $filter['val']);
						});
					}
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}))->checked()
			->orderBy($queryParams['sort'], $queryParams['direction'])
			->take($queryParams['take'])
			->skip($queryParams['skip'])
			->get();

		// Queries for pages model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden, and limits.               
		$p['pages'] = Page::whereHas('categories', function($q) use ($category_id, $siteId, $c) {
				$q->where('categories_id', $c, $category_id)->where('sites_id', '=', $siteId);
			})->where(function($q) use ($a) {
				if (count($a) > 0) {
					foreach ($a as $filter) {
						$q->whereHas('attributes', function($query) use ($filter) {
							$query->where('name', '=', $filter['name'])->where('val', '=', $filter['val']);
						});
					}
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->excludeHidden()
			->orderBy('created_at', 'desc')
			->take($queryParams['take_pages'])
			->skip($queryParams['skip_pages'])
			->get();

		//
		return $p;
	}
	
}
