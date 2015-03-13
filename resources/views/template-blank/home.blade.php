@extends($template.'.layout.base')

@section('body')
    <section class="slide">
        <div class="content">
            <h2><span class="site-name">veer</span></h2>
			<p class="engine-info">{{ $app['veer']->statistics['version'] }} | 
			time {{ $app['veer']->statistics['loading'] }} | memory 
                        {{ $app['veer']->statistics['memory'] }} | created by <a href="http://bolshaya.net">bolshaya.net</a></p>
        </div>
    </section>
@stop
