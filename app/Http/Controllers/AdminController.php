<?php

namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input as Input;

class AdminController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');

        $this->middleware('auth.admin');

        app('veer')->loadedComponents['template'] = app('veer')->template                     = $this->template
            = config('veer.template-admin');

        app('veer')->isBoundSite = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view(app('veer')->template.'.dashboard',
            array(
            "template" => app('veer')->template
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $t
     */
    public function show($t)
    {
        if (Input::has('SearchField')) {
            $search = ( new \Veer\Services\Show\Search)->searchAdmin($t);

            if (is_object($search)) {
                return $search;
            }
        }

        $filters = array(Input::get('filter') => Input::get('filter_id'));

        if (in_array($t,
                array("categories", "pages", "products", "users", "orders")))
                $t = $this->checkOnePageEntities($t);

        $show = $this->getRouteParams($filters);

        $view = $t == "lists" ? "userlists" : $t;

        if (array_key_exists($t, $show)) {
            $className = '\Veer\Services\Show\\'.$show[$t][0];

            $items = ( new $className)->{$show[$t][1]}($show[$t][2]);
        } elseif ($t == "restore") {

            app('veeradmin')->restore(Input::get('type'), Input::get('id'));
            return back();
        } else {
            list($items, $view) = $this->{'showAdmin'.ucfirst($t)}($t);
        }

        if (is_object($items)) $items->fromCategory = Input::get('category');

        if (isset($items)) return $this->sendViewOrJson($items, $view);
    }

    protected function sendViewOrJson($items, $view)
    {
        if (null != Input::get('json')) return response()->json($items);

        return view($this->template.'.'.$view,
            array(
            "items" => $items,
            "template" => $this->template
        ));
    }

    protected function checkOnePageEntities($t)
    {
        $check = $t == "categories" ? Input::get('category') : Input::get('id');

        return empty($check) ? $t : str_singular($t);
    }

    protected function getRouteParams($filters)
    {
        return array(
            "sites" => ["Site", "getSites", null],
            "categories" => ["Category", "getAllCategories", Input::get('image')],
            "category" => ["Category", "getCategoryAdvanced", Input::get('category')],
            "pages" => ["Page", "getAllPages", $filters],
            "page" => ["Page", "getPageAdvanced", Input::get('id')],
            "products" => ["Product", "getAllProducts", $filters],
            "product" => ["Product", "getProductAdvanced", Input::get('id')],
            "images" => ["Image", "getImages", $filters],
            "attributes" => ["Attribute", "getUngroupedAttributes", null],
            "tags" => ["Tag", "getTagsWithoutSite", null],
            "downloads" => ["Download", "getDownloads", null],
            "users" => ["User", "getAllUsers", $filters],
            "user" => ["User", "getUserAdvanced", Input::get('id')],
            "books" => ["UserProperties", "getBooks", $filters],
            "lists" => ["UserProperties", "getLists", $filters], // userlists view
            "searches" => ["UserProperties", "getSearches", $filters],
            "communications" => ["UserProperties", "getCommunications", $filters],
            "comments" => ["UserProperties", "getComments", $filters],
            "roles" => ["UserProperties", "getRoles", $filters],
            "orders" => ["Order", "getAllOrders", $filters],
            "order" => ["Order", "getOrderAdvanced", Input::get('id')],
            "bills" => ["OrderProperties", "getBills", $filters],
            "discounts" => ["OrderProperties", "getDiscounts", $filters],
            "shipping" => ["OrderProperties", "getShipping", $filters],
            "payment" => ["OrderProperties", "getPayment", $filters],
            "statuses" => ["OrderProperties", "getStatuses", null],
            "configuration" => ["Site", "getConfiguration", Input::get('site')],
            "components" => ["Site", "getComponents", Input::get('site')],
            "secrets" => ["Site", "getSecrets", null],
            "jobs" => ["Site", "getQdbJobs", $filters],
            //"etc"
        );
    }

    protected function showAdminEtc($t)
    {
        return array(
            app('veeradmin')->{'show'.ucfirst($t)}(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $t
     * @return Response
     */
    public function update($t)
    {
        if (Input::has('SearchField')) return $this->show($t);

        $f = "update".strtoupper($t[0]).substr($t, 1);

        $data = app('veeradmin')->{$f}();

        if (!app('request')->ajax() && !(app('veeradmin')->skipShow)) {
            return $this->show($t);
        }

        return $data;
    }
}
