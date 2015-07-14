
@if (!isset($mode))
    @if( $mode  = 'normal' ) @endif
@endif

@if (!isset($action))

    @if ( $need_action = false  ) @endif
    @if ( $fire_on_load= false  ) @endif
    @if ( $class       = 'none' ) @endif

@else

    @if (!is_array( $action ))
        @if ( $action = base64_decode( $action ) ) @endif
        @if ( $action = (array)json_decode( $action ) ) @endif
    @endif

    @if ( $need_action = true) @endif
    @if ( $fire_on_load= ( isset( $action[ 'fire_on_load' ]) &&
                                  $action[ 'fire_on_load' ]   )) @endif

    @if ( $class = $action[ 'class-prefix' ] ) @endif
    @if ( $div   = $action[ 'div'          ] ) @endif
    @if ( $route = isset( $action[ 'route' ] )? $action[ 'route' ] : '' ) @endif
    @if ( $query = isset( $action[ 'query' ] )? $action[ 'query' ] : '' ) @endif

@endif

@if ( $uid = $user->id ) @endif

<section>
    <ul class="list-unstyled box-list">

        <li>

        <!--
            <li class="col-md-4 os-animation"
                 data-os-animation="fadeInUp"
                 data-os-animation-delay=".0s"
              >
        -->

           @if ($mode == 'normal' )
            <div class="box-round flat-shadow">
        @elseif ( $mode == 'basic')
            <div class="box-round flat-shadow" style="width:75px;">
        @else
            <div class="box-round flat-shadow">
        @endif
                <div class="box-dummy">
                </div>
                <a class='{{ $class }}-uid-{{ $uid }}-box' href='javascript:void(0)'>
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
                <a class='{{ $class }}-uid-{{ $uid }}-name' href='javascript:void(0)'>
                    {{ $user->name }}
                </a>
                @if (isset($user->slogan)&&!empty($user->slogan))
                    <small class="block">
                        {{ $user->slogan or 'famous!'}}
                    </small>
                @endif
            </h3>
            <p class="text-center ">
                {{ $user->email }} | uid: {{ $uid }}
                {{ $user->opt_out? '(opt-out)':'' }}

            @if (isset($user->bio)&&!empty($user->bio))
                {{ $user->bio}}
            @endif

            </p>

            <ul class="list-inline text-center social-icons social-simple">
                <li>
                    <a class='{{ $class }}-uid-{{ $uid }}-accounts' href='javascript:void(0)'>
                        <i class="fa fa-user"></i>
                    </a>
                </li>

                @foreach( $user->providers as $provider => $value )
                <li>
                    <a class='{{ $class }}-uid-{{ $uid }}-{{ $provider }}' href='javascript:void(0)'>
                        <i class='fa fa-{{ $provider }}'></i>
                    </a>
                </li>
                @endforeach

            </ul>

@elseif ( $mode == 'basic' )
            <h5 class="text-center">
                <a class='{{ $class }}-uid-{{ $uid }}-name' href='javascript:void(0)'>
                    {{ $user->name }}
                </a>
                    {{ $user->email }} | uid: {{ $uid }}
            </h5>
@endif
        </li>
    </ul>
</section>

@if ($need_action)

<script>

var selectors = [   '.{{ $class }}-uid-{{ $uid }}-accounts',
                    '.{{ $class }}-uid-{{ $uid }}-name',
                    '.{{ $class }}-uid-{{ $uid }}-box'
                ];

setAjaxById( selectors, '/{{ $route }}?{{ $query }}', '{{ $div }}' );

@foreach( $user->providers as $provider => $value )

setAjaxById('.{{ $class }}-uid-{{ $uid }}-{{ $provider }}'                  , // id
            '/{{ $route }}/{{ $uid }}/{{ $provider }}?{{ $query }}'  , // route
            '{{ $div }}'                                                    ); // div_id

@endforeach

@if ( $fire_on_load )
    onreadyAjax( '/{{ $route }}/{{ $uid }}?{{ $query }}','{{ $div }}' );
@endif

</script>

@else
    <!-- No action provided for user.info -->
@endif
