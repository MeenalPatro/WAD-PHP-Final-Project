<?php
require_once 'config/database.php';
require_once 'includes/helpers.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$page_title = 'Dashboard';
$current_page = 'dashboard';
$stats = getDashboardStats($db, $role, $user_id);

require_once 'includes/app_header.php';
?>

<div class="dashboard-stat-grid row g-4 mb-4" data-aos="fade-up">
    <?php if ($role === 'admin'): ?>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div><div class="stat-value"><?php echo $stats['total_requests'] ?? 0; ?></div><div class="stat-label">Total Requests</div></div></div>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon orange"><i class="fas fa-spinner"></i></div><div class="stat-value"><?php echo $stats['active_requests'] ?? 0; ?></div><div class="stat-label">Active</div></div></div>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon blue"><i class="fas fa-hands-helping"></i></div><div class="stat-value"><?php echo $stats['total_volunteers'] ?? 0; ?></div><div class="stat-label">Volunteers</div></div></div>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon green"><i class="fas fa-building"></i></div><div class="stat-value"><?php echo $stats['total_ngos'] ?? 0; ?></div><div class="stat-label">NGOs</div></div></div>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon"><i class="fas fa-search"></i></div><div class="stat-value"><?php echo $stats['missing_persons'] ?? 0; ?></div><div class="stat-label">Missing</div></div></div>
        <div class="col-6 col-lg-2"><div class="stat-card"><div class="stat-icon green"><i class="fas fa-heart"></i></div><div class="stat-value"><?php echo $stats['safe_checkins'] ?? 0; ?></div><div class="stat-label">Check-ins 24h</div></div></div>
    <?php elseif ($role === 'citizen'): ?>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-file-alt"></i></div><div class="stat-value"><?php echo $stats['my_requests'] ?? 0; ?></div><div class="stat-label">My Requests</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon orange"><i class="fas fa-clock"></i></div><div class="stat-value"><?php echo $stats['pending'] ?? 0; ?></div><div class="stat-label">Pending</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon green"><i class="fas fa-check"></i></div><div class="stat-value"><?php echo $stats['completed'] ?? 0; ?></div><div class="stat-label">Completed</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon blue"><i class="fas fa-shield-alt"></i></div><div class="stat-value"><?php echo $stats['checkins'] ?? 0; ?></div><div class="stat-label">Safe Check-ins</div></div></div>
    <?php elseif ($role === 'volunteer'): ?>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="fas fa-tasks"></i></div><div class="stat-value"><?php echo $stats['active_tasks'] ?? 0; ?></div><div class="stat-label">Active Tasks</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-double"></i></div><div class="stat-value"><?php echo $stats['completed_tasks'] ?? 0; ?></div><div class="stat-label">Completed</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon blue"><i class="fas fa-bullhorn"></i></div><div class="stat-value"><?php echo $stats['available_nearby'] ?? 0; ?></div><div class="stat-label">Open Urgent</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon orange"><i class="fas fa-circle"></i></div><div class="stat-value" style="font-size:1.2rem;"><?php echo ucfirst($stats['availability'] ?? 'offline'); ?></div><div class="stat-label">Status</div></div></div>
    <?php elseif ($role === 'ngo'): ?>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon"><i class="fas fa-boxes"></i></div><div class="stat-value"><?php echo $stats['total_resources'] ?? 0; ?></div><div class="stat-label">Total Resources</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon green"><i class="fas fa-campground"></i></div><div class="stat-value"><?php echo $stats['active_camps'] ?? 0; ?></div><div class="stat-label">Active Camps</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon blue"><i class="fas fa-certificate"></i></div><div class="stat-value" style="font-size:1.2rem;"><?php echo $stats['verified'] ?? 'Pending'; ?></div><div class="stat-label">Verification</div></div></div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- REAL-TIME MULTI-USER TRACKING MAP (NEW)      -->
<!-- ============================================ -->
<div class="page-card mb-4" data-aos="fade-up">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <i class="fas fa-map-marked-alt me-2"></i> 
                Real-Time Live Tracking Map
                <span class="badge bg-danger ms-2" id="liveBadge">
                    <i class="fas fa-circle" style="font-size: 8px;"></i> LIVE
                </span>
            </div>
            <div class="map-filters mt-2 mt-sm-0">
                <button class="btn btn-sm btn-outline-light filter-btn active" data-filter="all" onclick="filterMapByType('all')">
                    <i class="fas fa-globe"></i> All
                </button>
                <button class="btn btn-sm btn-outline-light filter-btn" data-filter="emergencies" onclick="filterMapByType('emergencies')">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Emergencies
                </button>
                <button class="btn btn-sm btn-outline-light filter-btn" data-filter="volunteers" onclick="filterMapByType('volunteers')">
                    <i class="fas fa-hands-helping text-success"></i> Volunteers
                </button>
                <button class="btn btn-sm btn-outline-light filter-btn" data-filter="ngos" onclick="filterMapByType('ngos')">
                    <i class="fas fa-building text-primary"></i> NGOs/Camps
                </button>
                <button class="btn btn-sm btn-outline-light filter-btn" data-filter="citizens" onclick="filterMapByType('citizens')">
                    <i class="fas fa-user-check text-info"></i> Safe Citizens
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="realtimeMap" style="height: 550px; width: 100%;"></div>
    </div>
    <div class="card-footer bg-light">
        <div class="row text-center">
            <div class="col-3">
                <small class="text-muted">🚨 Active Emergencies</small>
                <h5 class="mb-0 text-danger" id="emergencyCount">0</h5>
            </div>
            <div class="col-3">
                <small class="text-muted">🤝 Online Volunteers</small>
                <h5 class="mb-0 text-success" id="volunteerCount">0</h5>
            </div>
            <div class="col-3">
                <small class="text-muted">🏢 Active NGOs</small>
                <h5 class="mb-0 text-primary" id="ngoCount">0</h5>
            </div>
            <div class="col-3">
                <small class="text-muted">✅ Safe Check-ins</small>
                <h5 class="mb-0 text-info" id="citizenCount">0</h5>
            </div>
        </div>
    </div>
</div>

<div class="page-card mb-4" data-aos="fade-up">
    <div class="card-header bg-dark"><i class="fas fa-list me-2"></i> Recent Emergency Requests</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Title</th><th>Type</th><th>Priority</th><th>Status</th><th>Reporter</th><th>Action</th></tr></thead>
                <tbody id="emergencyTableBody"><tr><td colspan="7" class="text-center py-4">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6" data-aos="fade-up">
        <div class="page-card">
            <div class="card-header bg-info"><i class="fas fa-chart-bar me-2"></i> Requests by Category</div>
            <div class="card-body"><canvas id="categoryChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6" data-aos="fade-up">
        <div class="page-card">
            <div class="card-header bg-success"><i class="fas fa-chart-pie me-2"></i> Priority Distribution</div>
            <div class="card-body"><canvas id="priorityChart" height="220"></canvas></div>
        </div>
    </div>
</div>

<style>
/* Real-time Map Styles */
@keyframes pulse-marker {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulse-blue {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}
.custom-marker { background: transparent !important; border: none !important; }
.live-dot {
    position: absolute; bottom: -2px; right: -2px;
    width: 10px; height: 10px; background: #4ade80;
    border-radius: 50%; border: 2px solid white;
    animation: pulse-dot 1s infinite;
}
.map-legend {
    background: white; padding: 12px; border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 12px; min-width: 140px;
}
.legend-title { font-weight: bold; margin-bottom: 8px; }
.legend-marker {
    display: inline-block; width: 16px; height: 16px;
    border-radius: 50%; margin-right: 8px;
}
.legend-marker.emergency { background: #dc3545; }
.legend-marker.volunteer { background: #28a745; }
.legend-marker.ngo { background: #0d6efd; }
.legend-marker.citizen { background: #17a2b8; }
.pulse-dot {
    display: inline-block; width: 10px; height: 10px;
    background: #4ade80; border-radius: 50%; margin-right: 8px;
    animation: pulse-dot 1s infinite;
}
.filter-btn { margin: 0 2px; transition: all 0.3s; }
.filter-btn.active { background: #dc3545; color: white; border-color: #dc3545; }
#liveBadge { animation: pulse-dot 1.5s infinite; }
</style>

<script>
// ============================================
// REAL-TIME MAP JAVASCRIPT
// ============================================
let realtimeMap = null;
let mapLayers = { emergencies: [], volunteers: [], ngos: [], citizens: [] };
let refreshInterval = null;
let selectedFilter = 'all';
let userLocationMarker = null;
let watchId = null;

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getMarkerIcon(type, color, isLive = false) {
    const colors = {
        danger: '#dc3545', warning: '#ffc107',
        success: '#28a745', primary: '#0d6efd', info: '#17a2b8'
    };
    const icons = {
        emergency: { icon: '⚠️', size: 32, pulse: true },
        volunteer: { icon: '🤝', size: 28, pulse: isLive },
        ngo: { icon: '🏢', size: 30, pulse: false },
        citizen: { icon: '✅', size: 26, pulse: false }
    };
    const config = icons[type] || icons.emergency;
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="width:${config.size}px;height:${config.size}px;
                    background:${colors[color]||colors.danger};border-radius:50%;
                    display:flex;align-items:center;justify-content:center;
                    color:white;font-size:${config.size-8}px;
                    border:2px solid white;
                    box-shadow:0 2px 10px rgba(0,0,0,0.3);
                    ${config.pulse?'animation:pulse-marker 1.5s infinite;':''}
                    cursor:pointer;">
                    ${config.icon}${isLive?'<span class="live-dot"></span>':''}
                </div>`,
        iconSize: [config.size, config.size],
        popupAnchor: [0, -config.size/2]
    });
}

function initRealtimeMap() {
    const mapContainer = document.getElementById('realtimeMap');
    if (!mapContainer) return;
    realtimeMap = L.map('realtimeMap').setView([20.5937, 78.9629], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19
    }).addTo(realtimeMap);
    L.control.scale().addTo(realtimeMap);
    
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'map-legend');
        div.innerHTML = `
            <div class="legend-title">📍 Map Legend</div>
            <div><span class="legend-marker emergency"></span> Emergency</div>
            <div><span class="legend-marker volunteer"></span> Volunteer</div>
            <div><span class="legend-marker ngo"></span> NGO/Camp</div>
            <div><span class="legend-marker citizen"></span> Safe Citizen</div>
            <hr><div><span class="pulse-dot"></span> Live (Active now)</div>
        `;
        return div;
    };
    legend.addTo(realtimeMap);
    startRealTimeUpdates();
    trackMyLocation();
}

function clearAllMarkers() {
    Object.keys(mapLayers).forEach(key => {
        mapLayers[key].forEach(marker => {
            if (realtimeMap) realtimeMap.removeLayer(marker);
        });
        mapLayers[key] = [];
    });
}

async function fetchLiveLocations() {
    try {
        const response = await fetch(SITE_URL + 'api/get_live_locations.php?type=' + selectedFilter);
        const data = await response.json();
        if (!data.success) return;
        clearAllMarkers();
        
        data.data.forEach(item => {
            if (!item.latitude || !item.longitude) return;
            let marker = null, popupContent = '';
            
            if (item.marker_type === 'emergency') {
                marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], {
                    icon: getMarkerIcon('emergency', item.color)
                });
                popupContent = `
                    <div class="emergency-popup">
                        <h6><i class="fas fa-exclamation-triangle"></i> ${escapeHtml(item.title)}</h6>
                        <p><strong>Type:</strong> ${item.request_type}</p>
                        <p><strong>Priority:</strong> <span class="badge-${item.priority}">${item.priority}</span></p>
                        <p><strong>Status:</strong> ${item.status}</p>
                        <p><strong>Reporter:</strong> ${escapeHtml(item.reporter_name)}</p>
                        <a href="${SITE_URL}view_request.php?id=${item.id}" class="btn btn-sm btn-danger mt-2">View →</a>
                    </div>
                `;
                mapLayers.emergencies.push(marker);
            } else if (item.marker_type === 'volunteer') {
                const isLive = item.is_online && new Date(item.last_update) > new Date(Date.now() - 5*60000);
                marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], {
                    icon: getMarkerIcon('volunteer', item.color, isLive)
                });
                popupContent = `
                    <div class="volunteer-popup">
                        <h6><i class="fas fa-hands-helping"></i> ${escapeHtml(item.full_name)}</h6>
                        <p><strong>Status:</strong> <span class="badge bg-${item.availability==='available'?'success':'warning'}">${item.availability}</span></p>
                        <p><strong>Tasks:</strong> ${item.total_tasks_completed||0}</p>
                        <p><strong>Location:</strong> ${escapeHtml(item.location_name||'—')}</p>
                        ${isLive?'<p class="text-success"><i class="fas fa-circle"></i> Active Now</p>':''}
                    </div>
                `;
                mapLayers.volunteers.push(marker);
            } else if (item.marker_type === 'ngo') {
                marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], {
                    icon: getMarkerIcon('ngo', item.color)
                });
                popupContent = `
                    <div class="ngo-popup">
                        <h6><i class="fas fa-building"></i> ${escapeHtml(item.organization_name)}</h6>
                        ${item.camp_name?`<p><strong>Camp:</strong> ${escapeHtml(item.camp_name)} (${item.camp_type})</p>`:''}
                        <p><strong>Contact:</strong> ${escapeHtml(item.contact_person||'—')}</p>
                        ${item.verified?'<p class="text-success"><i class="fas fa-check-circle"></i> Verified NGO</p>':''}
                    </div>
                `;
                mapLayers.ngos.push(marker);
            } else if (item.marker_type === 'citizen') {
                marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], {
                    icon: getMarkerIcon('citizen', item.color)
                });
                popupContent = `
                    <div class="citizen-popup">
                        <h6><i class="fas fa-user-check"></i> ${escapeHtml(item.full_name)}</h6>
                        <p><strong>Status:</strong> <span class="text-success"><i class="fas fa-shield-alt"></i> Safe</span></p>
                        <p><strong>Location:</strong> ${escapeHtml(item.location_name||'—')}</p>
                        <p><strong>Checked in:</strong> ${new Date(item.checked_in_at).toLocaleString()}</p>
                        ${item.message?`<p><em>"${escapeHtml(item.message.substring(0,100))}"</em></p>`:''}
                    </div>
                `;
                mapLayers.citizens.push(marker);
            }
            if (marker) { marker.bindPopup(popupContent); marker.addTo(realtimeMap); }
        });
        
        updateStatsCounters(data.data);
    } catch (error) { console.error('Error:', error); }
}

function updateStatsCounters(data) {
    document.getElementById('emergencyCount').innerText = data.filter(d=>d.marker_type==='emergency').length;
    document.getElementById('volunteerCount').innerText = data.filter(d=>d.marker_type==='volunteer').length;
    document.getElementById('ngoCount').innerText = data.filter(d=>d.marker_type==='ngo').length;
    document.getElementById('citizenCount').innerText = data.filter(d=>d.marker_type==='citizen').length;
}

function trackMyLocation() {
    if (!navigator.geolocation) return;
    watchId = navigator.geolocation.watchPosition(
        async (position) => {
            const { latitude, longitude } = position.coords;
            if (userLocationMarker) { realtimeMap.removeLayer(userLocationMarker); }
            const userIcon = L.divIcon({
                className: 'user-location-marker',
                html: `<div style="width:20px;height:20px;background:#0d6efd;border-radius:50%;
                            border:2px solid white;box-shadow:0 0 0 2px #0d6efd;
                            animation:pulse-blue 1.5s infinite;"></div>`,
                iconSize: [20, 20]
            });
            userLocationMarker = L.marker([latitude, longitude], { icon: userIcon })
                .bindPopup('<strong>📍 You are here</strong>').addTo(realtimeMap);
            
            let locationName = '';
            try {
                const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                const geoData = await geoRes.json();
                locationName = geoData.display_name || '';
            } catch(e) {}
            
            await fetch(SITE_URL + 'api/update_location.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ latitude, longitude, location_name: locationName })
            });
            
            if (realtimeMap.getZoom() > 10) {
                realtimeMap.setView([latitude, longitude], 15);
            }
        },
        (error) => { console.error('Geolocation error:', error.message); },
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
    );
}

function startRealTimeUpdates() {
    fetchLiveLocations();
    refreshInterval = setInterval(fetchLiveLocations, 10000);
}

function filterMapByType(type) {
    selectedFilter = type;
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.filter === type) btn.classList.add('active');
    });
    fetchLiveLocations();
}

function stopRealTimeTracking() {
    if (refreshInterval) clearInterval(refreshInterval);
    if (watchId) navigator.geolocation.clearWatch(watchId);
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof SITE_URL !== 'undefined') {
        setTimeout(function() {
            if (document.getElementById('realtimeMap')) {
                initRealtimeMap();
            }
        }, 500);
    }
});

window.addEventListener('beforeunload', function() {
    if (typeof stopRealTimeTracking === 'function') stopRealTimeTracking();
});

// Auto location tracking background
<?php if(isset($_SESSION['user_id'])): ?>
setInterval(async function() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(async function(position) {
            try {
                await fetch('<?php echo SITE_URL; ?>api/update_location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                });
            } catch(e) {}
        }, function(error) {}, { enableHighAccuracy: true, timeout: 10000 });
    }
}, 30000);
<?php endif; ?>
</script>

<?php
$extra_scripts = '<script>const SITE_URL = "' . SITE_URL . '";</script>
<script src="' . SITE_URL . 'assets/js/dashboard.js?v=' . time() . '"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />';
require_once 'includes/app_footer.php';
?>