<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf_token" content="{{ csrf_token() }}" />
        <title>
            @section('title')
            @show
        </title>

        <!-- fav icons -->

        <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/favicons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/favicons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/favicons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/favicons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/favicons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/favicons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/favicons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/assets/images/favicons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicons/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicons/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/assets/images/favicons/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <link rel="shortcut icon" type="image/x-icon" href="/assets/images/favicons/favicon.ico" >
        <link rel="shortcut icon" type="image/png"    href="/assets/images/favicons/favicon.png" >

        <!-- iPhone 4 Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114"
            href="/assets/images/favicons/apple-icon-114x114.png" >

        <!-- iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72"
            href="/assets/images/favicons/apple-icon-72x72.png" >

        <!-- iPhone: -->
        <link rel="apple-touch-icon-precomposed"
            href="/assets/images/favicons/apple-icon-60x60.png" >

        <!-- css -->

        <link href="http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,400italic" rel='stylesheet'>

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

        tr.clickable{
            cursor:pointer;
        }

        </style>

        <!-- scripts -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>

        if ( typeof jQuery === 'undefined' ){
            document.write( unescape('%3Cscript%20src%3D%22/path/to/your/scripts/jquery-2.1.4.min.js%22%3E%3C/script%3E'));
        }

        function ajax_html(e)
        {
            // alert( 'ajax_html:' + e.data.url + ',div:' + e.data.div );
            
            $.ajax( e.data.url,
                   {
                        type: 'POST',
                        context: { div:e.data.div },
                       
                        beforeSend: function (xhr) {
                           var token = $('meta[name="csrf_token"]').attr('content');
                           if (token){
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                           }
                        },
                   
                        success:function(data){
                            // alert('sucess:' + this.div );
                            $(this.div).html(data);
                        },
                   
                        error:function(){
                            // alert('failure:' + this.div );
                            $(this.div).html('Failed to load data');
                        }
                   }); //end of ajax
        }

        function build_postAjax( route, div_id )
        {
            // since $.ready does not accept args, we build dynamic
            // no argument function from args!
            return function(){
                        var e = { data: {url: route, div: div_id } };
                        ajax_html( e );
                    };
        }

        function onreadyAjax( route, div_id )
        {
            div_id = '#' + div_id;

            if (jQuery.isReady){
                build_postAjax( route, div_id )();
            }
            else{
                $(document).ready( build_postAjax( route, div_id ) );
            }
        }

        function setAjax( id, route, div_id )
        {
            $(id).click({url: route, div: div_id }, ajax_html );
        }

        function setAjaxById( id, route, div_id )
        {
            if (id instanceof Array){
                for (ix = 0; ix < id.length; ++ix) {
                    setAjaxById(id[ix],route,div_id);
                }
            }
            else{
                if (typeof(route )==='undefined') route  = id;
                
                if (typeof(div_id)==='undefined') div_id = '#' + id + '_div';
                else                              div_id = '#' + div_id;
                
                selector  = '#' + id;

                setAjax(selector, route, div_id );
            }
        }

        </script>
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
                @if (isset($user->providers['facebook']))
                    <a href="logout_p/facebook">
                        <i class="fa fa-facebook" style="color:blue;"></i>
                @else
                    <a href="login/facebook">
                        <i class="fa fa-facebook"></i>
                @endif
                    </a>
                </li>
                <li>
                @if (isset( $user->providers['twitter'] ))
                    <a href="logout_p/twitter">
                        <i class="fa fa-twitter" style="color:cyan;"></i>
                @else
                    <a href="login/twitter">
                        <i class="fa fa-twitter"></i>
                @endif
                    </a>
                </li>
                <li>
                @if (isset($user->providers['linkedin']))
                    <a href="logout_p/linkedin">
                        <i class="fa fa-linkedin" style="color:red;"></i>
                @else
                    <a href="login/linkedin">
                        <i class="fa fa-linkedin"></i>
                @endif
                    </a>
                </li>
                <li>
                @if (isset($user->providers['google']))
                    <a href="logout_p/google">
                        <i class="fa fa-google" style="color:blue;"></i>
                @else
                    <a href="login/google">
                        <i class="fa fa-google"></i>
                @endif
                    </a>
                </li>

                <li>
                @if ( isset($user)&&!empty( $user ))
                    <img class='img-circle' width=64px
                        src='{{ $user->pri_photo_large or "assets/images/user.png" }}'
                        alt='{{ $user->name }}'/>
                @else
                    <img class='img-circle' width=64px
                        src="assets/images/user.png"
                        alt='famous'/>
                @endif
                </li>
                <li>
                @if ( isset($user)&&!empty( $user ))
                    <a href='logout'>
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
