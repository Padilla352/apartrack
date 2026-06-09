@extends('owner.layouts.app')

@section('title', 'Occupancy Report')

@section('page-title', 'occupancy report')

@section('content')
<div class="reports-container">
    <!-- Report Header with Print Button -->
    <div class="report-header-flex">
        <div class="header-left">
            <h2 class="report-title">Occupancy Report</h2>
            <p class="report-description">Detailed analysis of apartment occupancy rates</p>
        </div>
        <div class="header-right">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Statistics Cards - Matching APARTRACK Design -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);">
                <div class="stat-number">{{ $totalApartments ?? 0 }}</div>
            </div>
            <div class="stat-label">Total Units</div>
            <div class="stat-trend">
                <i class="fas fa-building"></i> All properties
            </div>
            <div class="click-hint">View all units</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number">{{ $occupiedUnits ?? 0 }}</div>
            </div>
            <div class="stat-label">Occupied Units</div>
            <div class="stat-trend">
                <i class="fas fa-home trend-up"></i> Currently rented
            </div>
            <div class="click-hint">View occupied units</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="stat-number">{{ $vacantUnits ?? 0 }}</div>
            </div>
            <div class="stat-label">Vacant Units</div>
            <div class="stat-trend">
                <i class="fas fa-door-open"></i> Available now
            </div>
            <div class="click-hint">View vacant units</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <div class="stat-number">{{ isset($occupancyRate) ? number_format($occupancyRate, 0) : 0 }}%</div>
            </div>
            <div class="stat-label">Occupancy Rate</div>
            <div class="stat-trend">
                <i class="fas fa-chart-pie"></i> Overall rate
            </div>
            <div class="click-hint">View details</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-two-columns">
        <!-- Occupancy by Type Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Occupancy by Apartment Type</h3>
                <p class="chart-subtitle">Distribution across property types</p>
            </div>
            <div class="chart-body">
                <canvas id="occupancyByTypeChart" height="280"></canvas>
            </div>
        </div>
        
        <!-- Occupancy Rate Gauge -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Overall Occupancy Rate</h3>
                <p class="chart-subtitle">Current occupancy percentage</p>
            </div>
            <div class="chart-body gauge-wrapper">
                <div class="gauge-container">
                    <canvas id="occupancyGauge" height="280"></canvas>
                    <div class="gauge-center-text">
                        <div class="gauge-percentage">{{ isset($occupancyRate) ? number_format($occupancyRate, 0) : 0 }}%</div>
                        <div class="gauge-label">Occupancy Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupancy by Type Table -->
    <div class="data-card">
        <div class="card-header-flex">
            <h3 class="card-title">Detailed Occupancy by Apartment Type</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Apartment Type</th>
                        <th>Total Units</th>
                        <th>Occupied</th>
                        <th>Vacant</th>
                        <th>Occupancy Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($occupancyByType) && $occupancyByType->count() > 0)
                        @foreach($occupancyByType as $type)
                            @php
                                $total = $type->total;
                                $occupied = $type->occupied;
                                $vacant = $total - $occupied;
                                $rate = $total > 0 ? ($occupied / $total) * 100 : 0;
                                $rateColor = $rate >= 80 ? '#28a745' : ($rate >= 50 ? '#ffc107' : '#dc3545');
                            @endphp
                            <tr>
                                <td><strong>{{ $type->type }}</strong></td>
                                <td>{{ $total }}</td>
                                <td class="text-occupied">{{ $occupied }}</td>
                                <td class="text-vacant">{{ $vacant }}</td>
                                <td class="text-rate">{{ number_format($rate, 1) }}%</td>
                                <td>
                                    <div class="progress-wrapper">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $rate }}%; background: {{ $rateColor }};"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="empty-message">
                                <i class="fas fa-chart-bar"></i>
                                <p>No data available</p>
                             </td>
                        </tr>
                    @endif
                </tbody>
             </table>
        </div>
    </div>

    <!-- Occupancy by Barangay Table -->
    <div class="data-card">
        <div class="card-header-flex">
            <h3 class="card-title">Occupancy by Barangay</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th>Total Units</th>
                        <th>Occupied</th>
                        <th>Vacant</th>
                        <th>Occupancy Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($occupancyByBarangay) && $occupancyByBarangay->count() > 0)
                        @foreach($occupancyByBarangay as $barangay)
                            @php
                                $total = $barangay->total;
                                $occupied = $barangay->occupied;
                                $vacant = $total - $occupied;
                                $rate = $total > 0 ? ($occupied / $total) * 100 : 0;
                                $rateColor = $rate >= 80 ? '#28a745' : ($rate >= 50 ? '#ffc107' : '#dc3545');
                                $barangayName = isset($barangay->barangay) ? $barangay->barangay->name : 'Unknown';
                            @endphp
                            <tr>
                                <td><strong>{{ $barangayName }}</strong></td>
                                <td>{{ $total }}</td>
                                <td class="text-occupied">{{ $occupied }}</td>
                                <td class="text-vacant">{{ $vacant }}</td>
                                <td class="text-rate">{{ number_format($rate, 1) }}%</td>
                                <td>
                                    <div class="progress-wrapper">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $rate }}%; background: {{ $rateColor }};"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="empty-message">
                                <i class="fas fa-map-marker-alt"></i>
                                <p>No data available</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Reports Page - APARTRACK Design System */
    .reports-container {
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
    
    /* Stats Grid - Matching APARTRACK */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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
    
    /* Charts Two Columns */
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
        background: white;
    }
    
    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #000333;
        margin: 0 0 4px 0;
    }
    
    .chart-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin: 0;
    }
    
    .chart-body {
        padding: 20px;
    }
    
    /* Gauge Chart */
    .gauge-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 320px;
    }
    
    .gauge-container {
        position: relative;
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
    }
    
    .gauge-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    
    .gauge-percentage {
        font-size: 42px;
        font-weight: 800;
        color: #000333;
        line-height: 1;
    }
    
    .gauge-label {
        font-size: 12px;
        color: #6B7280;
        margin-top: 4px;
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
        letter-spacing: 0.05em;
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
    
    .text-occupied {
        color: #28a745;
        font-weight: 600;
    }
    
    .text-vacant {
        color: #ffc107;
        font-weight: 600;
    }
    
    .text-rate {
        font-weight: 600;
    }
    
    /* Progress Bar */
    .progress-wrapper {
        min-width: 120px;
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
    
    /* Print Styles */
    @media print {
        .btn-print,
        .header-right,
        .click-hint {
            display: none;
        }
        
        .stat-card,
        .chart-card,
        .data-card {
            break-inside: avoid;
            box-shadow: none;
        }
        
        .stats-grid {
            break-inside: avoid;
        }
        
        body {
            background: white;
        }
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .report-title {
            color: #EDEDEC;
        }
        
        .report-description {
            color: #9CA3AF;
        }
        
        .chart-card {
            background: #1F2937;
            border-color: #374151;
        }
        
        .chart-header {
            background: #1F2937;
            border-bottom-color: #374151;
        }
        
        .chart-title {
            color: #F3F4F6;
        }
        
        .chart-subtitle {
            color: #9CA3AF;
        }
        
        .data-card {
            background: #1F2937;
            border-color: #374151;
        }
        
        .card-header-flex {
            border-bottom-color: #374151;
        }
        
        .card-title {
            color: #F3F4F6;
        }
        
        .data-table th {
            background: #1F2937;
            color: #9CA3AF;
            border-bottom-color: #374151;
        }
        
        .data-table td {
            border-bottom-color: #374151;
            color: #E5E7EB;
        }
        
        .data-table tr:hover td {
            background: #2D3748;
        }
        
        .gauge-percentage {
            color: #F3F4F6;
        }
        
        .gauge-label {
            color: #9CA3AF;
        }
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
        
        .stat-number {
            font-size: 28px;
        }
        
        .stat-circle {
            width: 70px;
            height: 70px;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            font-size: 12px;
        }
        
        .progress-wrapper {
            min-width: 80px;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Occupancy by Type Chart
        const occupancyByType = @json($occupancyByType ?? []);
        const ctx = document.getElementById('occupancyByTypeChart');
        
        if (ctx && occupancyByType.length > 0) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: occupancyByType.map(item => item.type),
                    datasets: [
                        {
                            label: 'Total Units',
                            data: occupancyByType.map(item => item.total),
                            backgroundColor: 'rgba(0, 123, 255, 0.6)',
                            borderColor: '#007BFF',
                            borderWidth: 1,
                            borderRadius: 8
                        },
                        {
                            label: 'Occupied Units',
                            data: occupancyByType.map(item => item.occupied),
                            backgroundColor: 'rgba(40, 167, 69, 0.6)',
                            borderColor: '#28a745',
                            borderWidth: 1,
                            borderRadius: 8
                        }
                    ]
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
                                    return context.dataset.label + ': ' + context.raw;
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
        
        // Occupancy Gauge Chart
        const occupancyRate = {{ isset($occupancyRate) ? $occupancyRate : 0 }};
        const gaugeCtx = document.getElementById('occupancyGauge');
        
        if (gaugeCtx) {
            new Chart(gaugeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Occupied', 'Vacant'],
                    datasets: [{
                        data: [occupancyRate, 100 - occupancyRate],
                        backgroundColor: ['#28a745', '#E5E7EB'],
                        borderWidth: 0,
                        cutout: '75%',
                        borderRadius: 10
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
                                    return context.label + ': ' + context.raw.toFixed(1) + '%';
                                }
                            }
                        }
                    },
                    events: []
                }
            });
        }
    });
</script>
@endpush