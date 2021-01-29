@extends('layouts.app')

@section('content')
  <div class="container-fluid mt-5">
    <h4>Logs</h4>
    <div class="table-responsive">
      <table class="table">
        <tr>
          <th>sn</th>
          <th>Image</th>
          <th>status</th>
          <th>Processed Data</th>
          <th>Date</th>
        </tr>
        @forelse ($logs as $log)
          <tr>
            <td> {{ $loop->iteration }}</td>
            <td> <a href="{{ $log->image_url }}" target="_blank">Image</a> </td>
            <td> {{ $log->status }} </td>
            <td> {{ $log->processed_data}} </td>
            @if (!empty($log->processed_data))
              <td> {{ date('F j, Y, g:i a', strtotime($log->processed_at)) }} </td>
            @else
              <td> {{ date('F j, Y, g:i a', strtotime($log->created_at)) }} </td>  
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="4" align="center"> No Records yet</td>
          </tr>
        @endforelse
      </table>
    </div>
  </div>
@endsection