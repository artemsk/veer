<div class="block-big">
    <div class="big-img"><a href="{{ $product['link'] }}"><img src="{{ $product['img'] }}" class="img-responsive img-center"></a></div> 
    <div class="block-stndrt-footer"><a href="{{ $product['category_link'] }}">{{  $product['category'] }}</a></div>
    <h3><a href="{{  $product['link'] }}">{{  $product['title'] }}</a></h3>
    <h5>{{  $product['price'] }}</h5>
    <div class="basket">{{  $product['basket'] }}</div>              
</div>