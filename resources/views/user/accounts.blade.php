<section class="section swatch-black-white has-top">
    <div class="decor-top">
        <svg class="decor hidden-xs hidden-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L2 60 L4 100 L6 60 L8 100 L10 60 L12 100 L14 60 L16 100 L18 60 L20 100 L22 60 L24 100 L26 60 L28 100 L30 60 L32 100 L34 60 L36 100 L38 60 L40 100 L42 60 L44 100 L46 60 L48 100 L50 60 L52 100 L54 60 L56 100 L58 60 L60 100 L62 60 L64 100 L66 60 L68 100 L70 60 L72 100 L74 60 L76 100 L78 60 L80 100 L82 60 L84 100 L86 60 L88 100 L90 60 L92 100 L94 60 L96 100 L98 60 L100 100 Z"
            stroke-width="0"></path>
        </svg>
        <svg class="decor visible-xs visible-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L5 60 L10 100 L5 60 L10 100 L15 60 L20 100 L25 60 L30 100 L35 60 L40 100 L45 60 L50 100 L55 60 L60 100 L65 60 L70 100 L75 60 L80 100 L85 60 L90 100 L95 60 L100 100"></path>
        </svg>
    </div>
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>uid</th>
                            <th>email/username</th>
                            <th>state</th>
                            <th>access</th>
                            <th>photo</th>
                            <th></th>
                        </tr>
                    </thead>
                    @foreach( $accounts as $account )
                        <tbody>
                            <tr>
                                <td style='vertical-align:middle;'>
                                    <i class="fa fa-{{$account->provider}}"></i>
                                </td>
                                <td style='vertical-align:middle;'>{{ $account->provider_uid }}</td>
                                <td style='vertical-align:middle;'>
                                    {{ $account->username or $account->email }}
                                </td>
                                <td style='vertical-align:middle;'>{{ $account->state }}</td>
                                <td style='vertical-align:middle;'>
                                    <div class="btn-group">
                                        <button type="button"
                                                class="btn btn-default dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false">
                                                {{ substr( $account->access_token, 0, 8) }}...
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" style='position:absolute;z-index:1' role="menu">
                                            <li style='font-size:xx-small'>
                                                <a href='#?token={{$account->access_token}}'>
                                                {{ $account->access_token }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td style='vertical-align:middle;'>
                                    <img class='img-circle' width=96px
                                                src='{{ $account->avatar or "assets/images/logo.png" }}'
                                                alt={{ $account->name }}
                                            />
                                </td>
                                <td style='vertical-align:middle;'>
                                    <i class="fa fa-{{$account->provider}}"></i>
                                    <h6><small>width x height</small></h6>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</section>