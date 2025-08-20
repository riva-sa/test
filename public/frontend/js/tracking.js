// tracking.js - Add this to your frontend assets

class TrackingManager {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '/api/tracking';
        this.debug = options.debug || false;
        this.sessionData = this.getSessionData();
        
        // Initialize tracking
        this.init();
    }

    init() {
        // Track page load
        this.trackPageLoad();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Track user engagement
        this.trackEngagement();
    }

    getSessionData() {
        return {
            sessionId: this.getOrCreateSessionId(),
            timestamp: Date.now(),
            userAgent: navigator.userAgent,
            referrer: document.referrer || null,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            },
            screen: {
                width: screen.width,
                height: screen.height
            }
        };
    }

    getOrCreateSessionId() {
        let sessionId = sessionStorage.getItem('tracking_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('tracking_session_id', sessionId);
        }
        return sessionId;
    }

    async track(type, id, event, metadata = {}) {
        const payload = {
            type,
            id,
            event,
            metadata: {
                ...metadata,
                ...this.sessionData,
                url: window.location.href,
                path: window.location.pathname,
                timestamp: Date.now()
            }
        };

        try {
            const response = await fetch(this.baseUrl + '/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (this.debug) {
                console.log('Tracking event sent:', payload, result);
            }

            return result;
        } catch (error) {
            if (this.debug) {
                console.error('Tracking error:', error);
            }
            // Silently fail in production
            return null;
        }
    }

    // Specific tracking methods
    async trackUnitView(unitId, metadata = {}) {
        return this.track('unit', unitId, 'view', metadata);
    }

    async trackUnitShow(unitId, metadata = {}) {
        return this.track('unit', unitId, 'show', metadata);
    }

    async trackUnitOrder(unitId, metadata = {}) {
        return this.track('unit', unitId, 'order', metadata);
    }

    async trackProjectVisit(projectId, metadata = {}) {
        return this.track('project', projectId, 'visit', metadata);
    }

    async trackProjectOrderShow(projectId, metadata = {}) {
        return this.track('project', projectId, 'show', metadata);
    }

    trackPageLoad() {
        // Extract tracking data from current page
        const pageData = this.extractPageData();
        
        if (pageData.type && pageData.id) {
            if (pageData.type === 'project') {
                this.trackProjectVisit(pageData.id, {
                    page_type: 'project_page',
                    load_time: performance.timing.loadEventEnd - performance.timing.navigationStart
                });
            } else if (pageData.type === 'unit') {
                this.trackUnitView(pageData.id, {
                    page_type: 'unit_page',
                    load_time: performance.timing.loadEventEnd - performance.timing.navigationStart
                });
            }
        }
    }

    extractPageData() {
        // Try to extract tracking data from various sources
        const dataElement = document.querySelector('[data-tracking]');
        if (dataElement) {
            const trackingData = JSON.parse(dataElement.getAttribute('data-tracking'));
            return trackingData;
        }

        // Extract from URL patterns
        const path = window.location.pathname;
        
        // Pattern: /projects/{slug}
        const projectMatch = path.match(/\/projects\/([^\/]+)/);
        if (projectMatch && document.querySelector('[data-project-id]')) {
            return {
                type: 'project',
                id: parseInt(document.querySelector('[data-project-id]').getAttribute('data-project-id'))
            };
        }

        // Pattern: /units/{id}
        const unitMatch = path.match(/\/units\/(\d+)/);
        if (unitMatch) {
            return {
                type: 'unit',
                id: parseInt(unitMatch[1])
            };
        }

        return {};
    }

    setupEventListeners() {
        // Track unit popup opens
        document.addEventListener('click', (event) => {
            const unitBtn = event.target.closest('[data-unit-id]');
            if (unitBtn) {
                const unitId = parseInt(unitBtn.getAttribute('data-unit-id'));
                this.trackUnitShow(unitId, {
                    trigger: 'button_click',
                    element: unitBtn.tagName.toLowerCase()
                });
            }

            // Track project order popup opens
            const orderBtn = event.target.closest('[data-project-order]');
            if (orderBtn) {
                const projectId = parseInt(orderBtn.getAttribute('data-project-order'));
                this.trackProjectOrderShow(projectId, {
                    trigger: 'order_button_click'
                });
            }
        });

        // Track form submissions (orders)
        document.addEventListener('submit', (event) => {
            const form = event.target;
            
            if (form.matches('[data-unit-order-form]')) {
                const unitId = parseInt(form.getAttribute('data-unit-id'));
                const formData = new FormData(form);
                
                this.trackUnitOrder(unitId, {
                    trigger: 'form_submit',
                    purchase_type: formData.get('purchaseType'),
                    purchase_purpose: formData.get('purchasePurpose')
                });
            }
        });

        // Track Livewire events
        if (window.Livewire) {
            Livewire.on('loadUnit', (data) => {
                this.trackUnitShow(data.unitId, {
                    trigger: 'livewire_event'
                });
            });

            Livewire.on('UnitOrderOpen', (data) => {
                this.trackProjectOrderShow(data.projectId, {
                    trigger: 'livewire_event'
                });
            });
        }
    }

    trackEngagement() {
        let startTime = Date.now();
        let isActive = true;
        let scrollDepth = 0;

        // Track time on page
        const trackTimeOnPage = () => {
            if (isActive) {
                const timeSpent = Date.now() - startTime;
                localStorage.setItem('page_time_' + window.location.pathname, timeSpent.toString());
            }
        };

        // Track scroll depth
        const trackScroll = () => {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            if (scrollPercent > scrollDepth) {
                scrollDepth = scrollPercent;
            }
        };

        // Track when user becomes inactive
        const handleVisibilityChange = () => {
            if (document.hidden) {
                isActive = false;
                trackTimeOnPage();
            } else {
                isActive = true;
                startTime = Date.now();
            }
        };

        // Event listeners
        window.addEventListener('scroll', trackScroll, { passive: true });
        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('beforeunload', () => {
            trackTimeOnPage();
            
            // Send engagement data
            const pageData = this.extractPageData();
            if (pageData.type && pageData.id) {
                const engagementData = {
                    time_spent: Date.now() - startTime,
                    scroll_depth: scrollDepth,
                    page_url: window.location.href
                };

                // Use sendBeacon for reliable tracking on page unload
                navigator.sendBeacon(
                    this.baseUrl + '/track',
                    JSON.stringify({
                        type: pageData.type,
                        id: pageData.id,
                        event: 'engagement',
                        metadata: engagementData
                    })
                );
            }
        });
    }

    // Analytics helpers
    async getAnalytics(options = {}) {
        try {
            const params = new URLSearchParams(options);
            const response = await fetch(`${this.baseUrl}/analytics?${params}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch analytics:', error);
            return null;
        }
    }

    async getPopularUnits(limit = 10, days = 30) {
        try {
            const response = await fetch(`${this.baseUrl}/popular/units?limit=${limit}&days=${days}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch popular units:', error);
            return null;
        }
    }

    async getPopularProjects(limit = 10, days = 30) {
        try {
            const response = await fetch(`${this.baseUrl}/popular/projects?limit=${limit}&days=${days}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch popular projects:', error);
            return null;
        }
    }
}

// Auto-initialize tracking
document.addEventListener('DOMContentLoaded', () => {
    window.trackingManager = new TrackingManager({
        debug: document.querySelector('meta[name="app-env"]')?.getAttribute('content') !== 'production'
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TrackingManager;
}