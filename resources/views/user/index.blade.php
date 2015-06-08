@extends('app')

@section('title') User Dashboard @stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                </div>
                <div class="panel-body">

                    @if (isset($errors) && (count( $errors) > 0 ) )
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong>
                            There were some problems with your input.
                            <br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table>
                        <tr>
                            <td><b>{{ $user->name }}</b></td>
                            <td><b><image src='{{$user->pri_photo_large}}'></b></td>
                        </tr>
                        <tr>
                            <td>{{ $user->email }} since {{$user->created_at}} </td>
                        </tr>

                        @if (isset( $user->accounts ) )
                        <tr>
                            <td>
                                @foreach( $user->accounts as $act )
                                <table>
                                    <tr>
                                        <td>{{$act->provider}}</td>
                                        <td><image src='{{$act->avatar}}'></td>
                                    </tr>
                                    <tr>
                                        <td>{{$act->name}} / {{$act->email}} </td>
                                    </tr>
                                </table>
                                @endforeach
                            <td>
                        </tr>
                        @endif

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection