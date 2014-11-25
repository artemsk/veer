@extends('template.'.$template.'.layout.base')

@section('body')
    <div class="head-logo"><img src="{{ URL::asset('assets/template/'.$template.'/img/logo12.png') }}" class="img-responsive"></div>

     <!--<div class="rowdelimiter-small"></div>-->

      <div class="container-fluid" id="headeraffix">
         <div class="row">
         <div class="col-xs-24">
               <div class="tags">кэнди шоп:</div>
               <div class="tags-lnks dropdown" id="dropdown-cats">
                       <button class="btn btn-default dropdown-toggle dropdown-toggle-menu" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                           {FILTER_CATS_FIRST}
                           <span class="caret"></span>
                       </button>
                       <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">                           
                           {FILTER_CATS}
                       </ul>
               </div>
               <div class="tags-lnks dropdown" id="dropdown-fabric">
                 <button class="btn btn-default dropdown-toggle dropdown-toggle-menu" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-expanded="true">
                     {FILTER_MANUF_FIRST}
                     <span class="caret"></span>
                 </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2">
                     {FILTER_MANUF}
                 </ul>
               </div>
               <div class="tags-lnks dropdown" id="dropdown-types">
                 <button class="btn btn-default dropdown-toggle dropdown-toggle-menu" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-expanded="true">
                     {FILTER_ATTR_2_FIRST}
                     <span class="caret"></span>
                 </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu3">
                     {FILTER_ATTR_2}
                 </ul>
               </div>
               <div class="tags-lnks dropdown" id="dropdown-types2">
                 <button class="btn btn-default dropdown-toggle dropdown-toggle-menu" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-expanded="true">
                     {FILTER_ATTR_3_FIRST}
                     <span class="caret"></span>
                 </button>
                 <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu3">
                     {FILTER_ATTR_3}
                 </ul>
               </div>
               <div class="pull-right">
               <div class="tags-lnks hidden-xs"><a href="?5">о магазине</a></div>
               <div class="tags-lnks hidden-xs"><a href="?6">доставка</a></div>
               <div class="tags-lnks"><a href="tel:+79035019959">+7 (903) 501-99-59</a></div>
               <div class="tags-lnks-last">
                   <button type="button" class="btn btn-default dropdown-toggle-menu" data-container="body" data-toggle="popover" data-placement="left" data-content='<div class="input-group">
                   <input type="text" class="form-control">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button">Go!</button>
      </span>
               </div>' data-html="true" data-title="Поиск по товарам">
                       <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                   </button>
                </div>
               </div>
         </div>
     </div>
 </div>

     <div class="rowdelimiter-small"></div>
     
<div class="container">
    <div class="row"><div class="col-xs-24 text-center">
            <h2 class="welcome">Самый большой выбор сладостей в Москве<br/>
            <small>Конфеты и шоколад по доступным ценам</small></h2>
        </div></div>
    
    <div class="rowdelimiter-small"></div>
    
     <div class="row">
         <div class="col-sm-6">
             
             <div class="block-big">
                 <div class="big-img"><a href="{{ array_get($output,'products.0.link') }}"><img src="{{ array_get($output,'products.0.img') }}" class="img-responsive img-center"></a></div> 
                 <div class="block-stndrt-footer"><a href="{{ array_get($output,'products.0.category_link') }}">{{ array_get($output,'products.0.category') }}</a></div>
                 <h3><a href="{{ array_get($output,'products.0.link') }}">{{ array_get($output,'products.0.title') }}</a></h3>
                 <h5>{{ array_get($output,'products.0.price') }}</h5>
                 <div class="basket">{{ array_get($output,'products.0.basket') }}</div>              
             </div>
             
             <div class="rowdelimiter-small"></div>
             
             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{{ array_get($output,'products.0.link') }}"><img src="{{ array_get($output,'products.0.img') }}" class="img-responsive img-responsive-custom" ></a></div>
                 <div class="block-stndrt-footer"><a href="{{ array_get($output,'products.0.category_link') }}">{{ array_get($output,'products.0.category') }}</a></div>
                 <h4><a href="{{ array_get($output,'products.0.link') }}">{{ array_get($output,'products.0.title') }}</a></h4>
                 <h5>{{ array_get($output,'products.0.price') }}</h5>
                 <div class="basket">{{ array_get($output,'products.0.basket') }}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_7_NAZVLNK}"><img src="{MP_7_IMG}" class="img-responsive img-responsive-custom" ></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_7_CATLNK}">{MP_7_CAT}</a></div>
                 <h4><a href="{MP_7_NAZVLNK}">{MP_7_NAZV}</a></h4>
                 <h5>{MP_7_PRICE}</h5>
                 <div class="basket">{MP_7_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_11_NAZVLNK}"><img src="{MP_11_IMG}" class="img-responsive img-responsive-custom" ></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_11_CATLNK}">{MP_11_CAT}</a></div>
                 <h4><a href="{MP_11_NAZVLNK}">{MP_11_NAZV}</a></h4>
                 <h5>{MP_11_PRICE}</h5>
                 <div class="basket">{MP_11_BASKET}</div>                 
             </div>
         </div>

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             
             <div class="block-big">
                 <div class="big-img"><a href="{MP_1_NAZVLNK}"><img src="{MP_1_IMG}"  class="img-responsive img-center"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_1_CATLNK}">{MP_1_CAT}</a></div>
                 <h3><a href="{MP_1_NAZVLNK}">{MP_1_NAZV}</a></h3>
                 <h5>{MP_1_PRICE}</h5>
                 <div class="basket">{MP_1_BASKET}</div>                 
             </div>
             
             <div class="rowdelimiter-small"></div>
             
             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_4_NAZVLNK}"><img src="{MP_4_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_4_CATLNK}">{MP_4_CAT}</a></div>
                 <h4><a href="{MP_4_NAZVLNK}">{MP_4_NAZV}</a></h4>
                 <h5>{MP_4_PRICE}</h5>
                 <div class="basket">{MP_4_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_8_NAZVLNK}"><img src="{MP_8_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_8_CATLNK}">{MP_8_CAT}</a></div>
                 <h4><a href="{MP_8_NAZVLNK}">{MP_8_NAZV}</a></h4>
                 <h5>{MP_8_PRICE}</h5>
                 <div class="basket">{MP_8_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_12_NAZVLNK}"><img src="{MP_12_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_12_CATLNK}">{MP_12_CAT}</a></div>
                 <h4><a href="{MP_12_NAZVLNK}">{MP_12_NAZV}</a></h4>
                 <h5>{MP_12_PRICE}</h5>
                 <div class="basket">{MP_12_BASKET}</div>                 
             </div>
         </div>   

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             
             <div class="block-big">
                 <div class="big-img"><a href="{MP_2_NAZVLNK}"><img src="{MP_2_IMG}"  class="img-responsive img-center"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_2_CATLNK}">{MP_2_CAT}</a></div>
                 <h3><a href="{MP_2_NAZVLNK}">{MP_2_NAZV}</a></h3>
                 <h5>{MP_2_PRICE}</h5>
                 <div class="basket">{MP_2_BASKET}</div>                 
             </div>
             
             <div class="rowdelimiter-small"></div>
             
             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_5_NAZVLNK}"><img src="{MP_5_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_5_CATLNK}">{MP_5_CAT}</a></div>
                 <h4><a href="{MP_5_NAZVLNK}">{MP_5_NAZV}</a></h4>
                 <h5>{MP_5_PRICE}</h5>
                 <div class="basket">{MP_5_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_9_NAZVLNK}"><img src="{MP_9_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_9_CATLNK}">{MP_9_CAT}</a></div>
                 <h4><a href="{MP_9_NAZVLNK}">{MP_9_NAZV}</a></h4>
                 <h5>{MP_9_PRICE}</h5>
                 <div class="basket">{MP_9_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_5_NAZVLNK}"><img src="{MP_5_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_5_CATLNK}">{MP_5_CAT}</a></div>
                 <h4><a href="{MP_5_NAZVLNK}">{MP_5_NAZV}</a></h4>
                 <h5>{MP_5_PRICE}</h5>
                 <div class="basket">{MP_5_BASKET}</div>                 
             </div>
         </div>

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_6_NAZVLNK}"><img src="{MP_6_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_6_CATLNK}">{MP_6_CAT}</a></div>
                 <h4><a href="{MP_6_NAZVLNK}">{MP_6_NAZV}</a></h4>
                 <h5>{MP_6_PRICE}</h5>
                 <div class="basket">{MP_6_BASKET}</div>                 
             </div>

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_10_NAZVLNK}"><img src="{MP_10_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_10_CATLNK}">{MP_10_CAT}</a></div>
                 <h4><a href="{MP_10_NAZVLNK}">{MP_10_NAZV}</a></h4>
                 <h5>{MP_10_PRICE}</h5>
                 <div class="basket">{MP_10_BASKET}</div>                 
             </div>             

             <div class="rowdelimiter-small"></div>

             <div class="block-stndrt">
                 <div class="cs-thumbnail-img"><a href="{MP_5_NAZVLNK}"><img src="{MP_5_IMG}"  class="img-responsive img-responsive-custom"></a></div>
                 <div class="block-stndrt-footer"><a href="{MP_5_CATLNK}">{MP_5_CAT}</a></div>
                 <h4><a href="{MP_5_NAZVLNK}">{MP_5_NAZV}</a></h4>
                 <h5>{MP_5_PRICE}</h5>
                 <div class="basket">{MP_5_BASKET}</div>                 
             </div>             
         </div>
     </div>

 </div>

<div class="rowdelimiter-small"></div>

 <div class="container-fluid">
     <div class="row">
         <div class="col-xs-24 text-center">
             <div class="more-pages"><a href="?2" class="more-pages-lnk">еще</a></div>
         </div>
     </div>
 </div>
@stop