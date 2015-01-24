@extends($template.'.layout.base')

@section('body')
    <section class="slide-2">
        <div class="content container">
			<div class="row"><div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
{{ Form::open(array('route' => 'user.cart.update')) }}

<button type="submit" name="action" value="update" class="btn btn-default btn-block">Update</button>
<button type="submit" name="action" value="order" class="btn btn-default btn-block">Make order</button>
{{ Form::close() }}
				</div></div>
		</div>
	</section>
@stop