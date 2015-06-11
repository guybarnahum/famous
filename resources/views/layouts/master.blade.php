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

    </head>

<body>
    <header id="masthead" class="navbar navbar-sticky swatch-{{ $color or "red" }}-white" role="banner">

        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".main-navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="#">
                    <image src="{{ asset('assets/images/logo.png') }}" alt='famous'>
                    famous
                </a>
            </div>

            <nav class="collapse navbar-collapse main-navbar" role="navigation">

                <ul class="inline navbar-right social-icons social-background social-small">
                    <li>
                        <a href="login/facebook">
                        <i class="fa fa-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a href="login/twitter">
                        <i class="fa fa-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a href="login/linkedin">
                        <i class="fa fa-linkedin"></i>
                        </a>
                    </li>

                    <li>
                        <a href="login/google">
                        <i class="fa fa-google-plus"></i>
                        </a>
                    </li>
                    <li class="">
                        <a href='logout'>out</a>
                    </li>
                </ul>
<!--
                <ul class="nav navbar-nav ">
                </ul>
-->
            </nav>
        </div>

    </header>

    <!-- content -->
    @yield('content')

    <!-- footer -->
    <footer id="footer" role="contentinfo">
    </footer>

    <!-- go top -->

    <a class="go-top" href="javascript:void(0)">
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
