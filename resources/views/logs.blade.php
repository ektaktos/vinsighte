@extends('layouts.app')

@section('content')
  <div class="">
    <logs-component :joblogs="{{ $logs }}" :pendingjobs="{{ $pendingJobs }}"></logs-component>
  </div>
@endsection