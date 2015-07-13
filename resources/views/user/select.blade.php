@if ( isset($us) && is_array($us) && count($us) )
    <section>
        <div class="container">
            <div class="row-fluid">
                <div class="span12">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td style="width=100%;">
                                    @include( 'user.info',  [ 'user'  => $us[ 0 ] ] )
                                </td>
                                <td>
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                @foreach( $us as $ix => $user )

                                                    @if ( $ix % 3 == 0 )
                                                    </tr><tr>
                                                    @endif

                                                    <td>
                                                        <div style="width=200px; height:150px; overflow:hidden">
                                                            @include( 'user.info',  [ 'user'  => $us[ $ix ], 'mode' => 'basic' ] )
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
                </div>
            </div>
        </div>
    </section>

    <div id='selected-user-div'>
    </div>
    </section>
@endif