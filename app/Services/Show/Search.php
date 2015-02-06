<?php namespace Veer\Services\Show;

class Search {

	/**
	 * Query Builder: 
	 * 
	 * - who: Search Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | search/{id} or $_POST
	 */
	public function searchStoreQuery($siteId, $id, $queryParams)
	{
		$p = null;

		$q = explode(' ', $queryParams['q']);

		$field = $queryParams['search_field_product'];

		$p['products'] = Product::siteValidation($siteId)
			->whereNested(function($query) use ($q, $field) {
				foreach ($q as $word) {
					$query->where(function($queryNested) use ($word, $field) {
						$queryNested->where($field, '=', $word)
						->orWhere($field, 'like', $word . '%%%')
						->orWhere($field, 'like', '%%%' . $word)
						->orWhere($field, 'like', '%%%' . $word . '%%%');
					});
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->checked()
			->orderBy($queryParams['sort'], $queryParams['direction'])
			->take($queryParams['take'])
			->skip($queryParams['skip'])
			->get();

		$field = $queryParams['search_field_page'];

		$p['pages'] = Page::siteValidation($siteId)
			->whereNested(function($query) use ($q, $field) {
				foreach ($q as $word) {
					$query->where(function($queryNested) use ($word, $field) {
						$queryNested->where($field, '=', $word)
						->orWhere($field, 'like', $word . '%%%')
						->orWhere($field, 'like', '%%%' . $word)
						->orWhere($field, 'like', '%%%' . $word . '%%%');
					});
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->excludeHidden()
			->orderBy('created_at', 'desc')
			->take($queryParams['take_pages'])
			->skip($queryParams['skip_pages'])
			->get();
		return $p;
	}
}
