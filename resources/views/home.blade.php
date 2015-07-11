@extends('layouts.master', [ 'user'  => isset( $user )? $user : false ] )

@section('content')

    @if( isset( $msg ) && $msg && !empty( $msg )  )
        @include( 'message.info', [ 'title'=> (isset($title)? $title:null),
                                    'msg'  => $msg ] )
    @endif

    @if ( isset($user_select) && $user_select && !empty( $user_select ) )
        @include( 'user.select', [ 'us' => $user_select ] )
    @endif

    @if ( isset($user) && $user && !empty( $user ) )
        @include('user.info',[ 'user' => $user ] )
    @endif

@endsection
