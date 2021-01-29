@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
  <h4>Search</h4>
  <p>Search term: <strong>{{ $queryString }}</strong> </p>
  <div class="table-responsive">
    <table class="table">
      <tr>
        <th>sn</th>
        <th>Image</th>
        <th>Processed Data</th>
        <th>Date</th>
      </tr>
     
      @empty($logs)
        <tr>
          <td colspan="4" align="center"> No Records yet</td>
        </tr>
      @endempty
      @foreach ($logs as $log)
        <tr>
          <td> {{ $loop->iteration }}</td>
          <td> <a href="{{ $log->image_url }}" target="_blank">Image</a> </td>
          <td> {{ $log->processed_data}} </td>
          <td> {{ date('F j, Y, g:i a', strtotime($log->processed_at)) }} </td>
        </tr>
      @endforeach
    </table>
  </div>
</div>
@endsection