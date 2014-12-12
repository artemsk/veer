@extends($template.'.layout.base')

@section('body')
    <section class="slide">
        <div class="content">
            <h2 class="site-name">Veer</h2>
			<p>version {{ $app['veer']->statistics['version'] }} <br/>
			queries {{ $app['veer']->statistics['queries'] }} | loading time {{ $app['veer']->statistics['loading'] }} | memory 
			{{ $app['veer']->statistics['memory'] }}</p>
			<p><a href="http://bolshaya.net">bolshaya.net</a></p>
        </div>
    </section>
@stop
