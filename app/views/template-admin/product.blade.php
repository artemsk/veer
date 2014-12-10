@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}"><strong>Products</strong></a></li>
</ol>
<h1>Product</h1>
<br/>
<div class="container">

<p>{{ $items->id }}</p>
<p>{{ $items->url }}</p>
<p>{{ $items->grp }}</p>
<p>{{ $items->status }}</p>
<p>{{ $items->qty }}</p>
<p>{{ $items->weight }}</p>
<p>{{ $items->title }}</p>
<p>{{ $items->descr }}</p>
<p>{{ $items->production_code }}</p>
<p>{{ $items->grp_ids }}</p>
<p>{{ $items->to_show }}</p>
<p>{{ $items->currency }}</p>
<p>{{ $items->price }}</p>
<p>{{ $items->price_sales }}</p>
<p>{{ $items->price_opt }}</p>
<p>{{ $items->price_base }}</p>
<p>{{ $items->price_sales_on }}</p>
<p>{{ $items->price_sales_off }}</p>
<p>{{ $items->score }}</p>
<p>{{ $items->star }}</p>
<p>{{ $items->download }}</p>
<p>{{ $items->ordered }}</p>
<p>{{ $items->viewed }}</p>
<p>{{ $items->created_at }}</p>
<p>{{ $items->updated_at }}</p>
	
<!--
subproducts
parentproducts
pages
categories
tags
attributes
images
orders
comments
downloads
userlists
communications
-->
	
</div>
@stop