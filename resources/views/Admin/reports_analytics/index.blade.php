@extends('layouts.admin')

@section('content')
<div class="reports-container">
    <div class="reports-content-wrapper">
        
        {{-- Header Section --}}
        <div class="reports-header">
            <div>
                <h2 class="page-title">Reports & Analytics</h2>
                <p class="page-subtitle">Real-time property management insights</p>
            </div>
            <div class="header-buttons">
                <button onclick="window.print()" class="btn-export">
                    <i class="fas fa-download"></i> Export PDF
                </button>
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        {{-- Stats Cards Grid --}}
        <div class="stats-grid">
            <!-- Occupancy Rate Card -->
            <div class="stat-card glow-card">
                <div class="stat-icon-wrapper icon-occupancy">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Occupancy Rate</span>
                    <div class="stat-value-wrapper">
                        <h2 class="stat-value" id="occupancyRate">{{ $stats['occupancyRate'] ?? 0 }}<span class="stat-unit">%</span></h2>
                        <span class="stat-trend positive" id="occupancyTrend"><i class="fas fa-chart-line"></i> Loading...</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="occupancyFill" style="width: {{ $stats['occupancyRate'] ?? 0 }}%"></div>
                    </div>
                    <p class="stat-footer">
                        <i class="fas fa-calendar"></i> 
                        <span id="occupiedUnits">{{ $stats['occupiedUnits'] ?? 0 }}</span> / 
                        <span id="totalUnits">{{ $stats['totalUnits'] ?? 0 }}</span> units occupied
                    </p>
                </div>
            </div>

            <!-- Avg Verification Card -->
            <div class="stat-card glow-card">
                <div class="stat-icon-wrapper icon-verification">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Avg. Verification Time</span>
                    <div class="stat-value-wrapper">
                        <h2 class="stat-value" id="avgVerification">{{ $stats['avgVerification'] ?? '0' }}<span class="stat-unit">h</span></h2>
                        <span class="stat-trend neutral" id="verificationTarget"><i class="fas fa-hourglass-half"></i> Target: &lt;48h</span>
                    </div>
                    <div class="stat-footer-text">
                        <i class="fas fa-chart-line"></i> 
                        <span id="pendingVerifications">{{ $stats['pendingVerifications'] ?? 0 }}</span> pending verifications
                    </div>
                </div>
            </div>

            <!-- New Registrations Card -->
            <div class="stat-card glow-card">
                <div class="stat-icon-wrapper icon-registrations">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">New Registrations</span>
                    <div class="stat-value-wrapper">
                        <h2 class="stat-value" id="newRegistrations">{{ $stats['newRegistrations'] ?? 0 }}</h2>
                        <span class="stat-trend positive" id="registrationsTrend"><i class="fas fa-chart-line"></i> This month</span>
                    </div>
                    <div class="stat-footer-text">
                        <i class="fas fa-calendar"></i> 
                        <span id="lastMonthRegistrations">{{ $stats['lastMonthRegistrations'] ?? 0 }}</span> last month
                    </div>
                </div>
            </div>

            <!-- Rejected Permits Card -->
            <div class="stat-card glow-card">
                <div class="stat-icon-wrapper icon-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Rejected Permits</span>
                    <div class="stat-value-wrapper">
                        <h2 class="stat-value rejected" id="rejectedPermits">{{ $stats['rejectedPermits'] ?? 0 }}</h2>
                        <span class="stat-trend negative" id="rejectionRate"><i class="fas fa-percent"></i> {{ $stats['rejectionRate'] ?? 0 }}% Total</span>
                    </div>
                    <div class="stat-footer-text">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <span id="totalPermits">{{ $stats['totalPermits'] ?? 0 }}</span> total applications
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Grid --}}
        <div class="charts-grid">
            <!-- Growth Trends Chart -->
            <div class="chart-card large">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">System Growth Trends</h3>
                        <p class="chart-subtitle">New registrations over time</p>
                    </div>
                    <select class="chart-select" id="periodSelect">
                        <option value="6">Last 6 Months</option>
                        <option value="12">Last 12 Months</option>
                        <option value="3">Last Quarter</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <!-- Property Distribution Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Property Distribution</h3>
                        <p class="chart-subtitle">By property type</p>
                    </div>
                    <div class="legend-indicator">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div class="chart-container doughnut-container">
                    <canvas id="distChart"></canvas>
                </div>
                <div class="custom-legend" id="customLegend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #f5b81b;"></span>
                        <span class="legend-label">Apartments</span>
                        <span class="legend-value" id="apartmentPercent">0%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #00e5ff;"></span>
                        <span class="legend-label">Boarding House</span>
                        <span class="legend-value" id="boardingPercent">0%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #b388ff;"></span>
                        <span class="legend-label">Commercial</span>
                        <span class="legend-value" id="commercialPercent">0%</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Recent Activity Section --}}
        <div class="recent-activity">
            <div class="activity-header">
                <h3 class="activity-title">Recent Activity</h3>
                <span class="activity-badge">Last 7 days</span>
            </div>
            <div class="activity-list" id="activityList">
                <div class="activity-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Store chart instances
    let growthChart = null;
    let distChart = null;
    
    // Colors
    const colors = {
        primary: '#f5b81b',
        secondary: '#00e5ff',
        tertiary: '#b388ff',
        success: '#10b981',
        danger: '#ef4444',
        warning: '#f59e0b',
        background: '#0a0c10',
        cardBg: '#0f1115'
    };

    // Create gradient for growth chart
    const createGlowGradient = (ctx) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(245, 184, 27, 0.35)');
        gradient.addColorStop(0.5, 'rgba(245, 184, 27, 0.1)');
        gradient.addColorStop(1, 'rgba(15, 23, 42, 0.02)');
        return gradient;
    };

    // Fetch real-time data from server
    async function fetchRealTimeData() {
        try {
            const response = await fetch('{{ route("reports.analytics.data") }}');
            const data = await response.json();
            
            if (data.success) {
                // Update stats cards
                updateStatsCards(data.stats);
                
                // Update charts
                await updateGrowthChart(data.growthData, document.getElementById('periodSelect').value);
                await updateDistributionChart(data.distributionData);
                
                // Update recent activity
                updateRecentActivity(data.recentActivity);
                
                // Update last updated time
                updateLastUpdated(data.lastUpdated);
            }
        } catch (error) {
            console.error('Error fetching real-time data:', error);
        }
    }

    // Update stats cards with real data
    function updateStatsCards(stats) {
        // Occupancy Rate
        document.getElementById('occupancyRate').innerHTML = `${stats.occupancyRate}<span class="stat-unit">%</span>`;
        document.getElementById('occupancyFill').style.width = `${stats.occupancyRate}%`;
        document.getElementById('occupiedUnits').textContent = stats.occupiedUnits;
        document.getElementById('totalUnits').textContent = stats.totalUnits;
        
        // Trends
        if (stats.occupancyTrend !== undefined) {
            const trendElem = document.getElementById('occupancyTrend');
            trendElem.innerHTML = `<i class="fas ${stats.occupancyTrend >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}"></i> ${Math.abs(stats.occupancyTrend)}% vs last month`;
            trendElem.className = `stat-trend ${stats.occupancyTrend >= 0 ? 'positive' : 'negative'}`;
        }
        
        // Verification
        document.getElementById('avgVerification').innerHTML = `${stats.avgVerification}<span class="stat-unit">h</span>`;
        document.getElementById('pendingVerifications').textContent = stats.pendingVerifications || 0;
        
        // Registrations
        document.getElementById('newRegistrations').textContent = stats.newRegistrations;
        document.getElementById('lastMonthRegistrations').textContent = stats.lastMonthRegistrations || 0;
        
        // Permits
        document.getElementById('rejectedPermits').textContent = stats.rejectedPermits;
        document.getElementById('rejectionRate').innerHTML = `<i class="fas fa-percent"></i> ${stats.rejectionRate}% Total`;
        document.getElementById('totalPermits').textContent = stats.totalPermits;
    }

    // Update growth chart with real data
    async function updateGrowthChart(growthData, period) {
        const ctx = document.getElementById('growthChart').getContext('2d');
        
        if (growthChart) {
            growthChart.destroy();
        }
        
        const chartData = growthData[period] || growthData['6'];
        
        growthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'New Registrations',
                    data: chartData.data,
                    borderColor: colors.primary,
                    borderWidth: 3,
                    backgroundColor: createGlowGradient(ctx),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 8,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#0f1115',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#ffcc44',
                    pointHoverBorderColor: '#0f1115',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 21, 0.95)',
                        titleColor: colors.primary,
                        bodyColor: '#e2e8f0',
                        borderColor: `${colors.primary}40`,
                        borderWidth: 1,
                        cornerRadius: 12,
                        callbacks: {
                            label: (context) => `New Registrations: ${context.raw}`
                        }
                    }
                },
                scales: {
                    y: { 
                        border: { display: false }, 
                        grid: { color: 'rgba(245, 184, 27, 0.08)' }, 
                        ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8', stepSize: 10 },
                        title: { display: true, text: 'Number of Registrations', color: `${colors.primary}80`, font: { size: 10, weight: '600' } }
                    },
                    x: { 
                        border: { display: false }, 
                        grid: { display: false }, 
                        ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' },
                        title: { display: true, text: 'Month', color: `${colors.primary}80`, font: { size: 10, weight: '600' } }
                    }
                }
            }
        });
    }

    // Update distribution chart with real data
    async function updateDistributionChart(distData) {
        const ctx = document.getElementById('distChart').getContext('2d');
        
        if (distChart) {
            distChart.destroy();
        }
        
        const total = distData.apartments + distData.boarding + distData.commercial;
        const aptPercent = total > 0 ? Math.round((distData.apartments / total) * 100) : 0;
        const boardPercent = total > 0 ? Math.round((distData.boarding / total) * 100) : 0;
        const commPercent = total > 0 ? Math.round((distData.commercial / total) * 100) : 0;
        
        // Update legend
        document.getElementById('apartmentPercent').textContent = `${aptPercent}%`;
        document.getElementById('boardingPercent').textContent = `${boardPercent}%`;
        document.getElementById('commercialPercent').textContent = `${commPercent}%`;
        
        distChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Apartments', 'Boarding House', 'Commercial'],
                datasets: [{
                    data: [distData.apartments, distData.boarding, distData.commercial],
                    backgroundColor: [colors.primary, colors.secondary, colors.tertiary],
                    borderWidth: 0,
                    hoverOffset: 12,
                    cutout: '70%',
                    borderRadius: 8,
                    spacing: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 21, 0.95)',
                        titleColor: colors.primary,
                        bodyColor: '#e2e8f0',
                        borderColor: `${colors.primary}40`,
                        borderWidth: 1,
                        cornerRadius: 12,
                        callbacks: {
                            label: (context) => `${context.label}: ${context.raw} units (${Math.round((context.raw / total) * 100)}%)`
                        }
                    }
                },
                animation: { animateRotate: true, animateScale: true, duration: 800 }
            }
        });
    }

    // Update recent activity list
    function updateRecentActivity(activities) {
        const activityList = document.getElementById('activityList');
        
        if (!activities || activities.length === 0) {
            activityList.innerHTML = `
                <div class="activity-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No recent activity</p>
                </div>
            `;
            return;
        }
        
        activityList.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <div class="activity-icon ${activity.type}">
                    <i class="fas ${activity.icon}"></i>
                </div>
                <div class="activity-details">
                    <p class="activity-message">${activity.message}</p>
                    <span class="activity-time">${activity.time_ago}</span>
                </div>
            </div>
        `).join('');
    }

    // Update last updated timestamp
    function updateLastUpdated(lastUpdated) {
        const existingBadge = document.querySelector('.activity-badge');
        if (existingBadge && lastUpdated) {
            existingBadge.textContent = `Updated ${lastUpdated}`;
        }
    }

    // Period selector change handler
    document.getElementById('periodSelect').addEventListener('change', async function() {
        const period = this.value;
        try {
            const response = await fetch(`{{ route("reports.analytics.data") }}?period=${period}`);
            const data = await response.json();
            if (data.success) {
                await updateGrowthChart(data.growthData, period);
            }
        } catch (error) {
            console.error('Error updating period:', error);
        }
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('%c📊 APARTrack Analytics | Real-time Dashboard', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
        
        // Initial data load
        await fetchRealTimeData();
        
        // Set up auto-refresh every 30 seconds
        setInterval(fetchRealTimeData, 30000);
        
        // Refresh button handler
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', async function() {
                this.classList.add('spinning');
                await fetchRealTimeData();
                setTimeout(() => this.classList.remove('spinning'), 800);
            });
        }
        
        // Animate cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-in');
        });
    });
</script>
@endpush

<style>
/* ========== GLOW DARK THEME - REPORTS & ANALYTICS ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.reports-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1rem;
}

html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
}

@media (min-width: 768px) {
    .reports-container {
        padding: 2rem;
    }
}

.reports-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.reports-header {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .reports-header {
        flex-direction: row;
        align-items: center;
    }
}

.page-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
    margin: 0;
}

.page-subtitle {
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 500;
    margin: 0.25rem 0 0;
}

.header-buttons {
    display: flex;
    gap: 0.75rem;
}

.btn-export, .btn-refresh {
    padding: 0.625rem 1.25rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.3);
    border-radius: 40px;
    color: #cbd5e1;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.btn-export i, .btn-refresh i {
    margin-right: 0.5rem;
}

.btn-export:hover, .btn-refresh:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(245, 184, 27, 0.15);
}

.btn-refresh.spinning i {
    animation: spin 0.6s ease-in-out;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1.25rem;
    transition: all 0.3s ease;
    display: flex;
    gap: 1rem;
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

.stat-card.animate-in {
    opacity: 1;
    transform: translateY(0);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card:hover {
    border-color: rgba(245, 184, 27, 0.4);
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
}

.stat-icon-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.icon-occupancy {
    background: rgba(245, 184, 27, 0.15);
    color: #f5b81b;
}

.icon-verification {
    background: rgba(0, 229, 255, 0.15);
    color: #00e5ff;
}

.icon-registrations {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.icon-rejected {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.625rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.7;
    display: block;
    margin-bottom: 0.5rem;
}

.stat-value-wrapper {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
}

.stat-value.rejected {
    color: #ef4444;
}

.stat-unit {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
}

.stat-trend {
    font-size: 0.5625rem;
    font-weight: 800;
    padding: 0.125rem 0.5rem;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-trend.positive {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.stat-trend.neutral {
    background: rgba(245, 184, 27, 0.1);
    color: #f5b81b;
}

.stat-trend.negative {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 4px;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #f5b81b, #ffcc44);
    border-radius: 4px;
    transition: width 1s ease;
}

.stat-footer, .stat-footer-text {
    font-size: 0.5625rem;
    font-weight: 600;
    color: #64748b;
    margin: 0;
}

.stat-footer i, .stat-footer-text i {
    margin-right: 0.25rem;
    font-size: 0.5rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 1024px) {
    .charts-grid {
        grid-template-columns: 2fr 1fr;
    }
}

.chart-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.chart-card:hover {
    border-color: rgba(245, 184, 27, 0.3);
    box-shadow: 0 8px 30px rgba(245, 184, 27, 0.08);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.chart-title {
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.chart-subtitle {
    font-size: 0.7rem;
    color: #64748b;
    margin: 0.25rem 0 0;
}

.chart-select {
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 40px;
    padding: 0.375rem 0.75rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: #cbd5e1;
    cursor: pointer;
    outline: none;
    font-family: 'Inter', sans-serif;
}

.chart-select:focus {
    border-color: #f5b81b;
}

.chart-container {
    height: 280px;
    position: relative;
}

.doughnut-container {
    height: 200px;
}

.legend-indicator {
    width: 32px;
    height: 32px;
    background: rgba(245, 184, 27, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f5b81b;
}

.custom-legend {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.375rem 0.75rem;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 4px;
}

.legend-label {
    flex: 1;
    font-size: 0.7rem;
    font-weight: 500;
    color: #cbd5e1;
}

.legend-value {
    font-size: 0.8rem;
    font-weight: 700;
    color: #ffffff;
}

/* Recent Activity Section */
.recent-activity {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1.5rem;
    margin-top: 1rem;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
}

.activity-title {
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.activity-badge {
    font-size: 0.625rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    background: rgba(245, 184, 27, 0.12);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 40px;
    color: #f5b81b;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 16px;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: rgba(245, 184, 27, 0.05);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.activity-icon.tenant {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.activity-icon.complaint {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.activity-icon.business {
    background: rgba(0, 229, 255, 0.15);
    color: #00e5ff;
}

.activity-icon.apartment {
    background: rgba(245, 184, 27, 0.15);
    color: #f5b81b;
}

.activity-details {
    flex: 1;
}

.activity-message {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #e2e8f0;
    margin: 0 0 0.25rem;
}

.activity-time {
    font-size: 0.625rem;
    color: #64748b;
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
    .reports-container {
        padding: 1rem;
    }
    
    .activity-item {
        flex-wrap: wrap;
    }
    
    .activity-icon {
        width: 36px;
        height: 36px;
    }
    
    .activity-message {
        font-size: 0.75rem;
    }
}
</style>
@endsection