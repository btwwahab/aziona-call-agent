@extends('layouts.app')
@section('title', 'AI Calling Agent Dashboard')
@section('content')
<!-- Main Content -->
<div class="container main-container">
    <div class="row">
        <!-- Agent Info Card -->
        <div class="col-12 fade-in">
            <div class="glass-card agent-info-card">
                <div class="row align-items-center">
                    <div class="col-lg-3 text-center mb-4 mb-lg-0">
                        <div class="agent-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="mb-2">Aziona Alpha</h4>
                        <p class="text-secondary mb-0">Powered by Aziona</p>
                    </div>
                    <div class="col-lg-6 text-center mb-4 mb-lg-0">
                        <div class="waveform" id="waveform">
                            <div class="waveform-bar" style="--i: 1"></div>
                            <div class="waveform-bar" style="--i: 2"></div>
                            <div class="waveform-bar" style="--i: 3"></div>
                            <div class="waveform-bar" style="--i: 4"></div>
                            <div class="waveform-bar" style="--i: 5"></div>
                            <div class="waveform-bar" style="--i: 6"></div>
                            <div class="waveform-bar" style="--i: 7"></div>
                            <div class="waveform-bar" style="--i: 8"></div>
                        </div>
                        <p class="mb-2"><strong>Status:</strong> Ready for calls</p>
                        <p class="mb-0"><strong>Last Ping:</strong> <span id="lastPing">2 seconds ago</span></p>
                    </div>
                    <div class="col-lg-3">
                        <div class="call-controls">
                            <input type="text" id="phoneInput" class="phone-input" placeholder="Enter phone number">
                            <a href="#" class="btn-call" id="startCallBtn">
                                <i class="fas fa-phone"></i>
                                Start Call
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <!-- Metrics Row -->
        <div class="col-lg-3 col-md-6 fade-in">
            <div class="metric-card">
                <div class="metric-value" id="totalCalls">847</div>
                <div class="metric-label">Total Calls</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in">
            <div class="metric-card">
                <div class="metric-value" id="uptime">99.8%</div>
                <div class="metric-label">Uptime</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in">
            <div class="metric-card">
                <div class="metric-value" id="avgDuration">3.4m</div>
                <div class="metric-label">Avg Duration</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 fade-in">
            <div class="metric-card">
                <div class="metric-value" id="successRate">94.2%</div>
                <div class="metric-label">Success Rate</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-8 fade-in">
            <!-- Schedule Call Section -->
            <div class="glass-card schedule-section fade-in">
                <h5 class="mb-4 text-center">
                    <i class="fas fa-calendar-plus me-2" style="color: var(--accent-green);"></i>
                    Schedule a Call
                </h5>
                <form id="scheduleForm" class="schedule-form">
                    <input type="text" id="schedulePhone" placeholder="Phone number (e.g. +12025550123)" required>
                    <input type="email" id="scheduleEmail" placeholder="Email address (for notifications)" required>
                    <input type="text" id="scheduleDate" class="flatpickr-date" placeholder="Select date" required autocomplete="off">
                    <input type="time" id="scheduleTime" placeholder="Select time" required>
                    <input type="text" id="scheduleNote" placeholder="Note (optional)">
                    <button type="submit" class="btn-schedule">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Schedule Call
                    </button>
                </form>
            </div>
        </div>
        <!-- System Activity & Schedule -->
        <div class="col-lg-4">
            <!-- System Activity -->
            <div class="glass-card fade-in">
                <h5 class="mb-4">
                    <i class="fas fa-activity me-2" style="color: var(--accent-green);"></i>
                    System Activity
                </h5>
                <div class="activity-item">
                    <div class="activity-icon activity-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Call Completed</div>
                        <small class="text-secondary">2 minutes ago</small>
                    </div>
                </div>
                <!-- <div class="activity-item">
                    <div class="activity-icon activity-info">
                        <i class="fas fa-sync"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Model Updated</div>
                        <small class="text-secondary">15 minutes ago</small>
                    </div>
                </div> -->
                <div class="activity-item">
                    <div class="activity-icon activity-success">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Incoming Call</div>
                        <small class="text-secondary">18 minutes ago</small>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon activity-warning">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Connection Issue</div>
                        <small class="text-secondary">1 hour ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Call Logs -->
        <div class="col-lg-12 fade-in">
            <div class="glass-card">
                <h5 class="mb-4">
                    <i class="fas fa-history me-2" style="color: var(--accent-cyan);"></i>
                    Recent Call Logs
                </h5>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom" id="callLogsTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Transcript Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Call logs will be loaded here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="row">
        <!-- Scheduled Calls -->
        <div class="col-lg-12 fade-in">
            <div class="glass-card">
                <h5 class="mb-4">
                    <i class="fas fa-calendar-alt me-2" style="color: var(--accent-green);"></i>
                    Scheduled Calls
                </h5>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom" id="scheduledCallsTable">
                            <thead>
                                <tr>
                                    <th>Scheduled For</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Scheduled calls will be loaded here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Appointments Section (Separate) -->
        <div class="col-lg-12 fade-in">
            <div class="glass-card">
                <h5 class="mb-4">
                    <i class="fas fa-calendar-alt me-2" style="color: var(--accent-green);"></i>
                    Appointments
                </h5>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-custom" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>Date & Time (UTC)</th>
                                    <th>Person</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Appointments will be loaded here by JS or Blade -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('.flatpickr-date', {
                dateFormat: 'Y-m-d',
                minDate: 'today',
            });
        }
    });
</script>
@endpush
@endsection
