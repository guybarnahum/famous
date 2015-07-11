@extends('layouts.master', [ 'user'  => isset( $user )? $user : null, ] )

@section('content')

    @if(isset($msg))
        @include( 'message.info', [ 'title'=> (isset($title)? $title:null),
                                    'msg'  => $msg ] )
    @endif

    @if (isset($user))
        @include('user.info',[ 'user' => $user ] )
    @endif

@endsection
