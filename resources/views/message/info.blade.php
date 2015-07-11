
<section class="section swatch-white-black" id="home">
    <div class="container">
    <header class="section-header text-center underline">
    @if(isset($title)&&!empty($title))
        <h1 class="headline hyper hairline">{{$title}}</h1>
    @endif
    @if ( isset($msg) && $msg && !empty($msg) )
        <div class="alert alert-info" role="alert">{{ $msg }}</div>
    @endif
    </header>
    </div>
</section>