@extends('layouts.admin')

@section('content')
<div class="tenant-view-container">
    {{-- Header with breadcrumb --}}
    <div class="view-header">
        <div class="breadcrumb">
            <a href="{{ route('users-management.tenants.list') }}" class="breadcrumb-link">
                <i class="fas fa-chevron-left"></i> Back to Tenants
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Tenant Profile</span>
        </div>
        <h1 class="view-title">Tenant Profile</h1>
        <p class="view-subtitle">View and manage tenant account details</p>
    </div>

    {{-- Main Card --}}
    <div class="view-card">
        {{-- Avatar Section (no badge) --}}
        <div class="view-avatar-section">
            <div class="view-avatar-wrapper">
                <div class="view-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="view-name-section">
                <h2 class="view-name">{{ $tenant->name }}</h2>
                <p class="view-meta">Member since {{ $tenant->created_at ? $tenant->created_at->format('F d, Y') : 'N/A' }}</p>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="view-info-grid">
            <div class="info-column">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-envelope"></i> Email Address</div>
                    <div class="info-value">{{ $tenant->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-phone-alt"></i> Contact Number</div>
                    <div class="info-value">{{ $tenant->contact ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar-alt"></i> Date Registered</div>
                    <div class="info-value">{{ $tenant->created_at ? $tenant->created_at->format('F d, Y h:i A') : 'N/A' }}</div>
                </div>
            </div>
            <div class="info-column">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-id-card"></i> Tenant ID</div>
                    <div class="info-value">#{{ $tenant->id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-user-tag"></i> Account Type</div>
                    <div class="info-value">Tenant</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
                    <div class="info-value">{{ $tenant->updated_at ? $tenant->updated_at->diffForHumans() : 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="view-actions">
            <a href="{{ route('users-management.tenants.edit', $tenant->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</div>

<style>
    .tenant-view-container {
        padding: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .view-header {
        margin-bottom: 2rem;
    }
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .breadcrumb-link {
        color: #9ca3af;
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-link:hover {
        color: #f5b81b;
    }
    .breadcrumb-separator {
        color: #4b5563;
    }
    .breadcrumb-current {
        color: #f5b81b;
        font-weight: 500;
    }
    .view-title {
        font-size: 1.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff, #f5b81b);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 0.25rem;
    }
    .view-subtitle {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Main Card */
    .view-card {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 1.5rem;
        border: 1px solid rgba(245, 184, 27, 0.2);
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* Avatar Section */
    .view-avatar-section {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, #1a1c23 0%, #0f1115 100%);
        border-bottom: 1px solid rgba(245, 184, 27, 0.15);
        flex-wrap: wrap;
    }
    .view-avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .view-avatar {
        width: 100px;
        height: 100px;
        background: #1e1e2a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #f5b81b;
        box-shadow: 0 0 20px rgba(245, 184, 27, 0.2);
    }
    /* Smaller icon – perfectly centered inside the circle */
    .view-avatar i {
        font-size: 1.6rem;
        color: #f5b81b;
    }
    .view-name-section {
        flex: 1;
    }
    .view-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem;
    }
    .view-meta {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Info Grid */
    .view-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        padding: 2rem;
        border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    }
    @media (max-width: 768px) {
        .view-info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    .info-column {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .info-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #f5b81b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-label i {
        width: 1rem;
        font-size: 0.75rem;
    }
    .info-value {
        font-size: 1rem;
        color: #e5e7eb;
        font-weight: 500;
        padding-left: 1.5rem;
    }

    /* Action Buttons */
    .view-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem 2rem;
        background: rgba(0, 0, 0, 0.2);
    }
    .btn {
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-primary {
        background: #f5b81b;
        color: #0a0c10;
    }
    .btn-primary:hover {
        background: #e6a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 184, 27, 0.3);
    }
</style>
@endsection