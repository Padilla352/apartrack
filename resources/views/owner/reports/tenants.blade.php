@extends('owner.layouts.app')

@section('title', 'Tenants Report')
@section('page-title', 'tenants report')

@section('content')
<div class="tenants-report-container">
    <!-- Report Header -->
    <div class="report-header-flex">
        <div class="header-left">
            <h2 class="report-title">Tenants Report</h2>
            <p class="report-description">Comprehensive tenant statistics and analysis</p>
        </div>
        <div class="header-right">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Statistics Cards - APARTRACK Style -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);">
                <div class="stat-number">{{ $totalTenants ?? 0 }}</div>
            </div>
            <div class="stat-label">Total Tenants</div>
            <div class="stat-trend">
                <i class="fas fa-users"></i> All registered tenants
            </div>
            <div class="click-hint">View all tenants</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number">{{ $activeTenants ?? 0 }}</div>
            </div>
            <div class="stat-label">Active Tenants</div>
            <div class="stat-trend">
                <i class="fas fa-user-check trend-up"></i> Currently active
            </div>
            <div class="click-hint">View active tenants</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #6c757d 0%, #8b9dc3 100%);">
                <div class="stat-number">{{ $inactiveTenants ?? 0 }}</div>
            </div>
            <div class="stat-label">Inactive Tenants</div>
            <div class="stat-trend">
                <i class="fas fa-user-slash"></i> Currently inactive
            </div>
            <div class="click-hint">View inactive tenants</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-two-columns">
        <!-- Tenants by Apartment Type Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Tenants by Apartment Type</h3>
                <p class="chart-subtitle">Distribution across property types</p>
            </div>
            <div class="chart-body">
                <canvas id="tenantsByTypeChart" height="280"></canvas>
            </div>
        </div>
        
        <!-- Lease Expiration Timeline Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Lease Expiration Timeline</h3>
                <p class="chart-subtitle">Upcoming lease expirations by month</p>
            </div>
            <div class="chart-body">
                <canvas id="leaseExpirationChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Tenants by Apartment Type Table -->
    <div class="data-card">
        <div class="card-header-flex">
            <h3 class="card-title">Tenant Distribution by Apartment Type</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Apartment Type</th>
                        <th>Number of Tenants</th>
                        <th>Percentage</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($tenantsByApartmentType) && $tenantsByApartmentType->count() > 0)
                        @php
                            $total = $tenantsByApartmentType->sum('count');
                        @endphp
                        @foreach($tenantsByApartmentType as $type)
                            @php
                                $percentage = $total > 0 ? ($type->count / $total) * 100 : 0;
                                $barColor = $percentage >= 50 ? '#28a745' : ($percentage >= 25 ? '#ffc107' : '#dc3545');
                            @endphp
                            <tr>
                                <td><strong>{{ $type->type ?? 'Unknown' }}</strong></td>
                                <td>{{ $type->count ?? 0 }}</td>
                                <td class="text-rate">{{ number_format($percentage, 1) }}%</td>
                                <td>
                                    <div class="progress-wrapper">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $percentage }}%; background: {{ $barColor }};"></div>
                                        </div>
                                    </div>
                                  </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="empty-message">
                                <i class="fas fa-chart-pie"></i>
                                <p>No data available</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lease Expiration Summary -->
    <div class="data-card">
        <div class="card-header-flex">
            <h3 class="card-title">Lease Expiration Summary</h3>
        </div>
        <div class="lease-summary-grid">
            @php
                $expiringSoon = 0;
                $expired = 0;
                $valid = 0;
                
                if(isset($leaseExpirations) && $leaseExpirations->count() > 0) {
                    foreach($leaseExpirations as $expiration) {
                        $expiringSoon += $expiration->count ?? 0;
                    }
                }
            @endphp
            
            <div class="summary-card summary-warning">
                <div class="summary-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="summary-number">{{ $expiringSoon }}</h3>
                <p class="summary-label">Expiring in 30 days</p>
                <span class="summary-hint">Needs attention</span>
            </div>
            
            <div class="summary-card summary-danger">
                <div class="summary-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="summary-number">{{ $expired }}</h3>
                <p class="summary-label">Expired Leases</p>
                <span class="summary-hint">Action required</span>
            </div>
            
            <div class="summary-card summary-success">
                <div class="summary-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="summary-number">{{ $valid }}</h3>
                <p class="summary-label">Valid Leases</p>
                <span class="summary-hint">All good</span>
            </div>
        </div>
    </div>
</div>

<style>
    .tenants-report-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* Report Header */
    .report-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .report-title {
        font-size: 28px;
        font-weight: 700;
        color: #000333;
        text-transform: lowercase;
        margin-bottom: 4px;
    }
    
    .report-description {
        color: #6B7280;
        margin: 0;
        font-size: 14px;
    }
    
    .btn-print {
        background: #6B7280;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-print:hover {
        background: #4B5563;
        transform: translateY(-2px);
    }
    
    /* Stats Grid */
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
        border: 1px solid #E0E0E0;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .stat-circle {
        width: 80px;
        height: 80px;
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
        color: #666;
    }
    
    .trend-up {
        color: #28a745;
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
    
    .stat-card:hover .click-hint {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Charts Row */
    .charts-two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .chart-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
    }
    
    .chart-header {
        padding: 20px 24px;
        border-bottom: 1px solid #E0E0E0;
    }
    
    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #000333;
        margin: 0;
    }
    
    .chart-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin: 4px 0 0 0;
    }
    
    .chart-body {
        padding: 20px;
    }
    
    /* Data Card */
    .data-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-header-flex {
        padding: 20px 24px;
        border-bottom: 1px solid #E0E0E0;
    }
    
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #000333;
        margin: 0;
    }
    
    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        text-align: left;
        padding: 16px 20px;
        background: #F9FAFB;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        border-bottom: 2px solid #E5E7EB;
    }
    
    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
    }
    
    .data-table tr:hover td {
        background: #F9FAFB;
    }
    
    .text-rate {
        font-weight: 600;
        color: #007BFF;
    }
    
    /* Progress Bar */
    .progress-wrapper {
        min-width: 150px;
    }
    
    .progress-bar-bg {
        background: #E5E7EB;
        border-radius: 9999px;
        height: 8px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.5s ease;
    }
    
    /* Lease Summary Grid */
    .lease-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 24px;
    }
    
    .summary-card {
        text-align: center;
        padding: 24px 20px;
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    
    .summary-card:hover {
        transform: translateY(-4px);
    }
    
    .summary-warning {
        background: linear-gradient(135deg, #FFF8E1 0%, #FFECB3 100%);
        border: 1px solid #FFC107;
    }
    
    .summary-danger {
        background: linear-gradient(135deg, #FFEBEE 0%, #FFCDD2 100%);
        border: 1px solid #DC3545;
    }
    
    .summary-success {
        background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
        border: 1px solid #28A745;
    }
    
    .summary-icon {
        font-size: 32px;
        margin-bottom: 12px;
    }
    
    .summary-warning .summary-icon {
        color: #FFC107;
    }
    
    .summary-danger .summary-icon {
        color: #DC3545;
    }
    
    .summary-success .summary-icon {
        color: #28A745;
    }
    
    .summary-number {
        font-size: 36px;
        font-weight: 800;
        margin: 0 0 8px 0;
    }
    
    .summary-warning .summary-number {
        color: #F57C00;
    }
    
    .summary-danger .summary-number {
        color: #D32F2F;
    }
    
    .summary-success .summary-number {
        color: #2E7D32;
    }
    
    .summary-label {
        font-size: 13px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: #333;
    }
    
    .summary-hint {
        font-size: 11px;
        color: #6B7280;
    }
    
    /* Empty Message */
    .empty-message {
        text-align: center;
        padding: 48px 24px;
    }
    
    .empty-message i {
        font-size: 48px;
        color: #D1D5DB;
        margin-bottom: 16px;
        display: block;
    }
    
    .empty-message p {
        color: #6B7280;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .charts-two-columns {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .lease-summary-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .report-header-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .report-title {
            font-size: 24px;
        }
        
        .stat-circle {
            width: 60px;
            height: 60px;
        }
        
        .stat-number {
            font-size: 24px;
        }
        
        .progress-wrapper {
            min-width: 100px;
        }
        
        .summary-number {
            font-size: 28px;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            font-size: 12px;
        }
    }
    
    /* Print Styles */
    @media print {
        .btn-print,
        .header-right {
            display: none;
        }
        
        .stat-card,
        .chart-card,
        .data-card {
            break-inside: avoid;
            box-shadow: none;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tenants by Apartment Type Chart
        const tenantsByType = @json($tenantsByApartmentType ?? []);
        const typeCtx = document.getElementById('tenantsByTypeChart');
        
        if (typeCtx && tenantsByType && tenantsByType.length > 0) {
            const labels = tenantsByType.map(function(item) {
                return item.type || 'Unknown';
            });
            const data = tenantsByType.map(function(item) {
                return item.count || 0;
            });
            
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#007BFF', '#28A745', '#FFC107', '#DC3545', '#17A2B8', '#6F42C1', '#FD7E14', '#20C997'],
                        borderWidth: 0,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Lease Expiration Chart
        const leaseExpirations = @json($leaseExpirations ?? []);
        const leaseCtx = document.getElementById('leaseExpirationChart');
        
        if (leaseCtx && leaseExpirations && leaseExpirations.length > 0) {
            const months = leaseExpirations.map(function(item) {
                return item.month || 'Unknown';
            });
            const counts = leaseExpirations.map(function(item) {
                return item.count || 0;
            });
            
            new Chart(leaseCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Expiring Leases',
                        data: counts,
                        borderColor: '#DC3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#DC3545',
                        pointBorderColor: '#fff',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Expiring: ' + context.raw + ' lease(s)';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                color: '#E5E7EB'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush