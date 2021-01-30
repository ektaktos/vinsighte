@extends('layouts.app')

@section('content')
  <div class="container-fluid mt-5">
    <h4>Logs</h4>
    <logs-component :jobLogs="{{ $logs }}" :pendingJobs="{{ $pendingJobs }}"></logs-component>
  </div>
@endsection