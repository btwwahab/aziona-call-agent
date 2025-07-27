/* ===== DASHBOARD JAVASCRIPT ===== */

// Real-time clock update for last ping
function updateLastPing() {
    const pingElement = document.getElementById('lastPing');
    let seconds = 0;

    setInterval(() => {
        seconds += 1;
        if (seconds < 60) {
            pingElement.textContent = `${seconds} seconds ago`;
        } else {
            const minutes = Math.floor(seconds / 60);
            pingElement.textContent = `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        }
    }, 1000);
}

// Simulate status changes
function simulateStatusChanges() {
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');

    // Randomly change status every 30 seconds (for demo)
    setInterval(() => {
        const isOnline = Math.random() > 0.1; // 90% chance online

        if (isOnline) {
            statusDot.className = 'status-dot status-online';
            statusText.textContent = 'Agent Online';
        } else {
            statusDot.className = 'status-dot status-offline';
            statusText.textContent = 'Agent Offline';
        }
    }, 30000);
}

// Animate metrics with random updates
function animateMetrics() {
    const totalCalls = document.getElementById('totalCalls');
    const uptime = document.getElementById('uptime');
    const avgDuration = document.getElementById('avgDuration');
    const successRate = document.getElementById('successRate');

    setInterval(() => {
        // Slightly increment total calls
        let calls = parseInt(totalCalls.textContent);
        if (Math.random() > 0.7) {
            totalCalls.textContent = calls + 1;
        }

        // Randomly fluctuate other metrics slightly
        if (Math.random() > 0.8) {
            const uptimeVal = (99.5 + Math.random() * 0.5).toFixed(1);
            uptime.textContent = `${uptimeVal}%`;

            const duration = (3.0 + Math.random() * 1.0).toFixed(1);
            avgDuration.textContent = `${duration}m`;

            const success = (93.0 + Math.random() * 2.0).toFixed(1);
            successRate.textContent = `${success}%`;
        }
    }, 5000);
}

// Start call button interaction
function initializeCallButton() {
    const startCallBtn = document.getElementById('startCallBtn');

    startCallBtn.addEventListener('click', function (e) {
        e.preventDefault();

        // Add loading state
        const originalText = startCallBtn.innerHTML;
        startCallBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connecting...';
        startCallBtn.style.pointerEvents = 'none';

        // Simulate call initialization
        setTimeout(() => {
            startCallBtn.innerHTML = '<i class="fas fa-phone"></i> Call Active';
            startCallBtn.style.background = 'linear-gradient(135deg, #00ff88, #00d4ff)';

            // Reset after 3 seconds
            setTimeout(() => {
                startCallBtn.innerHTML = originalText;
                startCallBtn.style.background = 'linear-gradient(135deg, var(--accent-blue), var(--accent-purple))';
                startCallBtn.style.pointerEvents = 'auto';
            }, 3000);
        }, 2000);
    });
}

// Add shimmer effect to cards on hover
function addShimmerEffects() {
    const cards = document.querySelectorAll('.glass-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.classList.add('shimmer');
        });

        card.addEventListener('mouseleave', function () {
            this.classList.remove('shimmer');
        });
    });
}

// Control waveform animation based on status
function controlWaveform() {
    const waveform = document.getElementById('waveform');
    const statusDot = document.getElementById('statusDot');

    // Check status and control animation
    setInterval(() => {
        if (statusDot.classList.contains('status-online')) {
            waveform.style.opacity = '1';
        } else {
            waveform.style.opacity = '0.3';
        }
    }, 1000);
}

// Initialize all functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    updateLastPing();
    simulateStatusChanges();
    animateMetrics();
    initializeCallButton();
    addShimmerEffects();
    controlWaveform();

    // Add staggered fade-in animation to elements
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
    });
});

// Add some console styling for development
console.log('%c🤖 AI Voice Agent Dashboard Loaded', 'color: #00d4ff; font-size: 16px; font-weight: bold;');
console.log('%cBuilt with Groq + Retell AI', 'color: #00ff88; font-size: 12px;');