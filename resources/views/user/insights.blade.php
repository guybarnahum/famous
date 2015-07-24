<section>
@if (isset($insights))
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <table class="table table-hover" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Trait</th>
                            <th>Value</th>
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
                                {{ $insight->value }}
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


