
@include('user.info',[ 'user' => $user, 'action' => [
                                            'class-prefix' => 'load-acct',
                                            'div'          => 'user-accounts-div',
                                            'route'        => 'accounts',
                                            'fire_on_load' => true
                                         ]
                     ] )

<div id='user-accounts-div'>
    @include( 'message.progress' )
</div>
