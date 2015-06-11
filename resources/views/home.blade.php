@extends('layouts.master')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
                @if (isset($user))
                <pre>
                    {{{ print_r( $user,true ) }}}
                </pre>
                @endif
			</div>
		</div>
	</div>
</div>
@endsection
