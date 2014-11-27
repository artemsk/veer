<?
if(!is_object(@$rating)) { $rating=new ratings; }

$prd_lst=$rating->pop("random");

if(count($prd_lst['prds'])>0) {

if(!is_object($cats)) { $cats=new categories(); }
if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']

if(!is_object($showprd)) { $showprd=new products(); }

$templ_prd99=$showprd->product_listing($prd_lst['prds'], $look, '0', @$templ_prd3['used'],1,LIMIT_PRD_POP);

$templ_prd3['used']=$templ_prd99['used'];

}

$out['{POP_PRDS}']=@$templ_prd99['templ_prd'];
?>