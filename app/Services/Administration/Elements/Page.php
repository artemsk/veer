<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Page extends Entity {
    
    public function __construct()
    {
        parent::__construct();
        $this->type = 'page';
    }
            
    public function run()
    {
        if(!empty($this->id)) return $this->updateOne();   
        
        $this->quickPagesActions($this->action); 
        
        if(!empty($this->title)) $this->quickAdd();
        
        if($this->action == 'sort') return $this->sortPages();
    }
    
    protected function quickAdd()
    {
        $this->data += ['url' => '', 'txt' => '', 'categories' => null];
        
        // TODO: test preg:
        $txt= preg_replace("/{{(?s).*}}/", "", $this->data['txt'], 1);
        preg_match("/{{(?s).*}}/", $this->data['txt'], $small);
        
        $fill = [
            'title' => trim($this->title),
            'url' => $this->data['url'],
            'hidden' => 1,
            'manual_order' => 99999,
            'users_id' => \Auth::id(),
            'small_txt' => !empty($small[0]) ? substr(trim($small[0]), 2, -2) : '',
            'txt' => trim($txt)
        ];
        
        $page = $this->create($fill);        
        
        $categories = explode(',', $this->data['categories']);
        if(!empty($categories)) {
            $page->categories()->attach($categories);
        }

        // images
        if(Input::hasFile('uploadImage')) {
            $this->upload('image', 'uploadImage', $page->id, 'pages', 'pg', null);
        }

        //files
        if(Input::hasFile('uploadFile')) {
            $this->upload('file', 'uploadFile', $page->id, $page, 'pg', null);
        }
        
        event('veer.message.center', trans('veeradmin.page.new'));
    }
            
    protected function prepareData()
    {
        $fill = array_get($this->data, 'fill', []);
        
        foreach(['original', 'show_small', 'show_comments', 'show_title', 'show_date', 'in_list'] as $field) {
            $fill[$field] = isset($fill[$field]) ? 1 : 0; 
        }
        
        $fill['users_id'] = empty($fill['users_id']) ? \Auth::id() : $fill['users_id'];
        $fill['url'] = trim($fill['url']); 
        return $fill;
    }   
    
    protected function sortPages()
    {
        $url_params = array_get($this->data, '_refurl');
        $parse_str = $this->data;

        if(!empty($url_params)) {
            parse_str(starts_with($url_params, '?') ? substr($url_params, 1) : $url_params, $parse_str);   
            if(!empty($parse_str['page'])) \Input::merge(['page' => $parse_str['page']]);
        }
        
        $parse_str += ['filter' => null, 'filter_id' => null, 'sort' => null, 'sort_direction' => null, 'page' => 1];

        $pages = new \Veer\Services\Show\Page;
        $oldsorting = $pages->getAllPages([
            [$parse_str['filter'] => $parse_str['filter_id']],
            [$parse_str['sort'] => $parse_str['sort_direction']]
        ]);
        
        if (is_object($oldsorting)) {
            $bottom_sort = $oldsorting[count($oldsorting) - 1]->manual_order;               
            $sort = $oldsorting[0]->manual_order;
            foreach($this->sortElementsEntities($oldsorting, $this->data) as $id) {
                if($sort < $bottom_sort && $parse_str['sort_direction'] == 'desc') { $sort = $bottom_sort; }
                if($sort > $bottom_sort && $parse_str['sort_direction'] == 'asc') { $sort = $bottom_sort; }
                \Veer\Models\Page::where('id', '=', $id)->update(['manual_order' => $sort]);
                if($parse_str['sort_direction'] == 'desc') { $sort--; } else { $sort++; }
            }
        }
    }    
}
