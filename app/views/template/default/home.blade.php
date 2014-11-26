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
                   <button type="button" class="btn btn-default dropdown-toggle-menu" data-container="body" data-toggle="popover" data-placement="left" data-content='{{ Form::open(array('route' => 'search.store')) }}<div class="input-group">
                   <input type="text" class="form-control" name="q">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Go!</button>
      </span>
               </div>{{ Form::close() }}' data-html="true" data-title="Поиск по товарам">
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
             
             @if (isset($output['products'][0]))
             @include('template.default.elements.productSmallCardOne', array('product'=>$output['products'][0]))
             
             <div class="rowdelimiter-small"></div>
             @endif
             
             @if (isset($output['products'][4]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][4]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][8]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][8]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][12]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][12]))
             @endif 
         </div>

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             
             @if (isset($output['products'][1]))
             @include('template.default.elements.productSmallCardOne', array('product'=>$output['products'][1]))
             
             <div class="rowdelimiter-small"></div>
             @endif
             
             @if (isset($output['products'][5]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][5]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][9]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][9]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][13]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][13]))
             @endif 
         </div>   

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             
             @if (isset($output['products'][2]))
             @include('template.default.elements.productSmallCardOne', array('product'=>$output['products'][2]))
             
             <div class="rowdelimiter-small"></div>
             @endif
             
             @if (isset($output['products'][6]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][6]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][10]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][10]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][14]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][14]))
             @endif 
         </div>

         <div class="rowdelimiter-small visible-xs-block"></div>

         <div class="col-sm-6">
             
             @if (isset($output['products'][3]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][3]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][7]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][7]))
             
             <div class="rowdelimiter-small"></div>
             @endif

             @if (isset($output['products'][11]))
             @include('template.default.elements.productSmallCardTwo', array('product'=>$output['products'][11]))
             @endif          
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