@extends('layouts.app')

@section('content')
    <prediction-component v-if="{{ Auth::user() }}" :user="{{ Auth::user() }}"></prediction-component>
    <prediction-component v-else :user="{{ Auth::user() }}"></prediction-component>    
@endsection