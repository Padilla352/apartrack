@extends('owner.layouts.app')

@section('title', 'Dashboard')
@section('page-title', '')

@section('content')
<!-- Welcome Message -->
<div class="welcome-section">
    <h2 class="welcome-title">WELCOME to Owner Dashboard</h2>
    <p class="welcome-subtitle">APARTrack: Your centralized platform for managing rentals in Binalonan.</p>
</div>

<!-- Stats Cards Row - First card is NOT clickable -->
<div class="stats-grid">
    <!-- Total Tenants Card -->
    <div class="stat-card">
        <div class="stat-circle">
            <span class="stat-number">{{ $stats['total_tenants'] ?? 0 }}</span>
        </div>
        <div class="stat-label">Total number of my Tenants</div>
        <div class="stat-trend">
            <span class="trend-up">+{{ $stats['pending_tenants'] ?? 0 }} pending</span>
        </div>
    </div>
    
    <!-- Pending Listings Card - CLICKABLE -->
    <a href="{{ route('owner.apartments.index', ['status' => 'Vacant']) }}" class="stat-card">
        <div class="stat-circle">
            <span class="stat-number">{{ $stats['pending'] ?? 0 }}</span>
        </div>
        <div class="stat-label">Pending Listings</div>
        <div class="stat-trend">
            <span class="trend-neutral">{{ $stats['maintenance'] ?? 0 }} under maintenance</span>
        </div>
        <div class="click-hint">Click to view pending listings →</div>
    </a>
    
    <!-- Approved Listings Card - CLICKABLE -->
    <a href="{{ route('owner.apartments.index', ['status' => 'Occupied']) }}" class="stat-card">
        <div class="stat-circle">
            <span class="stat-number">{{ $stats['approved'] ?? 0 }}</span>
        </div>
        <div class="stat-label">Approved Listings</div>
        <div class="stat-trend">
            <span class="trend-up">{{ $stats['occupancy_rate'] ?? 0 }}% occupancy rate</span>
        </div>
        <div class="click-hint">Click to view approved listings →</div>
    </a>
</div>

<!-- Two Column Layout -->
<div class="two-column-layout">
    <!-- Left Column: Annual Graph -->
    <div class="graph-section">
        <div class="graph-header">
            <h3 class="graph-title">Annual Graph for Tenants</h3>
            <div class="graph-subtitle">(progress of the apartment by showing how many tenants in a year)</div>
        </div>
        
        <!-- Graph Bars with REAL DATA -->
        <div class="graph-bars">
            @php
                // Safely get monthly data or use default
                $monthlyData = [];
                if (isset($monthlyTenantData) && is_array($monthlyTenantData) && count($monthlyTenantData) > 0) {
                    $monthlyData = $monthlyTenantData;
                } else {
                    // Default data with zeros
                    $monthlyData = [
                        ['month' => 'Jan', 'count' => 0], ['month' => 'Feb', 'count' => 0],
                        ['month' => 'Mar', 'count' => 0], ['month' => 'Apr', 'count' => 0],
                        ['month' => 'May', 'count' => 0], ['month' => 'Jun', 'count' => 0],
                        ['month' => 'Jul', 'count' => 0], ['month' => 'Aug', 'count' => 0],
                        ['month' => 'Sep', 'count' => 0], ['month' => 'Oct', 'count' => 0],
                        ['month' => 'Nov', 'count' => 0], ['month' => 'Dec', 'count' => 0]
                    ];
                }
                
                // Get max value for bar heights, prevent division by zero
                $maxValue = 1;
                if (is_array($monthlyData) && count($monthlyData) > 0) {
                    $counts = array_column($monthlyData, 'count');
                    $maxValue = max($counts) ?: 1;
                }
            @endphp
            
            @if(is_array($monthlyData) && count($monthlyData) > 0)
                @foreach($monthlyData as $data)
                    @php
                        $barHeight = isset($data['count']) && $data['count'] > 0 ? ($data['count'] / $maxValue) * 100 : 4;
                    @endphp
                    <div class="bar-column">
                        <div class="bar" style="height: {{ $barHeight }}px;"></div>
                        <div class="bar-label">{{ $data['month'] ?? 'N/A' }}</div>
                        <div class="bar-value">{{ $data['count'] ?? 0 }}</div>
                    </div>
                @endforeach
            @else
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                    <div class="bar-column">
                        <div class="bar" style="height: 4px;"></div>
                        <div class="bar-label">{{ $month }}</div>
                        <div class="bar-value">0</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <!-- Right Column: Reports + Email List -->
    <div class="reports-section">
        <!-- Reports Card - CLICKABLE -->
        <a href="{{ route('owner.reports.tenants') }}" class="reports-card">
            <div class="reports-circle">
                <span class="reports-number">{{ $stats['reports'] ?? 0 }}</span>
            </div>
            <div class="reports-label">Reports / Complain</div>
            <div class="stat-trend">
                <span class="trend-info">{{ $stats['recent_reports'] ?? 0 }} in last 30 days</span>
            </div>
            <div class="click-hint">Click to view reports →</div>
        </a>
        
        <!-- Email List with REAL TENANTS - CLICKABLE TENANTS -->
        <div class="email-list">
            <div class="email-header">
                <span>Recent Tenants</span>
                <a href="{{ route('owner.tenants.index') }}" class="view-all-link">View all →</a>
            </div>
            @php
                $recentTenantsList = isset($recentTenants) && $recentTenants->count() > 0 ? $recentTenants : collect();
            @endphp
            @forelse($recentTenantsList as $tenant)
                <a href="{{ route('owner.tenants.show', $tenant->id) }}" class="email-item">
                    {{ $tenant->email ?? ($tenant->full_name ?? 'No email') }}
                </a>
            @empty
                <div class="email-item">No tenants yet</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* Welcome Section */
    .welcome-section {
        text-align: center;
        margin-bottom: 32px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }
    
    .welcome-title {
        font-size: 24px;
        font-weight: 700;
        color: #000333;
        margin-bottom: 8px;
    }
    
    .welcome-subtitle {
        font-size: 14px;
        color: #64748b;
        letter-spacing: 0.3px;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: #F0F0F0;
        border-radius: 16px;
        padding: 24px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #E0E0E0;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    /* Non-clickable stat card doesn't have pointer cursor on hover */
    .stat-card:not(a) {
        cursor: default;
    }
    
    .stat-card:not(a):hover {
        transform: none;
    }
    
    /* Clickable stat cards have pointer cursor */
    a.stat-card {
        cursor: pointer;
    }
    
    a.stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .stat-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: white;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 13px;
        color: #333333;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .stat-trend {
        margin-top: 8px;
        font-size: 11px;
    }
    
    .trend-up {
        color: #000000;
    }
    
    .trend-neutral {
        color: #000000;
    }
    
    .trend-info {
        color: #007BFF;
    }
    
    .click-hint {
        font-size: 10px;
        color: #007BFF;
        margin-top: 10px;
        opacity: 0;
        transform: translateY(5px);
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    a.stat-card:hover .click-hint,
    .reports-card:hover .click-hint {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Two Column Layout */
    .two-column-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        background: #F0F0F0;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
    }
    
    .graph-section {
        padding: 24px;
        border-right: 1px solid #E0E0E0;
    }
    
    .graph-header {
        text-align: center;
        margin-bottom: 28px;
    }
    
    .graph-title {
        font-size: 18px;
        font-weight: 700;
        color: #000333;
        margin-bottom: 6px;
    }
    
    .graph-subtitle {
        font-size: 11px;
        color: #666666;
        font-style: italic;
    }
    
    .graph-bars {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 6px;
        padding: 10px 0 5px 0;
        min-height: 200px;
    }
    
    .bar-column {
        flex: 1;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    
    .bar {
        width: 100%;
        max-width: 35px;
        background: linear-gradient(180deg, #007BFF 0%, #00A2FF 100%);
        border-radius: 4px 4px 0 0;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .bar:hover {
        background: linear-gradient(180deg, #00A2FF 0%, #007BFF 100%);
        transform: scaleX(1.05);
    }
    
    .bar-label {
        font-size: 10px;
        font-weight: 600;
        color: #333333;
    }
    
    .bar-value {
        font-size: 10px;
        color: #666666;
        font-weight: 500;
    }
    
    .reports-section {
        padding: 24px;
        background: #F0F0F0;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .reports-card {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        border: 1px solid #E0E0E0;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    .reports-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    
    .reports-circle {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }
    
    .reports-number {
        font-size: 28px;
        font-weight: 800;
        color: white;
        line-height: 1;
    }
    
    .reports-label {
        font-size: 12px;
        color: #333333;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .email-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .email-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        color: #333333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        padding: 0 4px;
    }
    
    .view-all-link {
        color: #007BFF;
        text-decoration: none;
        font-size: 11px;
        font-weight: 500;
        text-transform: none;
    }
    
    .view-all-link:hover {
        text-decoration: underline;
    }
    
    .email-item {
        text-align: center;
        padding: 10px 12px;
        background: #FFFFFF;
        border-radius: 10px;
        color: #333333;
        font-size: 12px;
        font-family: monospace;
        border: 1px solid #E0E0E0;
        transition: all 0.2s ease;
        word-break: break-all;
        text-decoration: none;
        display: block;
    }
    
    .email-item:hover {
        background: #F0F0F0;
        border-color: #007BFF;
        transform: translateX(4px);
        color: #007BFF;
    }
    
    /* Responsive */
    @media (max-width: 900px) {
        .two-column-layout {
            grid-template-columns: 1fr;
        }
        
        .graph-section {
            border-right: none;
            border-bottom: 1px solid #E0E0E0;
        }
        
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        
        .stat-circle, .reports-circle {
            width: 60px;
            height: 60px;
        }
        
        .stat-number, .reports-number {
            font-size: 24px;
        }
        
        .stat-label, .reports-label {
            font-size: 11px;
        }
        
        .click-hint {
            font-size: 9px;
        }
        
        .welcome-title {
            font-size: 20px;
        }
        
        .welcome-subtitle {
            font-size: 12px;
        }
    }
    
    @media (max-width: 600px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .graph-bars {
            gap: 4px;
        }
        
        .bar-label, .bar-value {
            font-size: 8px;
        }
        
        .stat-circle, .reports-circle {
            width: 50px;
            height: 50px;
        }
        
        .stat-number, .reports-number {
            font-size: 20px;
        }
        
        .click-hint {
            font-size: 8px;
        }
        
        .email-header {
            font-size: 10px;
        }
        
        .view-all-link {
            font-size: 9px;
        }
        
        .welcome-title {
            font-size: 18px;
        }
        
        .welcome-subtitle {
            font-size: 11px;
        }
    }
</style>
@endsection