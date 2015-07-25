@if ( isset($user_list) && is_array($user_list) && count($user_list) )

    <section>
<!--
        <div class="container">
            <div class="row-fluid">
                <div class="span12">
-->
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td class="container">
                                    <div id='selected-user-div'>
            @if ( $action = [
                 'class-prefix' => 'load-acct',
                 'div'          => 'user-accounts-div',
                 'route'        => 'get/accounts',
                 'fire_on_load' => true
                 ] ) @endif

            @if ($query_action = json_encode( $action )) @endif
            @if ($query_action = base64_encode( $query_action )) @endif

            @include( 'user.info', [ 'user'=>$user_list[0],'action'=>$action ])

                                    </div>
                                </td>
                                <td class="container">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                @foreach( $user_list as $ix => $user )
                                                    @if ( $ix % 3 == 0 )
                                                    </tr><tr>
                                                    @endif

                                                    <td class="container">
                                                        <div 'user-list-{{ $ix }}'>

            @if ( $uid = $user->id ) @endif
            @if ( $action = [
                 'class-prefix' => 'select',
                 'div'          => 'selected-user-div',
                 'route'        => 'get/user/' . $uid,
                 'query'        => 'action=' . $query_action,
                 'fire_on_load' => false
                 ] ) @endif

            @include( 'user.info',  [ 'user'   => $user     ,
                                      'mode'   => 'basic'   ,
                                      'action' => $action
                                    ])
                                                        </div>
                                                    </td>

                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
<!--
                </div>
            </div>
        </div>
-->
    </section>

<div id='user-accounts-div'>
    @include( 'message.progress' )
</div>


@endif