
<header id="masthead" class="navbar navbar-sticky swatch-{{ $color or "black" }}-white" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".main-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a href="javascript:void(0);"
                data-toggle="tooltip" data-placement="bottom"
                title="build:{{ \App\Components\StringUtils::getBuildVersion() }}">
                <strong>famous</strong>
            </a>
            <a href="javascript:void(0);"
                data-toggle="tooltip" data-placement="bottom"
                title="device-guid: {{\App\Components\StringUtils::getDevGuid()}}">
                <image src="{{ asset('assets/images/logo.png') }}" alt='famous' width=128>
            </a>
        </div>

        <nav class="collapse navbar-collapse main-navbar va-middle" role="navigation">
            <div id="header-widget-providers">
            </div>
        </nav>
    </div>
</header>

<script>

onreadyAjax( 'widget/providers', // route
             'header-widget-providers');// div_id

</script>