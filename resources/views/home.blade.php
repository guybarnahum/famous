@extends('layouts.master' )

@section('header')
    @include( 'header', [ 'user'  => isset( $user )? $user : false ] )
@endsection

@section('content')

    @if( isset( $msg ) && $msg && !empty( $msg )  )
        @include( 'message.info', [ 'title'=> (isset($title)? $title:null),
                                    'msg'  => $msg ] )
    @endif

@if( false )

    @if ( isset($user) && $user && !empty( $user ) )
        @if ( $us = [ $user, $user, $user,$user, $user, $user,$user, $user, $user, $user ] ) @endif
        @include( 'user.select', [ 'us' => $us ] )
    @endif

@endif

    @if ( isset($user) && $user && !empty( $user ) )
        <section class="section swatch-white-black has-top">
            <div class="decor-top">
                <svg class="decor" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 100 L100 0 L100 100" stroke-width="0"></path>
                </svg>
            </div>
        </section>
        @include('user.active',[ 'user' => $user ] )
    @endif

@endsection
