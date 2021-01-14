@extends('layouts.app')

@section('content')
    <prediction-component :user="{{ Auth::user() }}"></prediction-component>    
@endsection