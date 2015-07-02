@extends('layouts.master', [ 'user'  => isset( $user )? $user : null, ] )

@section('content')

    @if(isset($msg))
        @include( 'message.info', array('title'=> (isset($title)? $title:null),
                                        'msg'  => $msg ) )
    @endif

    @if (isset($user))
        @include('user.info',array('user'=>$user))
    @endif


    @if (isset($facts))
        @include('user.facts'   , array('facts'    => $facts ))
    @endif

@endsection
