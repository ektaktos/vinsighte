@extends('layouts.app')

@section('content')
    <prediction-component></prediction-component>
    {{-- <prediction-component v-else :user="{{ Auth::user() }}"></prediction-component>     --}}
@endsection