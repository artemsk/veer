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

        exec(base_path()."/wkhtmltoimage.exe http://bolshaya.net ".config('veer.images_path')."/site-1.jpg");

        exec(base_path()."/wkhtmltoimage-amd64 ".config('veer.images_path')."/site-2.jpg");

        exec(base_path()."/wkhtmltoimage-i386 ".config('veer.images_path')."/site-3.jpg");

        return redirect()->route('admin.show', 'sites');
        
        /*return view(app('veer')->template.'.dashboard',
            array(
            "template" => app('veer')->template
        ));*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $t
     */
    public function show($t)
    {
        $specialRoute = $this->specialRoutes($t);
        
        if(!empty($specialRoute)) return $specialRoute;

        if (in_array($t,
                array("categories", "pages", "products", "users", "orders")))
                $t = $this->checkOnePageEntities($t);

        $view = $t == "lists" ? "userlists" : $t;

        $items = $this->getItems($t);

        if (isset($items)) return $this->sendViewOrJson($items, $view);
    }

    /**
     * special routes: search or restore
     */
    protected function specialRoutes($t)
    {
        if (Input::has('SearchField')) {
            $search = ( new \Veer\Services\Show\Search)->searchAdmin($t);

            if (is_object($search)) return $search;
        }

        if ($t == "restore") {
            app('veeradmin')->restore(Input::get('type'), Input::get('id'));
            return back();
        }
    }

    /**
     * get Items
     */
    protected function getItems($t)
    {
        $show = $this->getRouteParams(array(Input::get('filter') => Input::get('filter_id')));

        if (array_key_exists($t, $show)) {
            $className = '\Veer\Services\Show\\'.$show[$t][0];

            return ( new $className)->{$show[$t][1]}($show[$t][2]);
        }

        return $this->{'showAdmin'.ucfirst($t)}($t);
    }

    /**
     * send response - view or simple json
     */
    protected function sendViewOrJson($items, $view)
    {
        if (null != Input::get('json')) return response()->json($items);

        return view($this->template.'.'.$view,
            array(
            "items" => $items,
            "template" => $this->template
        ));
    }

    /**
     * check entities which have separate page for single entity
     */
    protected function checkOnePageEntities($t)
    {
        $check = $t == "categories" ? Input::get('category') : Input::get('id');

        return empty($check) ? $t : str_singular($t);
    }

    /**
     * configure administration routes
     */
    protected function getRouteParams($filters)
    {
        return array(
            "sites" => ["Site", "getSites", null],
            "categories" => ["Category", "getAllCategories", Input::get('image')],
            "category" => ["Category", "getCategoryAdvanced", Input::get('category')],
            "pages" => ["Page", "getAllPages", [$filters, [Input::get('sort') => Input::get('sort_direction')]]],
            "page" => ["Page", "getPageAdvanced", Input::get('id')],
            "products" => ["Product", "getAllProducts", [$filters, [Input::get('sort') => Input::get('sort_direction')]]],
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

    /**
     * soon to be deprecated - show etc. page
     */
    protected function showAdminEtc($t)
    {
        return app('veeradmin')->{'show'.ucfirst($t)}(array(
                Input::get('filter') => Input::get('filter_id'),
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $t
     * @return Response
     */
    public function update($t)
    {
        if (Input::has('SearchButton')) return $this->show($t);

        $f = "update".strtoupper($t[0]).substr($t, 1);

        $data = $t == "configuration" ? (new \Veer\Services\Administration\Configuration())->handle() : app('veeradmin')->{$f}();

        if (!app('request')->ajax() && !(app('veeradmin')->skipShow)) {
            return $this->show($t);
        }

        return $data;
    }
}
