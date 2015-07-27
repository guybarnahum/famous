<section>
@if (isset($facts))
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <table style='width:100%;'>
                    <tbody>
                          <tr>
                            <td width='100%'>
                                <a class='btn btn-default uid-mine-facts-me'
                                    href='javascript:void(0)' role='button'>
                                    <i class="fa fa-user"></i>
                                    Regenrate facts

                                </a>
                            </td>
                            <td nowrap>
                                <a class='btn btn-default uid-mine-facts-me-facebook'
                                    href='javascript:void(0)' role='button'>
                                        <i class="fa fa-facebook"></i>
                                </a>
                            </td>
                            <td nowrap>
                                <a class='btn btn-default uid-mine-facts-me-twitter'
                                    href='javascript:void(0)' role='button'>
                                        <i class="fa fa-twitter"></i>
                                </a>
                            </td>
                            <td nowrap>
                                <a class='btn btn-default uid-mine-facts-me-linkedin'
                                    href='javascript:void(0)' role='button'>
                                        <i class="fa fa-linkedin"></i>
                                </a>
                            </td>
                            <td nowrap>
                                <a class='btn btn-default uid-mine-facts-me-google'
                                    href='javascript:void(0)' role='button'>
                                        <i class="fa fa-google"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='6' width='100%' height='40px;'>
                                <div id='uid-mine-fact-state' >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>
                <table class="table table-hover" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <th style='width:30px;'></th>
                            <th style='width:50px;'>fact</th>
                            <th style='width:150px;'>subject</th>
                            <th>object</th>
                            <th>type</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ( $fct_name_last = '' ) @endif

                    @foreach( $facts as $fact )

                       @if ( $fct_name = $fact->fct_name ) @endif
                       @if ( !empty($fact->fct_type) )
                            @if ( $fct_name .= '.' . $fact->fct_type ) @endif
                       @endif

                       @if ( $fct_name != $fct_name_last )

                            @if ( $fact_num  = 0  ) @endif
                            <tr class='collapsable clickable expanded'>
                                <td style='width:30px;' ><i class="fa fa-caret-down"></i></td>
                                <td style='vertical-align:middle;width:50px;'>
                                    {{ $fct_name }}
                                </td>
                                <td style='width:150px;'></td>
                                <td></td>
                                <td></td>
                            </tr>

                            @if ( $fct_name_last = $fct_name ) @endif

                        @endif

                        @if ( $fact_num == 16 )
                            <tr class='collapsable clickable'>
                                <td style='width:30px;'><i class="fa fa-caret-right"></i></td>
                                <td style='width:50px;'><b>More..<b></td>
                                <td style='width:150px;'></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif

                        @if ( $fact_num >= 16 )
                            <tr style="display:none;">
                        @else
                            <tr>
                        @endif
                                <td style='width:30px;'></td>
                                <td style='width:50px;'></td>
                                <td style='vertical-align:middle;width:150px;'>
                                    uid.{{ $fact->uid }}:act.{{ $fact->act_id }}
                                </td>

                        <td style='vertical-align:middle; width:200px; word-wrap:break-word;'>
                            {{$fact->obj_name}}
                        </td>

                                <td style='vertical-align:middle;'>

                                    @if ( $str = $fact->obj_id_type . ':' .
                                          $fact->obj_provider_id ) @endif

                                    @if ( strlen( $str ) > 32 )
                                        {{ substr($str,0,32)}}...
                                    @else
                                        {{        $str      }}
                                    @endif
                                </td>
                            </tr>

                        @if ( $fact_num++ ) @endif

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    No facts found!
@endif

</section>

<script>

setAjaxById( '.uid-mine-facts-me'         , 'mine/facts/me'         , 'uid-mine-fact-state');
setAjaxById( '.uid-mine-facts-me-facebook', 'mine/facts/me/facebook', 'uid-mine-fact-state');
setAjaxById( '.uid-mine-facts-me-linkedin', 'mine/facts/me/linkedin', 'uid-mine-fact-state');
setAjaxById( '.uid-mine-facts-me-twitter' , 'mine/facts/me/twitter' , 'uid-mine-fact-state');
setAjaxById( '.uid-mine-facts-me-google'  , 'mine/facts/me/google'  , 'uid-mine-fact-state');

</script>



