@if ( !isset($providers) || !is_string($providers) )
    @if ( $providers = '' ) @endif
@endif

@if ( $active    = explode( ',', $providers ) ) @endif

@if ( $providers = ['facebook'=>'blue' ,
                    'twitter' =>'cyan' ,
                    'linkedin'=>'red'  ,
                    'google'  =>'blue' ] ) @endif

<ul class="inline navbar navbar-right social-icons social-background social-small">

    @foreach( $providers as $provider => $color )
        <li>
        @if ( in_array( $provider .':active' , $active ) )
            <a href="/logoutProvider/{{ $provider }}"
                data-toggle="tooltip" data-placement="bottom"
                title="logout from {{ $provider }}!">
                    <i class="fa fa-{{ $provider }}" style="color:{{ $color }};"></i>
        @else
            <a href="/login/{{ $provider }}"
               data-toggle="tooltip" data-placement="bottom"
               title="login to {{ $provider }}!">
                <i class="fa fa-{{ $provider }}"></i>
        @endif
            </a>
        </li>
    @endforeach

    <li>
        <img class='img-circle' width=64px
@if ( isset( $photo) && $photo && !empty( $photo ))
            src="{{ $photo }}" />
@else
            src="{{ asset('assets/images/user.png') }}" />
@endif
    </li>
    <li>
    @if ( isset( $photo) && $photo && !empty( $photo ))
        <a href='/logout'
            data-toggle="tooltip" data-placement="bottom" title="logout!">
    @else
        <a href='javascript:void(0)' style='opacity:0.2;'>
    @endif
            <i class="fa fa-sign-out"></i>
        </a>
    </li>
</ul>
