@extends($template.'.layout.base')

@section('body')
    <section class="slide-2">
        <div class="content container">
			<div class="row"><div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
<form method="POST" action="{{ route('user.cart.update') }}" accept-charset="UTF-8">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<button type="submit" name="action" value="update" class="btn btn-default btn-block">Update</button>
<button type="submit" name="action" value="order" class="btn btn-default btn-block">Make order</button>
</form>
				</div></div>
		</div>
	</section>
@stop