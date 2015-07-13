
@include('user.info',[ 'user' => $user, 'action' => 'load-acct' ] )

<div id='user-accounts-div'>
</div>

<script>

var ids = [ 'load-acct-uid-{{ $user->id }}-accounts',
            'load-acct-uid-{{ $user->id }}-name',
            'load-acct-uid-{{ $user->id }}-box'
];

setAjaxById(
     ids ,  // id
    '/accounts'                    ,  // route
    'user-accounts-div'            ); // div_id

@foreach( $user->providers as $provider => $value )

setAjaxById(
        'load-acct-uid-{{ $user->id }}-{{ $provider }}', // id
        '/accounts/{{ $user->id }}/{{ $provider }}', // route
        'user-accounts-div'); // div_id

@endforeach

onreadyAjax( '/accounts/{{ $user->id }}', 'user-accounts-div' );

</script>

