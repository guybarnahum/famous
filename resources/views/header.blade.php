
<header id="masthead" class="navbar navbar-sticky swatch-{{ $color or "black" }}-white" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".main-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a href="#">
                <strong>famous</strong>
                <image src="{{ asset('assets/images/logo.png') }}" alt='famous' width=128>
                <h5 style='font-size:xx-small'>{{ \App\Components\StringUtils::getBuildVersion() }}</h5>
                <h5 style='font-size:xx-small'>{{ \App\Components\StringUtils::getDevGuid() }}</h5>
            </a>
        </div>

        <nav class="collapse navbar-collapse main-navbar va-middle" role="navigation">
            <div id="header-user-providers">
                @if ( isset($user->providers))
                    @if ( $providers = $user->providers ) @endif
                @else
                    @if ( $providers = '' ) @endif
                @endif

                @if ( !isset( $user ) )
                    @if ( $user = false ) @endif
                @endif

                @include( 'widget.providers', [ 'user'      => $user,
                                                'providers' => $providers ] )
            </div>
        </nav>
    </div>
</header>

<script>

</script>