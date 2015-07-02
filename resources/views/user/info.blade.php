<section class="section swatch-white-black has-top" id="about">
    <div class="decor-top">
        <svg class="decor" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L100 0 L100 100" stroke-width="0"></path>
        </svg>
    </div>
    <div class="container">
        <div class="row">
            <ul class="list-unstyled row box-list">
                <li class="col-md-4 os-animation" data-os-animation="fadeInUp" data-os-animation-delay=".0s">
                    <div class="box-round flat-shadow">
                        <div class="box-dummy"></div>
                            <a id='uid-{{ $user->id }}-box' href='javascript:void(0)'>
                                <figure class="box-inner">
                                    <img class="svg-inject" width=144px
                                        src='{{ $user->pri_photo_large or "assets/images/logo.png" }}'
                                        alt={{ $user->name }}
                                    />
                                    <figcaption class="box-caption">
                                        <h4>Since</h4><p>{{$user->created_at}}</p>
                                    </figcaption>
                                </figure>
                            </a>
                    </div>
                    <h3 class="text-center">
                        <a id='uid-{{ $user->id }}-name' href='javascript:void(0)'>{{$user->name}}</a>
                        @if (isset($user->slogan)&&!empty($user->slogan))
                            <small class="block">
                                {{ $user->slogan or 'famous!'}}
                            </small>
                        @endif
                    </h3>
                    <p class="text-center ">
                        {{ $user->email }} | uid:
                        {{ $user->id }}
                        {{ $user->opt_out? '(opt-out)':'' }}

                    @if (isset($user->bio)&&!empty($user->bio))
                        {{ $user->bio}}
                    @endif
                    </p>
                    <ul class="list-inline text-center social-icons social-simple">
                        <li>
                            <a id='uid-{{ $user->id }}-accounts' href='javascript:void(0)'>
                                <i class="fa fa-user"></i>
                            </a>
                        </li>
                        @foreach( $accounts as $account )
                        <li>
                            <a id='uid-{{ $user->id }}-accounts-{{ $account->provider }}' href='javascript:void(0)'>
                                <i class='fa fa-{{ $account->provider }}'></i>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</section>

<div id='user-accounts-div'></div>

<script>

    var ids = [ 'uid-{{ $user->id }}-accounts',
                'uid-{{$user->id}}-name',
                'uid-{{$user->id}}-box'
    ];

    setAjaxById(
         ids ,  // id
        'accounts'                     ,  // route
        'user-accounts-div'            ); // div_id

 @foreach( $accounts as $account )
    setAjaxById(
        'uid-{{ $user->id }}-accounts-{{ $account->provider }}', // id
        'accounts_p/{{$account->provider}}', // route
        'user-accounts-div'); // div_id
@endforeach

</script>

