<?php namespace Veer\Services\Traits;

trait SortingTraits {

	protected function replaceSortingBy($orderBy)
	{
		if (\Input::get('sort', null)) $orderBy[0] = \Input::get('sort');

		if (\Input::get('direction', null)) $orderBy[1] = \Input::get('direction');

		return $orderBy;
	}

}
