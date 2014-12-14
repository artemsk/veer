@extends($template.'.layout.base')

@section('body')
    <section class="slide">
        <div class="content">
            <h2 class="site-name">Veer</h2>
			<p>version {{ $app['veer']->statistics['version'] }} <br/>
			time {{ $app['veer']->statistics['loading'] }} | memory 
			{{ $app['veer']->statistics['memory'] }}</p>
			<p>created by <a href="http://bolshaya.net">bolshaya.net</a></p>
        </div>
    </section>
@stop
