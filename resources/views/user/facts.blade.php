<section class="section swatch-white-black has-top">
    <div class="decor-top">
        <svg class="decor hidden-xs hidden-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L2 60 L4 100 L6 60 L8 100 L10 60 L12 100 L14 60 L16 100 L18 60 L20 100 L22 60 L24 100 L26 60 L28 100 L30 60 L32 100 L34 60 L36 100 L38 60 L40 100 L42 60 L44 100 L46 60 L48 100 L50 60 L52 100 L54 60 L56 100 L58 60 L60 100 L62 60 L64 100 L66 60 L68 100 L70 60 L72 100 L74 60 L76 100 L78 60 L80 100 L82 60 L84 100 L86 60 L88 100 L90 60 L92 100 L94 60 L96 100 L98 60 L100 100 Z"
            stroke-width="0"></path>
        </svg>
        <svg class="decor visible-xs visible-sm" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L5 60 L10 100 L5 60 L10 100 L15 60 L20 100 L25 60 L30 100 L35 60 L40 100 L45 60 L50 100 L55 60 L60 100 L65 60 L70 100 L75 60 L80 100 L85 60 L90 100 L95 60 L100 100"></path>
        </svg>
    </div>
    @if (isset($facts))
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
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
                        @if ( $fact_name = '' ) @endif

                        @foreach( $facts as $fact )

                            @if ( $fact->fct_name != $fact_name )

                                @if ( $fact_num  = 0  ) @endif
                                <tr class='collapsable clickable expanded'>
                                    <td style='width:30px;' ><i class="fa fa-caret-down"></i></td>
                                    <td style='vertical-align:middle;width:50px;'>
                                        {{$fact->fct_name}}
                                    </td>
                                    <td style='width:150px;'></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                @if ( $fact_name = $fact->fct_name ) @endif

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
                                        uid.{{$fact->uid}}:act.{{$fact->act_id}}
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

$('.collapsable').click(function(){
                $(this).nextUntil('.collapsable').toggle();
                $(this).toggleClass('expanded');
                
                expanded_html  = '<i class="fa fa-caret-down"></i>';
                collapsed_html = '<i class="fa fa-caret-right"></i>';
                        
                if ($(this).hasClass('expanded')) {
                    $(this).find("td:nth-child(1)").html(expanded_html);
                } else {
                    $(this).find("td:nth-child(1)").html(collapsed_html);
                }
            });
</script>

