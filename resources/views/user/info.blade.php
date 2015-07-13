@if (!isset($action))
    @if( $action = 'none' ) @endif
@endif

@if (!isset($mode))
    @if( $mode  = 'normal'  ) @endif
@endif

<section>
    <ul class="list-unstyled row box-list">
        <li class="col-md-4 os-animation" data-os-animation="fadeInUp" data-os-animation-delay=".0s">

           @if ($mode == 'normal' )
            <div class="box-round flat-shadow">
        @elseif ( $mode == 'basic')
            <div class="box-round flat-shadow" style="width:75px;">
        @else
            <div class="box-round flat-shadow">
        @endif
                <div class="box-dummy">
                </div>
                <a id='{{ $action }}-uid-{{ $user->id }}-box' href='javascript:void(0)'>
                    <figure class="box-inner">

@if ($mode == 'normal' )
                        <img class="svg-inject" width=150px
@elseif ( $mode == 'basic')
                        <img class="svg-inject" width=75px
@else
                        <img class="svg-inject" width=150px
@endif
                            src='{{ $user->pri_photo_large or "assets/images/logo.png" }}'
                            alt={{ $user->name }}
                        />
@if ($mode == 'normal' )
                        <figcaption class="box-caption">

                            <h4>Since</h4><p>{{$user->created_at}}</p>
                        </figcaption>
@endif
                    </figure>
                </a>
            </div>

@if ($mode == 'normal' )

            <h3 class="text-center">
                <a id='{{ $action }}-uid-{{ $user->id }}-name' href='javascript:void(0)'>
                    {{ $user->name }}
                </a>
                @if (isset($user->slogan)&&!empty($user->slogan))
                    <small class="block">
                        {{ $user->slogan or 'famous!'}}
                    </small>
                @endif
            </h3>
            <p class="text-center ">
                {{ $user->email }} | uid: {{ $user->id }}
                {{ $user->opt_out? '(opt-out)':'' }}

            @if (isset($user->bio)&&!empty($user->bio))
                {{ $user->bio}}
            @endif

            </p>

            <ul class="list-inline text-center social-icons social-simple">
                <li>
                    <a id='{{ $action }}-uid-{{ $user->id }}-accounts' href='javascript:void(0)'>
                        <i class="fa fa-user"></i>
                    </a>
                </li>

                @foreach( $user->providers as $provider => $value )
                <li>
                    <a id='{{ $action }}-uid-{{ $user->id }}-{{ $provider }}' href='javascript:void(0)'>
                        <i class='fa fa-{{ $provider }}'></i>
                    </a>
                </li>
                @endforeach

            </ul>

@elseif ( $mode == 'basic' )
            <h5 class="text-center">
                <a id='{{ $action }}-uid-{{ $user->id }}-name' href='javascript:void(0)'>
                    {{ $user->name }}
                </a>
                    {{ $user->email }} | uid: {{ $user->id }}
            </h5>
@endif
        </li>
    </ul>
</section>