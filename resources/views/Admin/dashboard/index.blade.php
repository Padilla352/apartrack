@extends('layouts.admin')

@section('content')
<div class="p-6 lg:p-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        {{-- Welcome Header --}}
        <div class="dashboard-header mb-8">
            <div>
                <h1 class="dashboard-title">Dashboard</h1>
                <p class="dashboard-subtitle">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! Here's what's happening today.</p>
            </div>
            <div class="date-badge">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDate">{{ date('F d, Y') }}</span>
            </div>
        </div>

        {{-- Stats Cards Grid - Glowing Dark Edition --}}
        <div class="stats-grid-dashboard">
            
            {{-- Total Apartments --}}
            <div class="stat-card-dashboard group">
                <div class="stat-card-inner">
                    <div class="stat-header">
                        <span class="stat-label-dashboard">Apartments</span>
                        <div class="stat-icon-dashboard icon-apartment">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="stat-value-dashboard" id="totalApartments">{{ $totalApartments ?? 0 }}</div>
                    <div class="stat-footer-dashboard">
                        <span class="stat-subtext">Total Listings</span>
                        <span class="stat-trend positive" id="apartmentTrend">
                            <i class="fas fa-chart-line"></i> Loading...
                        </span>
                    </div>
                </div>
                <div class="stat-progress-bar">
                    <div class="progress-fill" id="apartmentFill" style="width: 0%"></div>
                </div>
            </div>

            {{-- Total Businesses --}}
            <div class="stat-card-dashboard group">
                <div class="stat-card-inner">
                    <div class="stat-header">
                        <span class="stat-label-dashboard">Commercial</span>
                        <div class="stat-icon-dashboard icon-business">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                    <div class="stat-value-dashboard" id="totalBusinesses">{{ $totalBusinesses ?? 0 }}</div>
                    <div class="stat-footer-dashboard">
                        <span class="stat-subtext">Registered Entities</span>
                        <span class="stat-trend positive" id="businessTrend">
                            <i class="fas fa-chart-line"></i> Loading...
                        </span>
                    </div>
                </div>
                <div class="stat-progress-bar">
                    <div class="progress-fill" id="businessFill" style="width: 0%"></div>
                </div>
            </div>

            {{-- Pending Approvals --}}
            <div class="stat-card-dashboard group">
                <div class="stat-card-inner">
                    <div class="stat-header">
                        <span class="stat-label-dashboard">Approvals</span>
                        <div class="stat-icon-dashboard icon-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value-dashboard pending-value" id="pendingApprovals">{{ $pendingApprovals ?? 0 }}</div>
                    <div class="stat-footer-dashboard">
                        <span class="stat-subtext">Pending Requests</span>
                        <span class="stat-trend warning" id="approvalTrend">
                            <i class="fas fa-hourglass-half"></i> Awaiting
                        </span>
                    </div>
                </div>
                <div class="stat-progress-bar">
                    <div class="progress-fill warning" id="approvalFill" style="width: 0%"></div>
                </div>
            </div>

            {{-- Total Users --}}
            <div class="stat-card-dashboard group">
                <div class="stat-card-inner">
                    <div class="stat-header">
                        <span class="stat-label-dashboard">Community</span>
                        <div class="stat-icon-dashboard icon-users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value-dashboard" id="totalUsers">{{ number_format($totalUsers ?? 0) }}</div>
                    <div class="stat-footer-dashboard">
                        <span class="stat-subtext">Total Active Users</span>
                        <span class="stat-trend positive" id="userTrend">
                            <i class="fas fa-user-plus"></i> Loading...
                        </span>
                    </div>
                </div>
                <div class="stat-progress-bar">
                    <div class="progress-fill" id="userFill" style="width: 0%"></div>
                </div>
            </div>
        </div>

        {{-- Monthly Registration Chart --}}
        <div class="chart-card-dashboard mb-8">
            <div class="chart-header-dashboard">
                <div>
                    <h3 class="chart-title-dashboard">Monthly Registrations</h3>
                    <p class="chart-subtitle-dashboard">Growth overview for users and listings</p>
                </div>
                <div class="chart-filters">
                    <button class="year-filter-btn active" data-year="2024">2024</button>
                    <button class="year-filter-btn" data-year="2023">2023</button>
                    <button class="year-filter-btn" data-year="2022">2022</button>
                </div>
            </div>
            <div class="chart-container-dashboard">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- Recent Activities Section --}}
        <div class="activities-grid">
            {{-- Recent Approvals --}}
            <div class="activity-card-dashboard">
                <div class="activity-header">
                    <h3 class="activity-title">Recent Approvals</h3>
                    <a href="{{ route('admin.permit-verification.index') }}" class="activity-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="activity-list" id="recentApprovalsList">
                    <div class="activity-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading recent approvals...
                    </div>
                </div>
            </div>

            {{-- Pending Review --}}
            <div class="activity-card-dashboard">
                <div class="activity-header">
                    <h3 class="activity-title">Pending Review</h3>
                    <a href="{{ route('admin.permit-verification.index') }}" class="activity-link warning">Review All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="activity-list" id="pendingReviewList">
                    <div class="activity-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading pending reviews...
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="quick-actions">
            <div class="quick-actions-header">
                <h3>Quick Actions</h3>
                <span>Common administrative tasks</span>
            </div>
            <div class="actions-grid">
                {{-- View Tenants Button --}}
                <a href="{{ route('users-management.tenants.list') }}" class="quick-action-btn">
                    <i class="fas fa-users"></i> View Tenants
                </a>
                
                {{-- View Apartments Button --}}
                <a href="{{ route('admin.apartments.index') }}" class="quick-action-btn">
                    <i class="fas fa-building"></i> View Apartments
                </a>
                
                {{-- View Businesses Button --}}
                <a href="{{ route('admin.business.index') }}" class="quick-action-btn">
                    <i class="fas fa-store"></i> View Commercial
                </a>
                
                {{-- Review Permits Button --}}
                <a href="{{ route('admin.permit-verification.index') }}" class="quick-action-btn">
                    <i class="fas fa-clipboard-list"></i> View Permits
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let currentChart = null;
    let refreshInterval = null;

    // Fetch real-time dashboard data
    async function fetchDashboardData() {
        try {
            const response = await fetch('{{ route("admin.dashboard.data") }}');
            const data = await response.json();
            
            if (data.success) {
                updateStatsCards(data.stats);
                updateActivityLists(data.activities);
                updateChartData(data.chartData);
                updateProgressBars(data.stats);
            }
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }

    // Update stats cards with real data
    function updateStatsCards(stats) {
        document.getElementById('totalApartments').textContent = stats.totalApartments || 0;
        document.getElementById('totalBusinesses').textContent = stats.totalBusinesses || 0;
        document.getElementById('pendingApprovals').textContent = stats.pendingApprovals || 0;
        document.getElementById('totalUsers').textContent = stats.totalUsers ? stats.totalUsers.toLocaleString() : 0;
        
        document.getElementById('apartmentTrend').innerHTML = stats.apartmentTrend > 0 
            ? `<i class="fas fa-arrow-up"></i> +${stats.apartmentTrend}%` 
            : `<i class="fas fa-arrow-down"></i> ${stats.apartmentTrend}%`;
        
        document.getElementById('businessTrend').innerHTML = stats.businessTrend > 0 
            ? `<i class="fas fa-arrow-up"></i> +${stats.businessTrend}%` 
            : `<i class="fas fa-arrow-down"></i> ${stats.businessTrend}%`;
        
        document.getElementById('userTrend').innerHTML = stats.userTrend > 0 
            ? `<i class="fas fa-user-plus"></i> +${stats.userTrend}%` 
            : `<i class="fas fa-user-minus"></i> ${stats.userTrend}%`;
    }

    // Update progress bars
    function updateProgressBars(stats) {
        const maxValues = {
            apartments: 200,
            businesses: 100,
            approvals: 100,
            users: 2000
        };
        
        const apartmentPercent = Math.min((stats.totalApartments / maxValues.apartments) * 100, 100);
        const businessPercent = Math.min((stats.totalBusinesses / maxValues.businesses) * 100, 100);
        const approvalPercent = Math.min((stats.pendingApprovals / maxValues.approvals) * 100, 100);
        const userPercent = Math.min((stats.totalUsers / maxValues.users) * 100, 100);
        
        document.getElementById('apartmentFill').style.width = `${apartmentPercent}%`;
        document.getElementById('businessFill').style.width = `${businessPercent}%`;
        document.getElementById('approvalFill').style.width = `${approvalPercent}%`;
        document.getElementById('userFill').style.width = `${userPercent}%`;
    }

    // Update activity lists
    function updateActivityLists(activities) {
        // Recent Approvals
        const approvalsList = document.getElementById('recentApprovalsList');
        if (activities.recentApprovals && activities.recentApprovals.length > 0) {
            approvalsList.innerHTML = activities.recentApprovals.map(item => `
                <div class="activity-item-dashboard">
                    <div class="activity-icon success">
                        <i class="fas ${item.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text">${item.title} <span class="activity-type">${item.type}</span></p>
                        <span class="activity-time">${item.time_ago}</span>
                    </div>
                    <span class="activity-badge approved">Approved</span>
                </div>
            `).join('');
        } else {
            approvalsList.innerHTML = `
                <div class="activity-empty">
                    <i class="fas fa-check-circle"></i>
                    <p>No recent approvals</p>
                </div>
            `;
        }
        
        // Pending Reviews
        const pendingList = document.getElementById('pendingReviewList');
        if (activities.pendingReviews && activities.pendingReviews.length > 0) {
            pendingList.innerHTML = activities.pendingReviews.map(item => `
                <div class="activity-item-dashboard">
                    <div class="activity-icon pending">
                        <i class="fas ${item.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text">${item.title} <span class="activity-type">${item.type}</span></p>
                        <span class="activity-time">${item.time_ago}</span>
                    </div>
                    <span class="activity-badge pending">Pending</span>
                </div>
            `).join('');
        } else {
            pendingList.innerHTML = `
                <div class="activity-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No pending reviews</p>
                </div>
            `;
        }
    }

    // Update chart with real data
    function updateChartData(chartData) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const currentYear = document.querySelector('.year-filter-btn.active')?.dataset.year || '2024';
        
        const data = chartData[currentYear] || {
            tenants: Array(12).fill(0),
            apartments: Array(12).fill(0)
        };
        
        const createGradient = (context) => {
            const gradient = context.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(245, 184, 27, 0.25)');
            gradient.addColorStop(0.5, 'rgba(245, 184, 27, 0.08)');
            gradient.addColorStop(1, 'rgba(15, 23, 42, 0.02)');
            return gradient;
        };
        
        if (currentChart) {
            currentChart.destroy();
        }
        
        currentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'New Tenants',
                        data: data.tenants,
                        borderColor: '#f5b81b',
                        backgroundColor: createGradient(ctx),
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f5b81b',
                        pointBorderColor: '#0f1115',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#ffcc44',
                        pointHoverBorderColor: '#0f1115',
                        pointHoverBorderWidth: 2,
                    },
                    {
                        label: 'New Apartments',
                        data: data.apartments,
                        borderColor: '#00e5ff',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        tension: 0.4,
                        borderDash: [8, 6],
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#00e5ff',
                        pointHoverBorderColor: '#0f1115',
                        pointHoverBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeInOutQuart' },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 12,
                            boxHeight: 12,
                            font: { size: 12, weight: '600', family: "'Inter', system-ui" },
                            color: '#cbd5e1',
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 21, 0.95)',
                        titleColor: '#f5b81b',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 12,
                        borderColor: '#f5b81b40',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(245, 184, 27, 0.08)', drawBorder: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', stepSize: 20 }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8' }
                    }
                }
            }
        });
    }

    // Year filter functionality
    async function loadYearData(year) {
        try {
            const response = await fetch(`{{ route("admin.dashboard.data") }}?year=${year}`);
            const data = await response.json();
            if (data.success && data.chartData[year]) {
                updateChartData(data.chartData);
            }
        } catch (error) {
            console.error('Error loading year data:', error);
        }
    }

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('%c✨ APARTrack Dashboard | Real-time Ready', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
        
        // Initial data load
        await fetchDashboardData();
        
        // Set up auto-refresh every 30 seconds
        refreshInterval = setInterval(fetchDashboardData, 30000);
        
        // Year filter buttons
        const filterButtons = document.querySelectorAll('.year-filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const year = this.dataset.year;
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                await loadYearData(year);
            });
        });
        
        // Update date
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const now = new Date();
            dateElement.textContent = now.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
    });
</script>

<style>
    /* ========== GLOW DARK THEME - DASHBOARD ========== */
    
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    .min-h-screen {
        background: #0a0c10 !important;
        background-attachment: fixed;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: #0a0c10;
    }
    
    html, body, #app {
        background-color: #0a0c10;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .dashboard-title {
        font-size: 1.75rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff, #f5b81b);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }
    
    .dashboard-subtitle {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: rgba(15, 17, 21, 0.8);
        border: 1px solid rgba(245, 184, 27, 0.2);
        border-radius: 60px;
        font-size: 0.75rem;
        color: #cbd5e1;
    }
    
    .date-badge i {
        color: #f5b81b;
    }
    
    .stats-grid-dashboard {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    @media (min-width: 640px) {
        .stats-grid-dashboard {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .stats-grid-dashboard {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    .stat-card-dashboard {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 24px;
        border: 1px solid rgba(245, 184, 27, 0.15);
        transition: all 0.3s;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card-dashboard:hover {
        border-color: rgba(245, 184, 27, 0.35);
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
    }
    
    .stat-card-inner {
        padding: 1.25rem;
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .stat-label-dashboard {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #f5b81b;
        opacity: 0.7;
    }
    
    .stat-icon-dashboard {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    
    .icon-apartment { background: rgba(245, 184, 27, 0.12); color: #f5b81b; }
    .icon-business { background: rgba(0, 229, 255, 0.12); color: #00e5ff; }
    .icon-pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .icon-users { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    
    .stat-value-dashboard {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.5rem;
    }
    
    .pending-value {
        color: #f59e0b;
    }
    
    .stat-footer-dashboard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.7rem;
    }
    
    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 700;
    }
    
    .stat-trend.positive {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    
    .stat-trend.warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }
    
    .stat-trend.negative {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    
    .stat-subtext {
        color: #64748b;
    }
    
    .stat-progress-bar {
        height: 3px;
        background: rgba(255, 255, 255, 0.08);
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #f5b81b, #ffcc44);
        transition: width 0.5s;
    }
    
    .progress-fill.warning {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }
    
    .chart-card-dashboard {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 28px;
        border: 1px solid rgba(245, 184, 27, 0.15);
        padding: 1.5rem;
        transition: all 0.3s;
    }
    
    .chart-card-dashboard:hover {
        border-color: rgba(245, 184, 27, 0.3);
        box-shadow: 0 8px 30px rgba(245, 184, 27, 0.08);
    }
    
    .chart-header-dashboard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    }
    
    .chart-title-dashboard {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        margin-bottom: 4px;
    }
    
    .chart-subtitle-dashboard {
        font-size: 0.7rem;
        color: #64748b;
    }
    
    .chart-filters {
        display: flex;
        gap: 0.5rem;
    }
    
    .year-filter-btn {
        padding: 6px 14px;
        background: rgba(15, 17, 21, 0.8);
        border: 1px solid rgba(245, 184, 27, 0.2);
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .year-filter-btn:hover {
        border-color: #f5b81b;
        color: #f5b81b;
    }
    
    .year-filter-btn.active {
        background: rgba(245, 184, 27, 0.15);
        border-color: #f5b81b;
        color: #f5b81b;
    }
    
    .chart-container-dashboard {
        height: 320px;
        position: relative;
    }
    
    .activities-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    @media (min-width: 1024px) {
        .activities-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .activity-card-dashboard {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 24px;
        border: 1px solid rgba(245, 184, 27, 0.12);
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .activity-card-dashboard:hover {
        border-color: rgba(245, 184, 27, 0.25);
    }
    
    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    }
    
    .activity-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    
    .activity-link {
        font-size: 0.7rem;
        font-weight: 600;
        color: #f5b81b;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .activity-link.warning {
        color: #f59e0b;
    }
    
    .activity-link:hover {
        transform: translateX(4px);
    }
    
    .activity-list {
        padding: 0.25rem;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .activity-item-dashboard {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 16px;
        transition: all 0.2s;
    }
    
    .activity-item-dashboard:hover {
        background: rgba(245, 184, 27, 0.05);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .activity-icon.success {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }
    
    .activity-icon.pending {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        margin: 0 0 4px 0;
    }
    
    .activity-type {
        font-size: 0.7rem;
        font-weight: 400;
        color: #64748b;
    }
    
    .activity-time {
        font-size: 0.65rem;
        color: #64748b;
    }
    
    .activity-badge {
        padding: 4px 12px;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .activity-badge.approved {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    
    .activity-badge.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }
    
    .activity-loading, .activity-empty {
        text-align: center;
        padding: 2rem;
        color: #64748b;
    }
    
    .activity-loading i, .activity-empty i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .quick-actions {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 24px;
        border: 1px solid rgba(245, 184, 27, 0.12);
        padding: 1.25rem;
    }
    
    .quick-actions-header {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    }
    
    .quick-actions-header h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: white;
        margin: 0 0 4px 0;
    }
    
    .quick-actions-header span {
        font-size: 0.7rem;
        color: #64748b;
    }
    
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .actions-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(15, 17, 21, 0.8);
        border: 1px solid rgba(245, 184, 27, 0.2);
        border-radius: 60px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #cbd5e1;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .quick-action-btn:hover {
        border-color: #f5b81b;
        color: #f5b81b;
        transform: translateY(-2px);
        text-decoration: none;
    }
    
    .quick-action-btn i {
        font-size: 0.8rem;
    }
    
    ::-webkit-scrollbar {
        width: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: #0f1115;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #f5b81b;
        border-radius: 10px;
    }
    
    ::selection {
        background: #f5b81b;
        color: #0a0c10;
    }
    
    @media (max-width: 768px) {
        .stat-value-dashboard {
            font-size: 1.5rem;
        }
        
        .dashboard-title {
            font-size: 1.35rem;
        }
        
        .chart-container-dashboard {
            height: 250px;
        }
    }
</style>
@endpush