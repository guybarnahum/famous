
@include('user.info',[ 'user' => $user, 'action' => [
                                            'class-prefix' => 'load-acct',
                                            'div'          => 'user-accounts-div',
                                            'route'        => 'get/accounts',
                                            'fire_on_load' => true
                                         ]
                     ] )


<div id='user-accounts-div' ></div>
