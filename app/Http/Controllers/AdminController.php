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

        $show = array("attributes", "tags", "downloads",
            "configuration", "components", "secrets", "categories", "pages",
            "products", "users", "orders", "books", "lists", "searches",
            "communications", "comments", "roles", "bills", "discounts",
            "shipping", "payment", "statuses", "jobs", "etc");

        $view = $t;

        if (in_array($t, $show)) {
            list($items, $view) = $this->{'showAdmin'.ucfirst($t)}($t);
        } elseif ($t == "restore") {

            app('veeradmin')->restore(Input::get('type'), Input::get('id'));
            return back();
        } else {
            $className = '\Veer\Services\Show\\'.str_singular(ucfirst($t));

            $items = ( new $className)->{'get'.ucfirst($t)}(
                array(Input::get('filter') => Input::get('filter_id'))
            );
        }

        if (isset($items) && isset($view)) {
            return view($this->template.'.'.$view,
                array(
                "items" => $items,
                "template" => $this->template
            ));
        }
    }

    protected function showAdminAttributes($t)
    {
        return array(
            ( new \Veer\Services\Show\Attribute)->getUngroupedAttributes(),
            $t
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

    protected function showAdminTags($t)
    {
        return array(
            ( new \Veer\Services\Show\Tag)->getTagsWithoutSite(),
            $t
        );
    }
    /* 3 */

    protected function showAdminDownloads($t)
    {
        return array(
            ( new \Veer\Services\Show\Download)->getDownloads(),
            $t
        );
    }
    /* 4 */

    protected function showAdminComments($t)
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getComments(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
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

    protected function showAdminBooks($t)
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getBooks(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 6 */

    protected function showAdminLists()
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getLists(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            "userlists"
        );
    }
    /* 7 */

    protected function showAdminSearches($t)
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getSearches(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 8 */

    protected function showAdminCommunications($t)
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getCommunications(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 9 */

    protected function showAdminRoles($t)
    {
        return array(
            ( new \Veer\Services\Show\UserProperties)->getRoles(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }

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

    protected function showAdminBills($t)
    {
        return array(
            ( new \Veer\Services\Show\OrderProperties)->getBills(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 12 */

    protected function showAdminDiscounts($t)
    {
        return array(
            ( new \Veer\Services\Show\OrderProperties)->getDiscounts(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 13 */

    protected function showAdminShipping($t)
    {
        return array(
            ( new \Veer\Services\Show\OrderProperties)->getShipping(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 14 */

    protected function showAdminPayment($t)
    {
        return array(
            ( new \Veer\Services\Show\OrderProperties)->getPayment(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
        );
    }
    /* 15 */

    protected function showAdminStatuses($t)
    {
        return array(
            ( new \Veer\Services\Show\OrderProperties)->getStatuses(),
            $t
        );
    }
    /* 16 */

    protected function showAdminConfiguration($t)
    {
        return array(
            ( new \Veer\Services\Show\Site)->getConfiguration(Input::get('site')),
            $t
        );
    }
    /* 17 */

    protected function showAdminComponents($t)
    {
        return array(
            ( new \Veer\Services\Show\Site)->getComponents(Input::get('site')),
            $t
        );
    }
    /* 18 */

    protected function showAdminSecrets($t)
    {
        return array(
            $items = ( new \Veer\Services\Show\Site)->getSecrets(),
            $t
        );
    }

    protected function showAdminJobs($t)
    {
        return array(
            ( new \Veer\Services\Show\Site)->getQdbJobs(array(
                Input::get('filter') => Input::get('filter_id'),
            )),
            $t
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
    /*
     * API: Lists
     */

    public function getListOfItems($model)
    {
        if (strlen(Input::get('needle')) <= 1 && $model != 'image') return;

        $fields = array('tag' => 'name', 'attribute' => 'name', 'image' => 'id');

        $field = isset($fields[$model]) ? $fields[$model] : 'title';

        $modelFull = "\Veer\Models\\".ucfirst($model);

        $data = $modelFull::where($field, 'like',
                '%%%'.\Input::get('needle').'%%%');

        if ($model == 'attribute') $data->groupBy($field);

        return viewx(app('veer')->template.'.lists.suggestions',
            array('data' => $data->lists($model == 'image' ? 'img' : $field,
                'id'), 'model' => $model));
    }
}
