<div class="block-stndrt">
    <div class="cs-thumbnail-img"><a href="{{ $product['link'] }}"><img src="{{ $product['img'] }}" class="img-responsive img-responsive-custom" ></a></div>
    <div class="block-stndrt-footer"><a href="{{ $product['category_link'] }}">{{ $product['category'] }}</a></div>
    <h4><a href="{{ $product['link'] }}">{{ $product['title'] }}</a></h4>
    <h5>{{ $product['price'] }}</h5>
    <div class="basket">{{ $product['basket'] }}</div>                 
</div>