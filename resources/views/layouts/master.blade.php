
@if ( $style=array( 'facebook' =>'',
                    'twitter'  =>'',
                    'linkedin' =>'',
                    'google'   =>'' ) ) @endif

@if ( $url=array( 'facebook' =>'login',
                   'twitter'  =>'login',
                   'linkedin' =>'login',
                   'google'   =>'login' ) ) @endif

@if (isset($accounts))
    @foreach( $accounts as $account )
        @if     ( $account[ 'provider'] == 'facebook' )
            @if ( $style[ 'facebook' ] = 'color:blue;' ) @endif
        @elseif ( $account[ 'provider'] == 'twitter'  )
            @if ( $style[ 'twitter' ] = 'color:cyan;' ) @endif
        @elseif ( $account[ 'provider'] == 'linkedin'  )
            @if ( $style[ 'linkedin'] = 'color:red;' ) @endif
        @elseif ( $account[ 'provider'] == 'google'  )
            @if ( $style[ 'google'  ] = 'color:blue;' ) @endif
        @endif

        @if ( $url[ $account[ 'provider' ] ] = 'logout_p' ) @endif

    @endforeach
@endif

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            @section('title')
            @show
        </title>

        <!-- fav icons -->

        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicons/favicon.ico') }}" >
        <link rel="icon" type="image/png"    href="{{ asset('assets/images/favicons/favicon.png') }}" >

        <!-- iPhone 4 Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114"
            href="{{ asset('assets/images/favicons/apple-touch-icon-114x114-precomposed.png') }}" >

        <!-- iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72"
            href="{{ asset('assets/images/favicons/apple-touch-icon-72x72-precomposed.png') }}" >

        <!-- iPhone: -->
        <link rel="apple-touch-icon-precomposed"
            href="{{ asset('assets/images/favicons/apple-touch-icon-60x60-precomposed.png') }}" >

        <!-- css -->

        <link href="{{ asset('http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,400italic') }}" rel='stylesheet'>

        <link href="{{ asset('assets/css/bootstrap.min.css')           }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/theme.min.css')               }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/color-defaults.min.css')      }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-beige-black.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-black-beige.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-black-white.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-black-yellow.min.css') }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-blue-white.min.css')   }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-green-white.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-red-white.min.css')    }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-white-black.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-white-blue.min.css')   }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-white-green.min.css')  }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-white-red.min.css')    }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/swatch-yellow-black.min.css') }}" rel='stylesheet'>
        <link href="{{ asset('assets/css/fonts.min.css')}}" media='screen' rel='stylesheet'>

        <!-- styles should be incorporated or reused from theme.css -->

        <style>
        .va-middle ul{
            position: relative;
            top: 50%;
            -webkit-transform: translateY(50%);
            -ms-transform: translateY(50%);
            transform: translateY(50%);
        }
        </style>
    </head>
<body>
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
                    <a href="{{ $url['facebook'] }}/facebook"><i class="fa fa-facebook" style="{{ $style['facebook' ]}}"></i></a>
                </li>
                <li>
                    <a href="{{ $url['twitter'] }}/twitter"><i class="fa fa-twitter"   style="{{ $style['twitter' ]}}"></i></a>
                </li>
                <li>
                    <a href="{{ $url['linkedin'] }}/linkedin"><i class="fa fa-linkedin" style="{{ $style['linkedin' ]}}"></i></a>
                </li>
                <li>
                    <a href="{{ $url['google'] }}/google"><i class="fa fa-google-plus" style="{{ $style['google' ]}}"></i></a>
                </li>

                @if ( isset($user)&&!empty($user ))
                <li>
                    <img class='img-circle' width=64px
                         src='{{ $user->pri_photo_large or "assets/images/logo.png" }}'
                          alt={{ $user->name }} />
                </li>
                <li>
                    <a href="logout"><i class="fa fa-sign-out"></i></a>
                </li>
                @endif
            </ul>
            </nav>
        </div>
    </header>

    <!-- content -->
    @yield('content')

    <!-- footer -->
    <footer id="footer" role="contentinfo">
    </footer>

    <!-- go top -->

    <a class="go-top hex-alt" href="javascript:void(0)">
        <i class="fa fa-angle-up"></i>
    </a>

    <!-- scripts are placed here -->

    <script src="{{ asset('assets/js/packages.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.min.js')    }}"></script>
    <script src='https://maps.googleapis.com/maps/api/js?sensor=false'></script>
    <script src="{{ asset('assets/js/map.min.js')      }}" ></script>
    <script src='https://www.google.com/jsapi'             ></script>

    <!-- more scripts.. -->
    <script type="text/javascript">
        @yield('scripts')
    </script>

</body>
</html>
