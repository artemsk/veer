<?
if(!is_object($rating)) { $rating=new ratings; }

$prd_lst=$rating->random("attr",LIMIT_PRD_RANDOM);
if(count($prd_lst['prds'])>0) { $prd_lst2=implode("' OR ".DB_PREFIX."products.nnn='",$prd_lst['prds']);
if(!is_object($cats)) { $cats=new categories(); }
if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
if(!is_object($showprd)) { $showprd=new products(); }
$prd_rec=$showprd->collect_products($prd_lst2,'nnn','1');
$templ_prd11=$showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'],1,1);
$templ_prd3['used']=$templ_prd11['used'];
}
if(@$templ_prd11['templ_prd']!="") { 
    $out['{RANDOM_PRDS_1}']="<div class=\"randoms_name\">".$prd_lst['zag']."</div>".@$templ_prd11['templ_prd']; $templ_prd11['templ_prd']=""; }


// 2
$prd_lst=$rating->random("keyword",LIMIT_PRD_RANDOM);
if(count($prd_lst['prds'])>0) { $prd_lst2=implode("' OR ".DB_PREFIX."products.nnn='",$prd_lst['prds']);
if(!is_object($cats)) { $cats=new categories(); }
if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
if(!is_object($showprd)) { $showprd=new products(); }
$prd_rec=$showprd->collect_products($prd_lst2,'nnn','1');
$templ_prd11=$showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'],1,1);
$templ_prd3['used']=$templ_prd11['used'];
}
if(@$templ_prd11['templ_prd']!="") {
$out['{RANDOM_PRDS_2}']="<div class=\"randoms_name\">".$prd_lst['zag']."</div>".@$templ_prd11['templ_prd']; $templ_prd11['templ_prd']=""; }



//3
$prd_lst=$rating->random("manuf",LIMIT_PRD_RANDOM);
if(count($prd_lst['prds'])>0) { $prd_lst2=implode("' OR ".DB_PREFIX."products.nnn='",$prd_lst['prds']);
if(!is_object($cats)) { $cats=new categories(); }
if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
if(!is_object($showprd)) { $showprd=new products(); }
$prd_rec=$showprd->collect_products($prd_lst2,'nnn','1');
$templ_prd11=$showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'],1,1);
$templ_prd3['used']=$templ_prd11['used'];
}
if(@$templ_prd11['templ_prd']!="") {
$out['{RANDOM_PRDS_3}']="<div class=\"randoms_name\">".$prd_lst['zag']."</div>".@$templ_prd11['templ_prd']; $templ_prd11['templ_prd']=""; }
?>