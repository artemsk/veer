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
        $json = Input::get('json', false); // TODO: ?

        if (Input::has('SearchField')) {
            $search = ( new \Veer\Services\Show\Search)->searchAdmin($t);

            if (is_object($search)) {
                return $search;
            }
        }

        $filters = array(Input::get('filter') => Input::get('filter_id'));

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

        if (isset($items) && isset($view)) {
            return view($this->template.'.'.$view,
                array(
                "items" => $items,
                "template" => $this->template
            ));
        }
    }

    protected function getRouteParams($filters)
    {
        return array(
            "sites" => ["Site", "getSites", null],
            //"categories" => "",
            //"pages",
            //"products",
            "images" => ["Image", "getImages", $filters],
            "attributes" => ["Attribute", "getUngroupedAttributes", null],
            "tags" => ["Tag", "getTagsWithoutSite", null],
            "downloads" => ["Download", "getDownloads", null],
            //"users",
            "books" => ["UserProperties", "getBooks", $filters],
            "lists" => ["UserProperties", "getLists", $filters], // userlists view
            "searches" => ["UserProperties", "getSearches", $filters],
            "communications" => ["UserProperties", "getCommunications", $filters],
            "comments" => ["UserProperties", "getComments", $filters],
            "roles" => ["UserProperties", "getRoles", $filters],
            //"orders",
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

    protected function showAdminCategories()
    {
        $category    = Input::get('category');
        $imageFilter = Input::get('image');

        if (empty($category)) {
            $items = ( new \Veer\Services\Show\Category)->getAllCategories($imageFilter);
            $view  = "categories";
        } else {
            $items = ( new \Veer\Services\Show\Category)->getCategoryAdvanced($category);
            $view  = "category";
        }

        return array($items, $view);
    }

    protected function showAdminPages()
    {
        $page = Input::get('id');

        if (empty($page)) {
            $items = ( new \Veer\Services\Show\Page)->getAllPages(array(
                Input::get('filter') => Input::get('filter_id'),
            ));
            $view  = "pages";
        } else {
            $items = ( new \Veer\Services\Show\Page)->getPageAdvanced($page);
            $view  = "page";
        }

        if (is_object($items)) {
            $items->fromCategory = Input::get('category');
        }

        return array($items, $view);
    }

    protected function showAdminProducts()
    {
        $product = Input::get('id');

        if (empty($product)) {
            $items = ( new \Veer\Services\Show\Product)->getAllProducts(array(
                Input::get('filter') => Input::get('filter_id'),
            ));
            $view  = "products";
        } else {
            $items = ( new \Veer\Services\Show\Product)->getProductAdvanced($product);
            $view  = "product";
        }

        if (is_object($items)) {
            $items->fromCategory = Input::get('category');
        }

        return array($items, $view);
    }

    protected function showAdminUsers()
    {
        $user = Input::get('id');

        if (empty($user)) {
            $items = ( new \Veer\Services\Show\User)->getAllUsers(array(
                Input::get('filter') => Input::get('filter_id'),
            ));
            $view  = "users";
        } else {
            $items = ( new \Veer\Services\Show\User)->getUserAdvanced($user);
            $view  = "user";
        }

        return array($items, $view);
    }
    /* 5 */

    protected function showAdminOrders()
    {
        $order = Input::get('id');

        if (empty($order)) {
            $items = ( new \Veer\Services\Show\Order)->getAllOrders(array(
                Input::get('filter') => Input::get('filter_id'),
            ));
            $view  = "orders";
        } else {
            $items = ( new \Veer\Services\Show\Order)->getOrderAdvanced($order);
            $view  = "order";
        }

        return array($items, $view);
    }
    /* 11 */

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
