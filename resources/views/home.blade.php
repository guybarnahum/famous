@extends('layouts.master', ['user' => isset($user)? $user:null ])

@section('content')

    @if(!isset($user))
        @if ( $warn='No one is logged in') @endif
        @if ( $msg=isset($msg)? $msg.'<br>'.$warn : $warn ) @endif
    @endif

    @if(isset($msg))
        @include('message.info', array('title'=> (isset($title)? $title:null), 'msg'=>$msg))
    @endif

    @if (isset($user))
        @include('user.info',array('user'=>$user))
        @include('user.accounts', array('accounts' => $user->accounts))
    @endif

@endsection
