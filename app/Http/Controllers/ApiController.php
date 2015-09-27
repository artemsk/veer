<?php

namespace Veer\Http\Controllers;

use Veer\Http\Requests;
use Veer\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');

        $this->middleware('auth.admin');
    }

    /**
     * API: Lists
     */
    public function lists($model)
    {
        if(!$this->checkLength($model)) return null;

        $params = $this->paramModels($model);

        if(empty($params)) return null;

        $modelFull = "\Veer\Models\\".ucfirst($model);

        $data = $modelFull::where($params[0], 'like', '%%%'.\Input::get('needle').'%%%');

        $data = $this->additionalConditions($model, $data, $params[0]);

        return view( config('veer.template-admin') . '.lists.suggestions',
            array('data' => $data->lists($params[1], $params[2]), 'model' => $model));
    }


    protected function checkLength($model)
    {
        if (strlen(\Input::get('needle')) <= 1 && $model != 'image') return false;

        return true;
    }

    protected function paramModels($model)
    {
        $params = array(
            "attribute" => ["name", "name", "id"],
            "category" => ["title", "title", "id"],
            "download" => ["id", "fname", "id"],
            "image" => ["id", "img", "id"],
            "page" => ["title", "title", "id"],
            "product" => ["title", "title", "id"],
            "site" => ["url", "url", "id"],
            "tag" => ["name", "name", "id"]
        );

        return array_get($params, $model);
    }

    protected function additionalConditions($model, $data, $field)
    {
        if ($model == 'attribute') return $data->groupBy($field);

        return $data;
    }

}
