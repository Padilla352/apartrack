@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Business Spaces</h1>

    {{-- Search & Filter Form --}}
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or type" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="barangay" class="form-control">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->name }}" {{ request('barangay') == $barangay->name ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('user.businesses.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="row">
        @forelse($businesses as $business)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($business->images && count(json_decode($business->images, true)) > 0)
                        <img src="{{ asset('storage/' . json_decode($business->images, true)[0]) }}" class="card-img-top" alt="{{ $business->business_name }}">
                    @else
                        <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $business->business_name }}</h5>
                        <p class="card-text">{{ Str::limit($business->description, 100) }}</p>
                        <p><strong>Type:</strong> {{ $business->type }}</p>
                        <p><strong>Barangay:</strong> {{ $business->barangay_name }}</p>
                        <p><strong>Monthly Rent:</strong> ₱{{ number_format($business->monthly_rent, 2) }}</p>
                        <a href="{{ route('user.businesses.show', $business->id) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No approved businesses found.</div>
            </div>
        @endforelse
    </div>

    {{ $businesses->links() }}
</div>
@endsection