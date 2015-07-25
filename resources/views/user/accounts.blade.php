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
                <h2 style='display:inline;'>Accounts</h2>
                <a  class='collapsable' href='javascript:void(0);'>
                        <i class="fa fa-caret-down"></i>
                </a>
                <br><br>
                <div class='collapsee'>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>uid</th>
                                <th>email/username</th>
                                <th>state</th>
                                <th>token</th>
                                <th>photo</th>
                                <th>facts</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $accounts as $account )
                            <tr>
                                <td style='vertical-align:middle;'>
                                    <a class='uid-{{ $account->uid }}-{{$account->provider}}-facts'
                                        href='javascript:void(0)'>
                                        <i class="fa fa-{{$account->provider}}"></i>
                                    </a>
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
                                                token:<a href='#?token={{$account->access_token}}' style='text-align:left'>
                                                {{ $account->access_token }}
                                                </a>
                                            </li>
                                            <li style='font-size:xx-small'>
                                            expiration {{ \App\Components\DateTimeUtils::nice_time($account->expired_at) }}
                                            </li>
                                            @if (!empty($account->scope_request))
                                            <li style='font-size:xx-small'>
                                                scope:{{ $account->scope_request}}
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                                <td style='vertical-align:middle;'>
                                    <img class='img-circle' width=96px
                                                src='{{ $account->avatar or "assets/images/logo.png" }}'
                                                alt={{ $account->name }}/>
                                    <br>

@if ( $size = \App\Components\PhotoUtils::getSize($account->avatar)) @endif
@if ( is_array($size) && (count($size) > 1))
<h5><small>{{ $size[0] }} x {{ $size[1] }} px</small></h5>
@endif
                                </td>
                                <td style='vertical-align:middle;align=center;'>
                                    <a class='btn btn-default uid-{{ $account->uid }}-{{$account->provider}}-mine-facts'
                                        href='javascript:void(0)' role='button'>
                                        fact(or)&nbsp;<i class="fa fa-{{$account->provider}}"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<style>

/* Set the size and font of the tab widget */
.tabGroup {
//    font: 10pt arial, verdana;
//    width: 800px;
//    height: 400px;
}
 
/* Configure the radio buttons to hide off screen */
.tabGroup > input[type="radio"] {
//    position: absolute;
//    left:-100px;
//    top:-100px;
    display:none;
}

/* Configure labels to look like tabs */
.tabGroup > input[type="radio"] + label {
    /* inline-block such that the label can be given dimensions */
    display: inline-block;
 
    /* A nice curved border around the tab */
    border: 1px solid black;
    border-radius: 10px 10px 0 0;
    -moz-border-radius: 10px 10px 0 0;
    -webkit-border-radius: 10px 10px 0 0;
     
    /* the bottom border is handled by the tab content div */
    border-bottom: 0;
    margin-bottom: 0;
 
    /* Padding around tab text */
    padding: 5px 20px 0px 20px;
 
    /* Set the background color to default gray (non-selected tab) */
    background-color:#ddd;
}
 
/* Focused tabs need to be highlighted as such */
.tabGroup > input[type="radio"]:focus + label {
    background-color:#ddd;
//    border:1px dashed black;
}
 
/* Checked tabs must be white with the bottom border removed */
.tabGroup > input[type="radio"]:checked + label {
    background-color:white;
    font-weight: bold;
    border-bottom: 1px solid white;
    margin-bottom: -1px;
}
 
/* The tab content must fill the widgets size and have a nice border */
.tabGroup > div {
    display: none;
    border-top: 1px solid black;
    background-color: white;
    padding: 10px 10px;
    height: 100%;
    overflow: auto;
     
//    box-shadow: 0 0 20px #444;
//    -moz-box-shadow: 0 0 20px #444;
//    -webkit-box-shadow: 0 0 20px #444;
     
//    border-radius: 0 5px 5px 5px;
//    -moz-border-radius: 0 5px 5px 5px;
//    -webkit-border-radius: 0 5px 5px 5px;
}
 
/* This matchs tabs displaying to thier associated radio inputs */
.tab1:checked ~ .tab1, .tab2:checked ~ .tab2, .tab3:checked ~ .tab3 {
    display: block;
}

</style>

<section class="section swatch-white-black has-top">

    <div class="decor-top">
        <svg class="decor hidden-xs hidden-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 100 L2 60 L4 100 L6 60 L8 100 L10 60 L12 100 L14 60 L16 100 L18 60 L20 100 L22 60 L24 100 L26 60 L28 100 L30 60 L32 100 L34 60 L36 100 L38 60 L40 100 L42 60 L44 100 L46 60 L48 100 L50 60 L52 100 L54 60 L56 100 L58 60 L60 100 L62 60 L64 100 L66 60 L68 100 L70 60 L72 100 L74 60 L76 100 L78 60 L80 100 L82 60 L84 100 L86 60 L88 100 L90 60 L92 100 L94 60 L96 100 L98 60 L100 100 Z"
        stroke-width="0"></path>
        </svg>
        <svg class="decor visible-xs visible-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 100 L5 60 L10 100 L5 60 L10 100 L15 60 L20 100 L25 60 L30 100 L35 60 L40 100 L45 60 L50 100 L55 60 L60 100 L65 60 L70 100 L75 60 L80 100 L85 60 L90 100 L95 60 L100 100"></path>
        </svg>
    </div>

    <div class="tabGroup">
        <input type="radio" name="tabGroup1" id="rad1" class="tab1" checked="checked"/>
        <label for="rad1"><h2>Facts</h2></label>
     
        <input type="radio" name="tabGroup1" id="rad2" class="tab2"/>
        <label for="rad2"><h2>Insights</h2></label>

        <input type="radio" name="tabGroup1" id="rad3" class="tab3"/>
        <label for="rad3"><h2>Reports</h2></label>

        <br/>

        <div class="tab1">
            <div id='user-accounts-facts-div' >
                @include( 'message.progress' )
            </div>

        </div>
        <div class="tab2">
            <div id='user-accounts-insights-div' >
                @include( 'message.progress' )
            </div>
        </div>
        <div class="tab3">
            <div id='user-accounts-reports-div' >
                @include( 'message.progress' )
            </div>
        </div>

    </div>

</section>

<script>

@foreach( $accounts as $account )


setAjaxById('.uid-{{ $account->uid }}-{{$account->provider}}-facts', // id
            'mine/facts/{{ $account->uid }}/{{$account->provider}}', // route
            'user-accounts-facts-div'); // div_id

setAjaxById(
            '.uid-{{ $account->uid }}-{{$account->provider}}-mine-facts', // id
            'mine/facts/{{ $account->uid }}/{{$account->provider}}', // route
            false); // div_id

@endforeach

@if ( count( $accounts) > 1 )

onreadyAjax( '/get/facts/{{ $accounts[0]->uid }}', // route
             'user-accounts-facts-div');// div_id

@elseif ( count( $accounts ) == 1 )

onreadyAjax( 'get/facts/{{ $accounts[0]->uid }}/{{ $accounts[0]->provider }}', // route
             'user-accounts-facts-div');// div_id

@endif


onreadyAjax( 'get/insights/{{ $accounts[0]->uid }}', // route
            'user-accounts-insights-div');// div_id

onreadyAjax( 'get/reports/{{ $accounts[0]->uid }}', // route
            'user-accounts-reports-div');// div_id

</script>
