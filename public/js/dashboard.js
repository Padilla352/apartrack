/**
 * Dashboard Module
 * Handles all dashboard functionality for barangay statistics
 */

class Dashboard {
    constructor() {
        this.statsCards = document.querySelectorAll('.stat-card');
        this.init();
    }

    /**
    
    /**
     * Attach click handlers to stat cards
     */
    attachCardClickHandlers() {
        if (this.statsCards.length === 0) return;

        this.statsCards.forEach(card => {
            card.addEventListener('click', (event) => {
                const cardData = this.getCardData(card);
                this.handleCardClick(cardData);
            });
        });
    }

    /**
     * Extract card data from DOM
     */
    getCardData(card) {
        return {
            brgyName: card.querySelector('.brgy-name').textContent,
            availableCount: card.querySelector('.available-count').textContent,
            totalUnits: card.querySelector('.total-label').textContent,
            element: card
        };
    }

    /**
     * Handle card click event
     */
    handleCardClick(cardData) {
        console.log(`Clicked on ${cardData.brgyName}`);
        console.log(`Available: ${cardData.availableCount}`);
        console.log(`${cardData.totalUnits}`);
        
        // Show modal with details
        this.showBarangayDetails(cardData);
    }

    /**
     * Load barangay data from API
     */
    async loadBarangayData() {
        try {
            // Show loading state
            this.showLoadingState();
            
            // Fetch data from your backend
            const response = await fetch('/api/barangays/stats');
            const data = await response.json();
            this.updateBarangayStats(data);
            
            // Hide loading state
            this.hideLoadingState();
            
        } catch (error) {
            console.error('Error loading barangay data:', error);
            this.showNotification('Failed to load barangay data', 'error');
            this.hideLoadingState();
        }
    }

    /**
     * Update barangay statistics
     */
    updateBarangayStats(data) {
        this.statsCards.forEach(card => {
            const brgyName = card.querySelector('.brgy-name').textContent;
            if (data[brgyName]) {
                const availableCount = card.querySelector('.available-count');
                const totalLabel = card.querySelector('.total-label');
                
                this.animateValueUpdate(availableCount, availableCount.textContent, data[brgyName].available);
                totalLabel.textContent = `Total: ${data[brgyName].total} Units`;
            }
        });
    }

    /**
     * Animate value update
     */
    animateValueUpdate(element, oldValue, newValue) {
        element.classList.add('updating');
        element.textContent = newValue;
        
        setTimeout(() => {
            element.classList.remove('updating');
        }, 300);
    }

    /**
     * Show loading state
     */
    showLoadingState() {
        this.statsCards.forEach(card => {
            card.classList.add('loading');
        });
    }

    /**
     * Hide loading state
     */
    hideLoadingState() {
        this.statsCards.forEach(card => {
            card.classList.remove('loading');
        });
    }

    /**
     * Show barangay details modal
     */
    showBarangayDetails(cardData) {
        // Create modal dynamically
        const modalHtml = `
            <div class="modal fade" id="barangayModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark text-white">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title">${cardData.brgyName}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <img src="${cardData.element.querySelector('.brgy-logo').src}" 
                                     alt="${cardData.brgyName}" 
                                     style="width: 100px; height: 100px; border-radius: 50%;">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <h6>Available Apartments</h6>
                                    <p class="h2 text-success">${cardData.availableCount}</p>
                                </div>
                                <div class="col-6">
                                    <h6>Total Units</h6>
                                    <p class="h2 text-info">${cardData.totalUnits.replace('Total: ', '')}</p>
                                </div>
                            </div>
                            <hr>
                            <h6>Additional Information:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Occupancy Rate:</strong> ${Math.round((parseInt(cardData.availableCount) / parseInt(cardData.totalUnits.replace('Total: ', '').replace(' Units', ''))) * 100)}%</li>
                                <li><strong>Location:</strong> ${cardData.brgyName}, City</li>
                                <li><strong>Last Updated:</strong> ${new Date().toLocaleDateString()}</li>
                            </ul>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="window.location.href='/barangay/${cardData.brgyName.toLowerCase().replace(/\s+/g, '-')}'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('barangayModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal using Bootstrap
        const modal = new bootstrap.Modal(document.getElementById('barangayModal'));
        modal.show();
        
        // Remove modal from DOM when hidden
        document.getElementById('barangayModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // You can integrate with a toast library like toastr
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Simple alert for now (replace with better notification system)
        if (type === 'error') {
            alert(`Error: ${message}`);
        }
    }

    /**
     * Refresh dashboard data
     */
    refreshDashboard() {
        this.showNotification('Refreshing dashboard data...', 'info');
        this.loadBarangayData();
    }

    /**
     * Export barangay data
     */
    exportData() {
        const barangayData = [];
        
        this.statsCards.forEach(card => {
            const cardData = this.getCardData(card);
            barangayData.push({
                barangay: cardData.brgyName,
                available_units: cardData.availableCount,
                total_units: cardData.totalUnits.replace('Total: ', '').replace(' Units', ''),
                logo_url: cardData.element.querySelector('.brgy-logo').src
            });
        });
        
        // Convert to JSON
        const dataStr = JSON.stringify(barangayData, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
        
        const exportFileDefaultName = `barangay-data-${new Date().toISOString().slice(0,10)}.json`;
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
        
        this.showNotification('Data exported successfully!', 'success');
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Dashboard;
}