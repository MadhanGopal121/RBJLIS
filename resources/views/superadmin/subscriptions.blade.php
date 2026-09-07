@extends('layouts.app')

@section('title', 'Lab Subscriptions - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Laboratory Subscriptions & Validity</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Lab Name</th>
              <th>Email</th>
              <th>Subscription Start</th>
              <th>Subscription Expiry</th>
              <th>Days Remaining</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($labs as $lab)
              @php
                $daysRemaining = $lab->sub_to ? (int)now()->diffInDays($lab->sub_to, false) : 0;
              @endphp
              <tr>
                <td><strong>{{ $lab->name }}</strong></td>
                <td>{{ $lab->email }}</td>
                <td>{{ $lab->sub_from ? $lab->sub_from->format('d M Y') : '-' }}</td>
                <td>{{ $lab->sub_to ? $lab->sub_to->format('d M Y') : '-' }}</td>
                <td>
                  @if($daysRemaining > 30)
                    <span class="badge badge-success">{{ $daysRemaining }} Days</span>
                  @elseif($daysRemaining > 0)
                    <span class="badge badge-warning">{{ $daysRemaining }} Days</span>
                  @else
                    <span class="badge badge-danger">Expired</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $lab->status ? 'success' : 'secondary' }}">{{ $lab->status ? 'Active' : 'Suspended' }}</span>
                </td>
                <td>
                  <a href="{{ route('superadmin.addlab', ['id' => $lab->id]) }}" class="btn btn-sm btn-info">Renew / Extend</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
