@if ( !isset($providers) || !is_string($providers) )
    @if ( $providers = '' ) @endif
@endif

@if ( $providers = explode( ',', $providers ) ) @endif

<ul class="inline navbar navbar-right social-icons social-background social-small">
    <li>
    @if ( in_array( 'facebook:active', $providers ) )
        <a href="/logoutProvider/facebook">
            <i class="fa fa-facebook" style="color:blue;"></i>
    @else
        <a href="/login/facebook">
            <i class="fa fa-facebook"></i>
    @endif
        </a>
    </li>
    <li>
    @if ( in_array( 'twitter:active', $providers ) )
        <a href="/logoutProvider/twitter">
            <i class="fa fa-twitter" style="color:cyan;"></i>
    @else
        <a href="/login/twitter">
            <i class="fa fa-twitter"></i>
    @endif
        </a>
    </li>
    <li>
    @if ( in_array( 'linkedin:active', $providers ) )
        <a href="/logoutProvider/linkedin">
            <i class="fa fa-linkedin" style="color:red;"></i>
    @else
        <a href="/login/linkedin">
            <i class="fa fa-linkedin"></i>
    @endif
        </a>
    </li>
    <li>
    @if ( in_array( 'google:active', $providers ) )
        <a href="/logoutProvider/google">
            <i class="fa fa-google" style="color:blue;"></i>
    @else
        <a href="/login/google">
            <i class="fa fa-google"></i>
    @endif
        </a>
    </li>

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
        <a href='/logout'>
    @else
        <a href='javascript:void(0)' style='opacity:0.2;'>
    @endif
            <i class="fa fa-sign-out"></i>
        </a>
    </li>
</ul>
