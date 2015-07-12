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
                <h5 style='font-size:xx-small'>{{ \App\Components\StringUtils::get_version() }}</h5>
            </a>
        </div>

        <nav class="collapse navbar-collapse main-navbar va-middle" role="navigation">
        <ul class="inline navbar navbar-right social-icons social-background social-small">
            <li>
            @if (isset($user->providers['facebook']))
                <a href="/logout_p/facebook">
                    <i class="fa fa-facebook" style="color:blue;"></i>
            @else
                <a href="/login/facebook">
                    <i class="fa fa-facebook"></i>
            @endif
                </a>
            </li>
            <li>
            @if (isset( $user->providers['twitter'] ))
                <a href="/logout_p/twitter">
                    <i class="fa fa-twitter" style="color:cyan;"></i>
            @else
                <a href="/login/twitter">
                    <i class="fa fa-twitter"></i>
            @endif
                </a>
            </li>
            <li>
            @if (isset($user->providers['linkedin']))
                <a href="/logout_p/linkedin">
                    <i class="fa fa-linkedin" style="color:red;"></i>
            @else
                <a href="/login/linkedin">
                    <i class="fa fa-linkedin"></i>
            @endif
                </a>
            </li>
            <li>
            @if (isset($user->providers['google']))
                <a href="/logout_p/google">
                    <i class="fa fa-google" style="color:blue;"></i>
            @else
                <a href="/login/google">
                    <i class="fa fa-google"></i>
            @endif
                </a>
            </li>

            <li>
            @if ( isset($user) && $user && !empty( $user ))
                <img class='img-circle' width=64px
                    src="{{ $user->pri_photo_large or asset(assets/images/user.png) }}"
                    alt="{{ $user->name }}"/>
            @else
                <img class='img-circle' width=64px
                    src="{{ asset('assets/images/user.png') }}"
                    alt='famous'/>
            @endif
            </li>
            <li>
            @if ( isset($user) && $user && !empty( $user ))
                <a href='/logout'>
            @else
                <a href='javascript:void(0)' style='opacity:0.2;'>
            @endif
                    <i class="fa fa-sign-out"></i>
                </a>
            </li>
        </ul>
        </nav>
    </div>
</header>