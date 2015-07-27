<section>
@if (isset($insights))
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <table style='width:100%;'>
                    <tbody>
                        <tr>
                            <td width='100%'>
                                <a class='btn btn-default uid-mine-insights-me'
                                    href='javascript:void(0)' role='button'>
                                    <i class="fa fa-user"></i>
                                    Regenrate insights
                                </a>
                            </td>
                        <tr>
                            <td width='100%' height='40px;'>
                                <div id='uid-mine-insights-state' >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-hover" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Trait</th>
                            <th>Value</th>
                            <th>+/-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ( $active_group = '' ) @endif

                        @foreach( $insights as $insight )
                        <tr>
                            @if ( $active_group != $insight->group )
                            @if ( $active_group  = $insight->group ) @endif
                            <td>
                                {{  $active_group }}
                            </td>
                            @else
                            <td></td>
                            @endif
                            <td style='vertical-align:middle; word-wrap:break-word;'>
                                {{ $insight->name }}
                            </td>
                            <td style='vertical-align:middle;'>
                                {{ $insight->value }}&nbsp;{{ $insight->v_units }}
                            </td>
                            <td style='vertical-align:middle;'>
                                {{ $insight->error }}&nbsp;{{ $insight->e_units }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    No insights found!
@endif

</section>

<script>

setAjaxById( '.uid-mine-insights-me', 'mine/insights/me', 'uid-mine-insights-state');

</script>

