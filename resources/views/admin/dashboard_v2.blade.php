@extends("admin.layout.main")

@push("style")
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  /* Dashboard-specific styles only — global theme handled by custom.css */
  [x-cloak] { display: none !important; }
  #video-stream-modal { z-index: 1060; }
  .modal .dropdown-menu { z-index: 1061; }
  .cursor-pointer { cursor: pointer; }

  /* Category cards */
  .dashboard-card {
    border: 3px solid transparent !important;
    border-radius: 0.75rem;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.18s ease, opacity 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  }
  /* ACTIVE — lifted, full brightness, white ring */
  .dashboard-card.active {
    transform: translateY(-5px) scale(1.02);
    opacity: 1 !important;
    border-color: rgba(255,255,255,0.85) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.22) !important;
    z-index: 10;
  }
  /* INACTIVE — dimmed, pushed back, no ring */
  .dashboard-card:not(.active) {
    opacity: 0.55;
    transform: scale(0.97);
    filter: saturate(0.7);
  }
  .dashboard-card:not(.active):hover {
    opacity: 0.8;
    transform: scale(0.99);
    filter: saturate(1);
  }
  .card-pointer {
    position: absolute; bottom: -10px; left: 50%;
    transform: translateX(-50%) rotate(45deg);
    width: 20px; height: 20px; z-index: 1;
  }

  /* Dash header */
  .dash-header {
    background-color: var(--c-bg-surface);
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    box-shadow: var(--c-shadow-sm);
    margin-bottom: 1.25rem;
  }

  /* Filter pills */
  .filter-pill {
    background-color: var(--c-bg-surface-2);
    color: var(--c-text-primary);
    border: none;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
    border-radius: 50rem;
    box-shadow: var(--c-shadow-sm);
  }
  button.filter-pill:hover,
  a.filter-pill:hover { background-color: var(--c-bg-hover); color: var(--c-text-primary); text-decoration: none; }
  .filter-pill.active { background-color: var(--c-bg-active); box-shadow: var(--c-shadow-inset); }
  .filter-pill .badge { background-color: var(--c-bg-surface-3); color: var(--c-text-primary); }

  .filter-pill.highlighted-pill { background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; }
  .filter-pill.highlighted-pill .badge { background-color: #ef4444; color: white; }
  button.filter-pill.highlighted-pill:hover,
  a.filter-pill.highlighted-pill:hover { background-color: #fca5a5; color: #b91c1c; }
  .filter-pill.highlighted-pill.active { background-color: #ef4444; color: white; border-color: #ef4444; }
  .filter-pill.highlighted-pill.active .badge { background-color: white; color: #ef4444; }
  [data-theme="dark"] .filter-pill.highlighted-pill { background-color: rgba(239,68,68,0.15); }
  [data-theme="dark"] .filter-pill.highlighted-pill:hover { background-color: rgba(239,68,68,0.25); }

  /* Breakdown section */
  .breakdown-section {
    background-color: var(--c-bg-surface);
    border-radius: 0.75rem;
    padding: 1.5rem 1.5rem 0;
    box-shadow: var(--c-shadow-sm);
    overflow: hidden;
  }
  .breakdown-accent { width: 6px; height: 24px; border-radius: 3px; margin-right: 12px; flex-shrink: 0; }

  /* Custom table inside breakdown */
  .table-custom { margin-bottom: 0; }
  .table-custom th { font-size: 0.72rem; letter-spacing: 1px; padding: 0.85rem 0.75rem; }
  .table-custom td { padding: 0.75rem; font-size: 0.875rem; }
  .table-custom tbody tr:last-child td { border-bottom: none !important; }

  /* Note input */
  .note-input { cursor: pointer; max-width: 200px; }
  .note-input:focus, .note-input.selected {
    border-color: #198754 !important;
    box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.2) !important;
    outline: none;
  }

  /* Status badge */
  .status-badge-inline {
    font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
    width: 32px; height: 32px; display: flex; align-items: center;
    justify-content: center; border-radius: 4px; flex-shrink: 0;
  }

  /* Pagination footer */
  .table-footer-row {
    background-color: var(--c-bg-surface-2);
    box-shadow: 0 -1px 0 0 rgba(0,0,0,0.04);
    padding: 0.75rem 1rem;
    color: var(--c-text-secondary);
    font-size: 0.82rem;
  }
  [data-theme="dark"] .table-footer-row { box-shadow: 0 -1px 0 0 rgba(255,255,255,0.04); }

  /* Location sub-cards */
  .location-sub-card { transition: all 0.2s; }
  .location-sub-card:hover { transform: translateY(-1px); }

  /* ── Fullscreen mode ─────────────────────────────────────────── */
  /* Top navbar (.navbar-static), sidebar, footer, page-header */
  body.zsc-fullscreen .navbar,
  body.zsc-fullscreen .navbar-static,
  body.zsc-fullscreen .navbar-main,
  body.zsc-fullscreen .sidebar,
  body.zsc-fullscreen .navbar-footer,
  body.zsc-fullscreen .page-header { display: none !important; }

  body.zsc-fullscreen .page-content { padding: 0 !important; margin: 0 !important; }
  body.zsc-fullscreen .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
  body.zsc-fullscreen .content-inner { padding: 0 !important; }
  body.zsc-fullscreen .content { padding: 0 !important; }
  body.zsc-fullscreen .dashboard-wrap { padding: 1.25rem !important; min-height: 100vh; }

  /* ── Floating right dock (fullscreen / theme / notif / account) ── */
  #zsc-float-dock {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }
  .zsc-dock-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.55);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    cursor: pointer;
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.25);
    transition: background 0.15s, transform 0.15s;
    padding: 0;
    flex-shrink: 0;
  }
  .zsc-dock-btn:hover { background: rgba(0,0,0,0.75); transform: scale(1.08); }
  [data-theme="dark"] .zsc-dock-btn { background: rgba(255,255,255,0.15); }
  [data-theme="dark"] .zsc-dock-btn:hover { background: rgba(255,255,255,0.28); }

  /* keep Bootstrap dropdowns above everything */
  .zsc-dock-dropdown .dropdown-menu { z-index: 10100; }

  /* ── Floating left menu button ───────────────────────────────── */
  #zsc-float-menu-btn {
    position: fixed;
    bottom: 1.5rem;
    left: 1.5rem;
    z-index: 9999;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.55);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    cursor: pointer;
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.25);
    transition: background 0.15s, transform 0.15s;
  }
  #zsc-float-menu-btn:hover { background: rgba(0,0,0,0.75); transform: scale(1.08); }
  [data-theme="dark"] #zsc-float-menu-btn { background: rgba(255,255,255,0.15); }
  [data-theme="dark"] #zsc-float-menu-btn:hover { background: rgba(255,255,255,0.28); }

  /* ── Floating menu slide panel ───────────────────────────────── */
  #zsc-float-menu-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    top: 0;
    width: 240px;
    background: #1a2035;
    color: #fff;
    z-index: 10200;
    transform: translateX(-100%);
    transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 24px rgba(0,0,0,0.35);
    overflow: hidden;
  }
  #zsc-float-menu-panel.open { transform: translateX(0); }

  .zsc-fmp-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1rem 0.75rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    flex-shrink: 0;
  }
  #zsc-float-menu-close {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.7);
    font-size: 1rem;
    cursor: pointer;
    line-height: 1;
    padding: 2px;
    transition: color 0.15s;
  }
  #zsc-float-menu-close:hover { color: #fff; }

  .zsc-fmp-nav {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem 0;
  }
  .zsc-fmp-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 1rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.85rem;
    transition: background 0.15s, color 0.15s;
    border-radius: 0;
  }
  .zsc-fmp-link:hover { background: rgba(255,255,255,0.1); color: #fff; text-decoration: none; }
  .zsc-fmp-link i { font-size: 1rem; flex-shrink: 0; }

  /* Backdrop behind slide panel */
  #zsc-float-menu-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 10100;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.28s;
  }
  #zsc-float-menu-backdrop.open { opacity: 1; pointer-events: all; }

  /* ── Map overlay on Locations breakdown ──────────────────────── */
  #zsc-location-map {
    width: 100%;
    z-index: 1;
    background: #e8eef3;
  }
  /* Keep Leaflet popup above everything */
  .leaflet-popup { z-index: 800; }

  /* ── Map markers ─────────────────────────────────────────────── */
  @keyframes zsc-blink-critical {
    0%, 100% { box-shadow: 0 0 0 3px rgba(220,53,69,0.9),  0 0 10px 4px rgba(220,53,69,0.55); opacity: 1; }
    50%       { box-shadow: 0 0 0 6px rgba(220,53,69,0.15), 0 0 18px 8px rgba(220,53,69,0.1);  opacity: 0.7; }
  }
  @keyframes zsc-blink-warning {
    0%, 100% { box-shadow: 0 0 0 3px rgba(255,193,7,0.9),  0 0 10px 4px rgba(255,193,7,0.55); opacity: 1; }
    50%       { box-shadow: 0 0 0 6px rgba(255,193,7,0.15), 0 0 18px 8px rgba(255,193,7,0.1);  opacity: 0.7; }
  }
  /* Normal active (green, no alert) */
  .map-marker-active   { background: #198754; border: 2px solid #fff; border-radius: 50%; width: 14px; height: 14px;
                         box-shadow: 0 0 0 3px rgba(25,135,84,0.35); }
  /* Inactive location (red, static) */
  .map-marker-inactive { background: #6c757d; border: 2px solid #fff; border-radius: 50%; width: 14px; height: 14px; }
  /* Active critical alert — red blink */
  .map-marker-critical { background: #dc3545; border: 2px solid #fff; border-radius: 50%; width: 16px; height: 16px;
                         animation: zsc-blink-critical 0.9s ease-in-out infinite; }
  /* Active warning alert — orange blink */
  .map-marker-warning  { background: #fd7e14; border: 2px solid #fff; border-radius: 50%; width: 16px; height: 16px;
                         animation: zsc-blink-warning 1.2s ease-in-out infinite; }
</style>
@endpush

@section("content")
@php $setting = App\Models\Setting::first(); @endphp

<div x-data="dashboard" x-init="init()" x-cloak class="dashboard-wrap pb-4">

    {{-- ============================================================
         MODALS
    ============================================================ --}}
    <div class="modal fade" id="card-widget-note-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span x-text="'NOTE — LOG: ' + selectedDeviceStatus?.device_log?.id + ' | DEVICE: ' + selectedDeviceStatus?.device_id"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <template x-if="selectedDeviceStatus">
                        <textarea class="form-control mb-2" rows="5" style="resize: none;" x-model="selectedDeviceStatus.notes"></textarea>
                    </template>
                    <div class="d-flex gap-2 align-items-center mt-2">
                        <span class="text-muted small">State:</span>
                        <span class="btn btn-sm" :class="selectedDeviceStatus?.is_normal_state ? 'btn-success' : 'btn-danger'"
                              x-text="selectedDeviceStatus?.is_normal_state ? 'Normal' : 'Not Normal'"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" @click="stream(selectedDeviceStatus?.device_id)">
                        <i class="ph-video-camera me-2"></i>STREAM
                    </button>
                    <template x-for="action in selectedDeviceStatus?.device?.publish_action">
                        <button type="button" class="btn btn-success" @click="publishAction(action.id)">
                            <span x-text="action.label?.toUpperCase()"></span>
                        </button>
                    </template>
                    <button type="button" class="btn btn-link ms-auto" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" @click="submitNote" :disabled="!selectedDeviceStatus?.notes?.trim()">
                        <template x-if="!selectedDeviceStatus?.isSaving">
                            <span><i class="ph-check me-1"></i>Submit Note</span>
                        </template>
                        <template x-if="selectedDeviceStatus?.isSaving">
                            <span><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="video-stream-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Video Streaming</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div style="min-height: 500px; aspect-ratio: 16/9;"
                         :style="{ display: isStreamingLoading ? 'flex' : 'none' }"
                         class="w-100 flex-column gap-2 justify-content-center align-items-center">
                        <div class="spinner-border spinner-border-lg" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span>Please wait...</span>
                    </div>
                    <iframe style="width: 100%; min-height: 500px; aspect-ratio: 16/9; border: 0; display: block; background: #000;"
                            :style="{ display: isStreaming ? 'block' : 'none' }"
                            class="w-100"
                            :src="iFrameUrl">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    @if($setting->location_widget)
    <div class="modal fade" tabindex="-1" id="registeredLocationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">REGISTERED LOCATION</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body overflow-auto text-nowrap">
                    <input type="text" x-model="locationSearch.registered" @input="regPage=1" class="form-control mb-3" placeholder="Search by Location ID...">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Location ID</th><th>Last Ping</th><th>Active Hour</th><th>Inactive Hour</th><th>Status</th><th>Coordinates</th></tr></thead>
                        <tbody style="height: 250px;">
                            <template x-for="(value, key) in pagedRegistered" :key="key">
                                <tr>
                                    <td x-text="key"></td>
                                    <td x-text="value[0]['last_ping_at'] || 'No ping data'"></td>
                                    <td x-text="value[0]['active_hour'] || 'No active hour'"></td>
                                    <td x-text="value[0]['inactive_hour'] || 'No inactive hour'"></td>
                                    <td><span class="badge" :class="Object.keys(activeLocations).includes(key) ? 'bg-success' : 'bg-danger'" x-text="Object.keys(activeLocations).includes(key) ? 'Active' : 'Inactive'"></span></td>
                                    <td>
                                        <template x-if="value[0]['latlong']">
                                            <a :href="`https://www.google.com/maps/search/?api=1&query=${value[0]['latlong']}`" target="_blank" class="text-decoration-none d-flex align-items-center gap-1" style="font-size:0.82rem;">
                                                <i class="ph-map-pin"></i><span x-text="value[0]['latlong']"></span>
                                            </a>
                                        </template>
                                        <template x-if="!value[0]['latlong']"><span class="text-muted" style="font-size:0.82rem;">—</span></template>
                                    </td>
                                </tr>
                            </template>
                            {{-- Empty filler rows to always maintain 5-row height --}}
                            <template x-for="i in Math.max(0, 5 - Object.keys(pagedRegistered).length)" :key="'fill-r-'+i">
                                <tr style="visibility:hidden;"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
                            </template>
                        </tbody>
                    </table>
                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-2 px-1" x-show="regTotalPages > 1">
                        <small class="text-muted" x-text="`Page ${regPage} of ${regTotalPages}`"></small>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-light" :disabled="regPage <= 1" @click="regPage--">&laquo;</button>
                            <button class="btn btn-sm btn-light" :disabled="regPage >= regTotalPages" @click="regPage++">&raquo;</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="activeLocationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">ACTIVE LOCATION</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body overflow-auto text-nowrap">
                    <input type="text" x-model="locationSearch.active" @input="actPage=1" class="form-control mb-3" placeholder="Search by Location ID...">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Location ID</th><th>Last Ping</th><th>Active Hour</th><th>Inactive Hour</th><th>Coordinates</th><th>Action</th></tr></thead>
                        <tbody>
                            <template x-for="(value, key) in pagedActive" :key="key">
                                <tr>
                                    <td x-text="key"></td>
                                    <td x-text="value[0]['last_ping_at'] || 'No ping data'"></td>
                                    <td x-text="value[0]['active_hour'] || 'No active hour'"></td>
                                    <td x-text="value[0]['inactive_hour'] || 'No inactive hour'"></td>
                                    <td>
                                        <template x-if="value[0]['latlong']">
                                            <a :href="`https://www.google.com/maps/search/?api=1&query=${value[0]['latlong']}`" target="_blank" class="text-decoration-none d-flex align-items-center gap-1" style="font-size:0.82rem;">
                                                <i class="ph-map-pin"></i><span x-text="value[0]['latlong']"></span>
                                            </a>
                                        </template>
                                        <template x-if="!value[0]['latlong']"><span class="text-muted" style="font-size:0.82rem;">—</span></template>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" @click.prevent="stream(value[0]['id'])">Stream</a></li>
                                                <li><a class="dropdown-item" href="#" @click.prevent="getHour(value[0]['id'])">Get Active Period</a></li>
                                                <li><a class="dropdown-item" href="#" @click.prevent="setActiveHour(value[0]['id'])">Set Active Hours</a></li>
                                                <li><a class="dropdown-item" href="#" @click.prevent="setInctiveHour(value[0]['id'])">Set Inactive Hours</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="i in Math.max(0, 5 - Object.keys(pagedActive).length)" :key="'fill-a-'+i">
                                <tr style="visibility:hidden;"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-2 px-1" x-show="actTotalPages > 1">
                        <small class="text-muted" x-text="`Page ${actPage} of ${actTotalPages}`"></small>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-light" :disabled="actPage <= 1" @click="actPage--">&laquo;</button>
                            <button class="btn btn-sm btn-light" :disabled="actPage >= actTotalPages" @click="actPage++">&raquo;</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="inactiveLocationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">INACTIVE LOCATION</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body overflow-auto text-nowrap">
                    <input type="text" x-model="locationSearch.inactive" @input="inactPage=1" class="form-control mb-3" placeholder="Search by Location ID...">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Location ID</th><th>Last Ping</th><th>Active Hour</th><th>Inactive Hour</th><th>Coordinates</th><th>Last Camera</th></tr></thead>
                        <tbody>
                            <template x-for="(value, key) in pagedInactive" :key="key">
                                <tr>
                                    <td x-text="key"></td>
                                    <td x-text="value[0]['last_ping_at'] || 'No ping data'"></td>
                                    <td x-text="value[0]['active_hour'] || 'No active hour'"></td>
                                    <td x-text="value[0]['inactive_hour'] || 'No inactive hour'"></td>
                                    <td>
                                        <template x-if="value[0]['latlong']">
                                            <a :href="`https://www.google.com/maps/search/?api=1&query=${value[0]['latlong']}`" target="_blank" class="text-decoration-none d-flex align-items-center gap-1" style="font-size:0.82rem;">
                                                <i class="ph-map-pin"></i><span x-text="value[0]['latlong']"></span>
                                            </a>
                                        </template>
                                        <template x-if="!value[0]['latlong']"><span class="text-muted" style="font-size:0.82rem;">—</span></template>
                                    </td>
                                    <td>
                                        <template x-if="getLastCaptureForDevice(value[0].device_id)">
                                            <a :href="getLastCaptureForDevice(value[0].device_id)" target="_blank">View Capture</a>
                                        </template>
                                        <template x-if="!getLastCaptureForDevice(value[0].device_id)"><span class="text-muted">N/A</span></template>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="i in Math.max(0, 5 - Object.keys(pagedInactive).length)" :key="'fill-i-'+i">
                                <tr style="visibility:hidden;"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-2 px-1" x-show="inactTotalPages > 1">
                        <small class="text-muted" x-text="`Page ${inactPage} of ${inactTotalPages}`"></small>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-light" :disabled="inactPage <= 1" @click="inactPage--">&laquo;</button>
                            <button class="btn btn-sm btn-light" :disabled="inactPage >= inactTotalPages" @click="inactPage++">&raquo;</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================
         HEADER ROW — title + location filters
    ============================================================ --}}
    <div class="dash-header d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size: 1.2em;">{{ $setting->app_name ?? config('app.name') }}</h6>
            <span class="text-muted" style="font-size: 0.82rem;">Select a category to view detailed status</span>
        </div>
        <div class="d-flex flex-column flex-md-row gap-2" style="min-width: 50%;">
            <div class="flex-fill">
                <select class="form-control select" data-placeholder="All Locations" name="branches" id="branches" multiple="multiple">
                    <option></option>
                    @foreach ($device_locations as $device_location)
                    <option value="{{ $device_location->branch }}">{{ ucfirst($device_location->branch) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-fill">
                <select class="form-control select" data-placeholder="All Sub-Locations" name="buildings" id="buildings" multiple="multiple">
                    <option></option>
                    @foreach ($device_sub_locations as $device_sub_location)
                    <option value="{{ $device_sub_location->building }}">{{ ucfirst($device_sub_location->building) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-fill">
                <select class="form-control select" data-placeholder="All Location-ID" name="rooms" id="rooms" multiple="multiple">
                    <option></option>
                    @foreach ($device_location_ids as $device_location_id)
                    <option value="{{ $device_location_id->room }}">{{ ucfirst($device_location_id->room) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================
         CATEGORY CARDS
    ============================================================ --}}
    <div class="row g-3 mb-4">
        <template x-for="cat in categories" :key="cat.id">
            <div class="col-lg-3 col-md-6">
                <div class="card text-white dashboard-card position-relative h-100"
                     :class="[cat.color, activeCategory === cat.id ? 'active' : '']"
                     @click="activeCategory = cat.id; activeFilter = null; currentPage = 1;">
                    <div class="card-body d-flex flex-column justify-content-between" style="padding: 1.25rem 1.5rem;">
                        <div class="d-flex justify-content-between align-items-start">
                            <h1 class="display-4 mb-0 fw-light" x-text="cat.count"></h1>
                            <div class="text-end" style="opacity:0.85; margin-top:4px;">
                                <div class="fw-bold" style="font-size:1.1rem;" x-text="cat.countSecondary"></div>
                                <div style="font-size:0.65rem; letter-spacing:0.5px; text-transform:uppercase; opacity:0.8;" x-text="cat.countSecondaryLabel"></div>
                            </div>
                        </div>
                        <h6 class="text-uppercase mt-4 mb-0 fw-bold" style="letter-spacing: 0.5px;" x-text="cat.name"></h6>
                        <i :class="cat.icon" class="position-absolute opacity-25" style="bottom: 15px; right: 15px; font-size: 4.5rem;"></i>
                    </div>
                    <template x-if="activeCategory === cat.id">
                        <div :class="['card-pointer', cat.color]"></div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- ============================================================
         BREAKDOWN SECTION
    ============================================================ --}}
    <template x-if="activeCategory">
        <div class="breakdown-section mb-4">

            {{-- Section title --}}
            <div class="d-flex align-items-center mb-4">
                <div class="breakdown-accent" :style="{ backgroundColor: categories.find(c => c.id === activeCategory)?.accentColor }"></div>
                <h5 class="mb-0 text-uppercase fw-bold breakdown-label" style="font-size: 0.9rem; letter-spacing: 1px;">
                    Breakdown: <span x-text="categories.find(c => c.id === activeCategory)?.name"></span>
                </h5>
            </div>

            {{-- LOCATIONS breakdown --}}
            <template x-if="activeCategory === 'locations'">
                <div class="pb-4">
                    {{-- Two-column layout: Map (left) + Cards (right) --}}
                    <div class="row g-3">
                        {{-- Map column --}}
                        <div class="col-md-8">
                            <div class="position-relative" style="height:340px;">
                                {{-- Reset View button — overlaid top-left of map --}}
                                <button id="zsc-map-reset-btn"
                                        class="btn btn-sm btn-light shadow-sm"
                                        style="position:absolute;top:10px;left:10px;z-index:900;font-size:0.78rem;padding:4px 10px;border-radius:6px;"
                                        onclick="window._zscMapReset && window._zscMapReset()">
                                    <i class="ph-arrows-out me-1"></i> Reset View
                                </button>
                                <div id="zsc-location-map" style="height:100%;border-radius:0.65rem;"></div>
                            </div>
                            {{-- Legend below map --}}
                            <div class="d-flex flex-wrap gap-3 mt-2 px-1" style="font-size:0.78rem; color: var(--c-text-secondary);">
                                <span class="d-flex align-items-center gap-1"><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#198754;border:2px solid #fff;box-shadow:0 0 0 2px rgba(25,135,84,0.35);flex-shrink:0;"></span> Active</span>
                                <span class="d-flex align-items-center gap-1"><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#6c757d;border:2px solid #fff;flex-shrink:0;"></span> Inactive</span>
                                <span class="d-flex align-items-center gap-1"><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#fd7e14;border:2px solid #fff;flex-shrink:0;"></span> Warning alert</span>
                                <span class="d-flex align-items-center gap-1"><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#dc3545;border:2px solid #fff;flex-shrink:0;"></span> Critical alert</span>
                            </div>
                        </div>

                        {{-- Cards column — stacked vertically --}}
                        <div class="col-md-4 d-flex flex-column gap-3">
                            <div class="location-sub-card card cursor-pointer flex-fill" data-bs-toggle="modal" data-bs-target="#registeredLocationModal">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Register Location</h6>
                                        <span class="text-muted" style="font-size:0.82rem;" x-text="Object.keys(registeredLocations).length + ' Total'"></span>
                                    </div>
                                    <i class="ph-arrow-right text-success fs-5"></i>
                                </div>
                            </div>
                            <div class="location-sub-card card cursor-pointer flex-fill" data-bs-toggle="modal" data-bs-target="#activeLocationModal">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Active Location</h6>
                                        <span class="text-muted" style="font-size:0.82rem;" x-text="Object.keys(activeLocations).length + ' Total'"></span>
                                    </div>
                                    <i class="ph-arrow-right text-success fs-5"></i>
                                </div>
                            </div>
                            <div class="location-sub-card card cursor-pointer flex-fill" data-bs-toggle="modal" data-bs-target="#inactiveLocationModal">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Inactive Location</h6>
                                        <span class="text-muted" style="font-size:0.82rem;" x-text="Object.keys(inactiveLocations).length + ' Total'"></span>
                                    </div>
                                    <i class="ph-arrow-right text-muted fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            @if($setting->is_access_device)
            {{-- ACCESS CONTROL breakdown — same pills + table as other status categories --}}
            <template x-if="activeCategory === 'access_control'">
                <div>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <template x-for="w in activeCategoryData" :key="w.status_type.id">
                            <div class="d-flex align-items-stretch" style="border-radius: 50rem; overflow:hidden;">
                                <button class="filter-pill"
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0; margin-right: 1px; border-right: 1px solid rgba(0,0,0,0.1);"
                                        :class="{ 'active': activeFilter === w.status_type.name, 'highlighted-pill': w.status_type.device_status.length > 0 }"
                                        @click="activeFilter = activeFilter === w.status_type.name ? null : w.status_type.name; currentPage = 1;">
                                    <span x-text="w.status_type.name"></span>
                                    <span class="badge ms-2" x-text="w.status_type.device_status.length"></span>
                                </button>
                                <a :href="`/admin/status_types/${w.status_type.id}/history`" target="_blank"
                                   class="filter-pill d-flex align-items-center justify-content-center text-decoration-none"
                                   style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding-left: 0.75rem; padding-right: 0.75rem;"
                                   :class="{ 'highlighted-pill': w.status_type.device_status.length > 0 }"
                                   title="View History">
                                    <i class="ph-clock"></i>
                                </a>
                            </div>
                        </template>
                    </div>
                    {{-- Table + pagination identical to other status categories --}}
                    <div class="table-responsive">
                        <table class="table table-custom text-nowrap">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="ps-4">Actions</th>
                                    <th>Status Type</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Lat / Long</th>
                                    <th>Cams</th>
                                    <th class="pe-4" style="min-width: 300px;">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in paginatedTableData" :key="item.id">
                                    <tr>
                                        <td class="ps-4">
                                            <button class="btn btn-info btn-sm rounded-circle p-2 text-white" @click="selectDeviceStatus(item, true)">
                                                <i class="ph-eye"></i>
                                            </button>
                                        </td>
                                        <td class="fw-bold text-dark" x-text="item.status_type_name"></td>
                                        <td class="text-muted" x-text="moment(item.created_at).format('YYYY-MM-DD HH:mm:ss')"></td>
                                        <td class="text-muted" style="font-size: 0.82rem;">
                                            <span x-text="[item.device?.branch, item.device?.building, item.device?.room].filter(Boolean).join(' / ').toUpperCase()"></span>
                                        </td>
                                        <td>
                                            <template x-if="item.device_log?.cam_payloads?.length">
                                                <ul class="list-unstyled mb-0 text-muted" style="font-size: 0.82rem;">
                                                    <template x-for="cam in item.device_log.cam_payloads" :key="cam.id">
                                                        <li>
                                                            <template x-if="resolveLatlong(item, cam.latlong)">
                                                                <a target="_blank" :href="`https://www.google.com/maps/search/?api=1&query=${resolveLatlong(item, cam.latlong)}`" class="text-decoration-none d-flex align-items-center"><i class="ph-map-pin me-1"></i><span x-text="resolveLatlong(item, cam.latlong)"></span></a>
                                                            </template>
                                                            <template x-if="!resolveLatlong(item, cam.latlong)"><span>No Coordinate</span></template>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </template>
                                            <template x-if="!item.device_log?.cam_payloads?.length">
                                                <span style="font-size:0.82rem;">
                                                    <template x-if="resolveLatlong(item, null)">
                                                        <a target="_blank" :href="`https://www.google.com/maps/search/?api=1&query=${resolveLatlong(item, null)}`" class="text-decoration-none d-flex align-items-center text-muted"><i class="ph-map-pin me-1"></i><span x-text="resolveLatlong(item, null)"></span><span class="ms-1 text-secondary" style="font-size:0.7rem;">(loc)</span></a>
                                                    </template>
                                                    <template x-if="!resolveLatlong(item, null)"><span class="text-muted">No Coordinate</span></template>
                                                </span>
                                            </template>
                                        </td>
                                        <td>
                                            <template x-for="cam in item.device_log?.cam_payloads">
                                                <a target="_blank" :href="`/storage/${cam.file}`" class="d-flex align-items-center text-primary text-decoration-none mb-1" style="font-size: 0.82rem;">
                                                    <i class="ph-image me-1"></i><span x-text="cam.file_name || 'View Image'"></span>
                                                </a>
                                            </template>
                                            <template x-if="!item.device_log?.cam_payloads?.length">
                                                <span class="text-muted" style="font-size: 0.82rem;">No Image</span>
                                            </template>
                                        </td>
                                        <td class="pe-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form-control form-control-sm note-input" x-model="item.notes" placeholder="Type a note..." :disabled="item.isSaving" @focus="selectDeviceStatus(item, false)" :class="{ 'selected': selectedStatusId === item.id }">
                                                <span class="status-badge-inline text-white" :class="item.is_normal_state ? 'bg-success' : 'bg-danger'" x-text="item.is_normal_state ? 'OK' : 'NOK'"></span>
                                                <button class="btn btn-sm btn-success" style="width:32px;height:32px;padding:0;" @click="submitInlineNote(item)" :disabled="!item.notes?.trim() || item.isSaving">
                                                    <template x-if="!item.isSaving"><i class="ph-check"></i></template>
                                                    <template x-if="item.isSaving"><span class="spinner-border spinner-border-sm"></span></template>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredTableData.length === 0">
                                    <tr><td colspan="7" class="text-center py-5 text-muted"><i class="ph-folder-open mb-2" style="font-size:2rem;opacity:0.5;"></i><p class="mb-0">No records found.</p></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-4 mt-2" x-show="totalPages > 1">
                        <div class="text-muted" style="font-size:0.85rem;">Showing <span x-text="(currentPage-1)*pageSize+1"></span> to <span x-text="Math.min(currentPage*pageSize, filteredTableData.length)"></span> of <span x-text="filteredTableData.length"></span> entries</div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm border" :disabled="currentPage===1" @click="currentPage--">Prev</button>
                            <template x-for="page in visiblePages" :key="page"><button class="btn btn-sm border" :class="currentPage===page?'btn-primary':''" @click="currentPage=page" x-text="page"></button></template>
                            <button class="btn btn-sm border" :disabled="currentPage===totalPages||totalPages===0" @click="currentPage++">Next</button>
                        </div>
                    </div>
                </div>
            </template>
            @endif

            {{-- STATUS TYPES breakdown (critical / warning / info) --}}
            <template x-if="activeCategory !== 'locations' && activeCategory !== 'access_control'">
                <div>
                    {{-- Filter pills --}}
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <template x-for="w in activeCategoryData" :key="w.status_type.id">
                            <div class="d-flex align-items-stretch" style="border-radius: 50rem; overflow:hidden;">
                                <button class="filter-pill"
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0; margin-right: 1px; border-right: 1px solid rgba(0,0,0,0.1);"
                                        :class="{ 'active': activeFilter === w.status_type.name, 'highlighted-pill': w.status_type.device_status.length > 0 }"
                                        @click="activeFilter = activeFilter === w.status_type.name ? null : w.status_type.name; currentPage = 1;">
                                    <span x-text="w.status_type.name"></span>
                                    <span class="badge ms-2" x-text="w.status_type.device_status.length"></span>
                                </button>
                                <a :href="`/admin/status_types/${w.status_type.id}/history`" target="_blank"
                                   class="filter-pill d-flex align-items-center justify-content-center text-decoration-none"
                                   style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding-left: 0.75rem; padding-right: 0.75rem;"
                                   :class="{ 'highlighted-pill': w.status_type.device_status.length > 0 }"
                                   title="View History">
                                    <i class="ph-clock"></i>
                                </a>
                            </div>
                        </template>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-custom text-nowrap">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="ps-3">Actions</th>
                                    <th>Status Type</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Lat / Long</th>
                                    <th>Cams</th>
                                    <th class="pe-3" style="min-width: 300px;">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in paginatedTableData" :key="item.id">
                                    <tr>
                                        <td class="ps-4">
                                            <button class="btn btn-success btn-sm rounded-circle p-2" @click="selectDeviceStatus(item, true)">
                                                <i class="ph-eye"></i>
                                            </button>
                                        </td>
                                        <td class="fw-bold text-dark" x-text="item.status_type_name"></td>
                                        <td class="text-muted" x-text="moment(item.created_at).format('YYYY-MM-DD HH:mm:ss')"></td>
                                        <td class="text-muted" style="font-size: 0.82rem;">
                                            <span x-text="[item.device?.branch, item.device?.building, item.device?.room].filter(Boolean).join(' / ').toUpperCase()"></span>
                                        </td>
                                        <td>
                                            <template x-if="item.device_log?.cam_payloads?.length">
                                                <ul class="list-unstyled mb-0 text-muted" style="font-size: 0.82rem;">
                                                    <template x-for="cam in item.device_log.cam_payloads" :key="cam.id">
                                                        <li>
                                                            <template x-if="resolveLatlong(item, cam.latlong)">
                                                                <a target="_blank" :href="`https://www.google.com/maps/search/?api=1&query=${resolveLatlong(item, cam.latlong)}`"
                                                                   class="text-decoration-none d-flex align-items-center">
                                                                    <i class="ph-map-pin me-1"></i><span x-text="resolveLatlong(item, cam.latlong)"></span>
                                                                </a>
                                                            </template>
                                                            <template x-if="!resolveLatlong(item, cam.latlong)"><span>No Coordinate</span></template>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </template>
                                            <template x-if="!item.device_log?.cam_payloads?.length">
                                                <span style="font-size:0.82rem;">
                                                    <template x-if="resolveLatlong(item, null)">
                                                        <a target="_blank" :href="`https://www.google.com/maps/search/?api=1&query=${resolveLatlong(item, null)}`"
                                                           class="text-decoration-none d-flex align-items-center text-muted">
                                                            <i class="ph-map-pin me-1"></i><span x-text="resolveLatlong(item, null)"></span><span class="ms-1 text-secondary" style="font-size:0.7rem;">(loc)</span>
                                                        </a>
                                                    </template>
                                                    <template x-if="!resolveLatlong(item, null)"><span class="text-muted">No Coordinate</span></template>
                                                </span>
                                            </template>
                                        </td>
                                        <td>
                                            <template x-for="cam in item.device_log?.cam_payloads">
                                                <a target="_blank" :href="`/storage/${cam.file}`"
                                                   class="d-flex align-items-center text-primary text-decoration-none mb-1"
                                                   style="font-size: 0.82rem;">
                                                    <i class="ph-image me-1"></i>
                                                    <span x-text="cam.file_name || 'View Image'"></span>
                                                </a>
                                            </template>
                                            <template x-if="!item.device_log?.cam_payloads?.length">
                                                <span class="text-muted" style="font-size: 0.82rem;">No Image</span>
                                            </template>
                                        </td>
                                        <td class="pe-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text"
                                                       class="form-control form-control-sm note-input"
                                                       x-model="item.notes"
                                                       placeholder="Type a note..."
                                                       :disabled="item.isSaving"
                                                       @focus="selectDeviceStatus(item, false)"
                                                       :class="{ 'selected': selectedStatusId === item.id }">
                                                <span class="status-badge-inline text-white"
                                                      :class="item.is_normal_state ? 'bg-success' : 'bg-danger'"
                                                      x-text="item.is_normal_state ? 'OK' : 'NOK'"></span>
                                                <button class="btn btn-sm btn-success"
                                                        style="width: 32px; height: 32px; padding: 0;"
                                                        @click="submitInlineNote(item)"
                                                        :disabled="!item.notes?.trim() || item.isSaving">
                                                    <template x-if="!item.isSaving"><i class="ph-check"></i></template>
                                                    <template x-if="item.isSaving">
                                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                                    </template>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredTableData.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="ph-folder-open mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mb-0 text-muted">No records found for this view.</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="table-footer-row d-flex justify-content-between align-items-center" x-show="totalPages > 1">
                        <div>
                            Showing <span x-text="(currentPage - 1) * pageSize + 1"></span> to
                            <span x-text="Math.min(currentPage * pageSize, filteredTableData.length)"></span> of
                            <span x-text="filteredTableData.length"></span> entries
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm border" :disabled="currentPage === 1" @click="currentPage--">Prev</button>
                            <template x-for="page in visiblePages" :key="page">
                                <button class="btn btn-sm border" :class="currentPage === page ? 'btn-primary' : ''" @click="currentPage = page" x-text="page"></button>
                            </template>
                            <button class="btn btn-sm border" :disabled="currentPage === totalPages || totalPages === 0" @click="currentPage++">Next</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ═══════════════════════════════════════════════════════════
         FLOATING RIGHT DOCK — fullscreen / theme / notif / account
    ═══════════════════════════════════════════════════════════ --}}
    <div id="zsc-float-dock">

        {{-- Fullscreen toggle --}}
        <button id="zsc-fullscreen-btn" class="zsc-dock-btn" title="Toggle fullscreen">
            <i class="ph-arrows-out" id="zsc-fs-icon"></i>
        </button>

        {{-- Theme toggle --}}
        <button id="zsc-float-theme" class="zsc-dock-btn" title="Toggle theme">
            <i class="ph-sun  zsc-icon-sun"></i>
            <i class="ph-moon zsc-icon-moon" style="display:none;"></i>
        </button>

        {{-- Notifications --}}
        <div class="dropdown zsc-dock-dropdown">
            <button class="zsc-dock-btn position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="ph-bell"></i>
                <span class="notification-count badge bg-yellow text-black position-absolute"
                      style="top:4px;right:4px;font-size:0.55rem;padding:2px 4px;line-height:1;border-radius:50rem;min-width:16px;">
                    {{ \App\Models\Notif::where('notif_status','unread')->count() }}
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:260px;max-height:320px;overflow-y:auto;">
                @forelse(\App\Models\Notif::where('notif_status','unread')->latest()->take(10)->get() as $n)
                <li>
                    <a class="dropdown-item small py-2" href="#">
                        <div class="fw-semibold">{{ $n->message }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $n->created_at?->diffForHumans() }}</div>
                    </a>
                </li>
                @empty
                <li><span class="dropdown-item text-muted small">No unread notifications</span></li>
                @endforelse
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item small text-center" href="#">View all</a></li>
            </ul>
        </div>

        {{-- Account --}}
        <div class="dropdown zsc-dock-dropdown">
            <button class="zsc-dock-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Account">
                <i class="ph-user-circle" style="font-size:1.25rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
                <li><a class="dropdown-item" href="/admin/change-password"><i class="ph-wrench me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="/admin/logout"><i class="ph-sign-out me-2"></i>Sign Out</a></li>
            </ul>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════
         FLOATING MAIN MENU (left side)
    ═══════════════════════════════════════════════════════════ --}}
    <button id="zsc-float-menu-btn" title="Main menu">
        <i class="ph-list" id="zsc-float-menu-icon"></i>
    </button>

    <div id="zsc-float-menu-panel">
        <div class="zsc-fmp-header">
            <span class="fw-bold" style="font-size:0.85rem;">{{ $setting->app_name ?? config('app.name') }}</span>
            <button id="zsc-float-menu-close"><i class="ph-x"></i></button>
        </div>
        <nav class="zsc-fmp-nav">
            <a href="/admin/dashboard"        class="zsc-fmp-link"><i class="ph-house"></i> Dashboard</a>
            <a href="{{ route('admin.status_types.index') }}" class="zsc-fmp-link"><i class="ph-activity"></i> Status Types</a>
            @can('locations-read')
            <a href="{{ route('admin.locations.index') }}"   class="zsc-fmp-link"><i class="ph-map-pin"></i> Locations</a>
            @endcan
            @can('device-types-read')
            <a href="{{ route('admin.device_types.index') }}" class="zsc-fmp-link"><i class="ph-bookmarks-simple"></i> Device Types</a>
            @endcan
            @can('devices-read')
            <a href="{{ route('admin.devices.index') }}"     class="zsc-fmp-link"><i class="ph-atom"></i> Devices</a>
            @endcan
            @can('device-logs-read')
            <a href="{{ route('admin.device_logs.index') }}" class="zsc-fmp-link"><i class="ph-notebook"></i> Device Logs</a>
            @endcan
            @can('device-statuses-read')
            <a href="{{ route('admin.device_statuses.index') }}" class="zsc-fmp-link"><i class="ph-list-checks"></i> Device Statuses</a>
            @endcan
            @can('users-read')
            <a href="{{ route('admin.users.index') }}"       class="zsc-fmp-link"><i class="ph-users"></i> Users</a>
            @endcan
            @can('roles-read')
            <a href="{{ route('admin.roles.index') }}"       class="zsc-fmp-link"><i class="ph-user-gear"></i> Roles</a>
            @endcan
            @can('settings-read')
            <a href="{{ route('admin.settings.index') }}"    class="zsc-fmp-link"><i class="ph-gear"></i> Settings</a>
            @endcan
            <hr style="margin:0.5rem 0.75rem; border-color: rgba(255,255,255,0.15);">
            <a href="/admin/logout" class="zsc-fmp-link" style="color:rgba(255,100,100,0.9);"><i class="ph-sign-out"></i> Sign Out</a>
        </nav>
    </div>
    <div id="zsc-float-menu-backdrop"></div>

</div>
@endsection

@push('js')
<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        init() {
            this.getCsrfToken();
            this.socketListener();
            this.setupModalListeners();
            this.triggerFetch();
            this.getRegisteredLocation();
            setInterval(() => this.getRegisteredLocation(), 1000 * 60 * 1);

            $('#branches, #buildings, #rooms').select2({ width: '100%' });
            $('#branches').on('change', () => { this.branches = $('#branches').val() || []; this.triggerFetch(); });
            $('#buildings').on('change', () => { this.buildings = $('#buildings').val() || []; this.triggerFetch(); });
            $('#rooms').on('change', () => { this.rooms = $('#rooms').val() || []; this.triggerFetch(); });
        },

        // STATE
        csrfToken: '',
        originalData: null,
        activeCategory: 'warning',
        activeFilter: null,
        currentPage: 1,
        pageSize: 5,
        selectedDeviceStatus: null,
        selectedStatusId: null,
        isModalOpen: false,
        pendingUpdate: false,
        branches: [], buildings: [], rooms: [],
        isStreaming: false, isStreamingLoading: false, iFrameUrl: '',
        streamingDeviceId: null,
        registeredLocations: {}, activeLocations: {}, inactiveLocations: {},
        locationSearch: { registered: '', active: '', inactive: '' },
        regPage: 1, actPage: 1, inactPage: 1,
        modalPageSize: 5,

        // COMPUTED
        get categories() {
            return [
                @if($setting->location_widget)
                { id: 'locations',      name: 'LOCATIONS',        count: Object.keys(this.activeLocations).length,        countSecondary: Object.keys(this.inactiveLocations).length, countSecondaryLabel: 'Inactive', color: 'bg-success', accentColor: '#198754', icon: 'ph-map-pin' },
                @endif
                @if($setting->is_access_device)
                { id: 'access_control', name: 'ACCESS CONTROL',   count: this.getCategoryRoomCount('access_control'),     countSecondary: this.getCategoryDeviceCount('access_control'), countSecondaryLabel: 'Devices', color: 'bg-info',    accentColor: '#0dcaf0', icon: 'ph-shield-check' },
                @endif
                { id: 'critical',       name: 'CRITICAL ALERTS',  count: this.getCategoryRoomCount('critical'),           countSecondary: this.getCategoryDeviceCount('critical'),  countSecondaryLabel: 'Devices', color: 'bg-danger',  accentColor: '#dc3545', icon: 'ph-warning-octagon' },
                { id: 'warning',        name: 'WARNINGS',          count: this.getCategoryRoomCount('warning'),            countSecondary: this.getCategoryDeviceCount('warning'),   countSecondaryLabel: 'Devices', color: 'bg-warning', accentColor: '#ffc107', icon: 'ph-warning' },
                { id: 'info',           name: 'INFORMATION',       count: this.getCategoryRoomCount('info'),               countSecondary: this.getCategoryDeviceCount('info'),      countSecondaryLabel: 'Devices', color: 'bg-info',    accentColor: '#0dcaf0', icon: 'ph-info' },
            ];
        },
        get activeCategoryData() {
            if (!this.originalData?.status_type_widgets) return [];
            return this.originalData.status_type_widgets
                .filter(w => w.status_type.category === this.activeCategory)
                .map(w => ({
                    ...w,
                    status_type: {
                        ...w.status_type,
                        device_status: w.status_type.device_status.filter(
                            item => !item.marked_as_read && item.notes !== 'Normal State'
                        )
                    }
                }));
        },
        get filteredTableData() {
            let items = [];
            this.activeCategoryData.forEach(w => {
                if (!this.activeFilter || this.activeFilter === w.status_type.name) {
                    w.status_type.device_status.forEach(ds => {
                        if (ds.isSaving === undefined) ds.isSaving = false;
                        items.push({ ...ds, status_type_name: w.status_type.name });
                    });
                }
            });
            return items.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        },
        get paginatedTableData() {
            return this.filteredTableData.slice((this.currentPage - 1) * this.pageSize, this.currentPage * this.pageSize);
        },
        get totalPages() {
            return Math.ceil(this.filteredTableData.length / this.pageSize);
        },
        get visiblePages() {
            let pages = [], start = Math.max(1, this.currentPage - 2), end = Math.min(this.totalPages, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },
        get filteredRegisteredLocations() {
            if (!this.locationSearch.registered) return this.registeredLocations;
            return Object.fromEntries(Object.entries(this.registeredLocations).filter(([k]) => k.toLowerCase().includes(this.locationSearch.registered.toLowerCase())));
        },
        get filteredActiveLocations() {
            if (!this.locationSearch.active) return this.activeLocations;
            return Object.fromEntries(Object.entries(this.activeLocations).filter(([k]) => k.toLowerCase().includes(this.locationSearch.active.toLowerCase())));
        },
        get filteredInactiveLocations() {
            if (!this.locationSearch.inactive) return this.inactiveLocations;
            return Object.fromEntries(Object.entries(this.inactiveLocations).filter(([k]) => k.toLowerCase().includes(this.locationSearch.inactive.toLowerCase())));
        },
        // ── Modal table pagination ────────────────────────────────────────
        get pagedRegistered() {
            const entries = Object.entries(this.filteredRegisteredLocations);
            const start = (this.regPage - 1) * this.modalPageSize;
            return Object.fromEntries(entries.slice(start, start + this.modalPageSize));
        },
        get regTotalPages() {
            return Math.max(1, Math.ceil(Object.keys(this.filteredRegisteredLocations).length / this.modalPageSize));
        },
        get pagedActive() {
            const entries = Object.entries(this.filteredActiveLocations);
            const start = (this.actPage - 1) * this.modalPageSize;
            return Object.fromEntries(entries.slice(start, start + this.modalPageSize));
        },
        get actTotalPages() {
            return Math.max(1, Math.ceil(Object.keys(this.filteredActiveLocations).length / this.modalPageSize));
        },
        get pagedInactive() {
            const entries = Object.entries(this.filteredInactiveLocations);
            const start = (this.inactPage - 1) * this.modalPageSize;
            return Object.fromEntries(entries.slice(start, start + this.modalPageSize));
        },
        get inactTotalPages() {
            return Math.max(1, Math.ceil(Object.keys(this.filteredInactiveLocations).length / this.modalPageSize));
        },

        // UTILITY
        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = meta ? meta.content : '{{ csrf_token() }}';
        },
        async apiPost(url, data) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                body: JSON.stringify(data)
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        },
        getCategoryCount(cat) {
            if (!this.originalData?.status_type_widgets) return 0;
            return this.originalData.status_type_widgets
                .filter(w => w.status_type.category === cat)
                .reduce((sum, w) => sum + w.status_type.device_status.filter(
                    item => !item.marked_as_read && item.notes !== 'Normal State'
                ).length, 0);
        },
        getCategoryRoomCount(cat) {
            if (!this.originalData?.status_type_widgets) return 0;
            const rooms = new Set();
            this.originalData.status_type_widgets
                .filter(w => w.status_type.category === cat)
                .forEach(w => w.status_type.device_status
                    .filter(item => !item.marked_as_read && item.notes !== 'Normal State')
                    .forEach(item => { if (item.device?.room) rooms.add(item.device.room); })
                );
            return rooms.size;
        },
        getCategoryDeviceCount(cat) {
            if (!this.originalData?.status_type_widgets) return 0;
            const devices = new Set();
            this.originalData.status_type_widgets
                .filter(w => w.status_type.category === cat)
                .forEach(w => w.status_type.device_status
                    .filter(item => !item.marked_as_read && item.notes !== 'Normal State')
                    .forEach(item => { if (item.device?.device_id) devices.add(item.device.device_id); })
                );
            return devices.size;
        },
        // Returns the best available lat/long for a cam payload row.
        // Fallback order: cam.latlong (if valid) → locations.coordinate by room.
        resolveLatlong(item, camLatlong) {
            // Treat as invalid if: null/empty, unparseable, or both lat & lng are ≤ 1
            // (catches device defaults like "0,0" and "0,1" which mean no GPS fix)
            const isInvalid = (ll) => {
                if (!ll || ll.trim() === '') return true;
                const parts = ll.split(',').map(s => parseFloat(s.trim()));
                if (parts.length < 2 || parts.some(isNaN)) return true;
                return Math.abs(parts[0]) <= 1 && Math.abs(parts[1]) <= 1;
            };
            if (!isInvalid(camLatlong)) return camLatlong;
            // Fall back to the static Location coordinate for this room
            const room = item.device?.room;
            if (room && this.registeredLocations[room]?.[0]?.latlong) {
                return this.registeredLocations[room][0].latlong;
            }
            return null;
        },
        getLastCaptureForDevice(deviceId) {
            if (!this.originalData?.status_type_widgets) return null;
            for (const widget of this.originalData.status_type_widgets) {
                for (const status of widget.status_type.device_status) {
                    if (status.device_id === deviceId && status.device_log?.cam_payloads?.length > 0) {
                        const p = status.device_log.cam_payloads;
                        return `/storage/${p[p.length - 1].file}`;
                    }
                }
            }
            return null;
        },

        // NOTE
        selectDeviceStatus(item, openModal = false) {
            this.selectedStatusId = item.id;
            this.selectedDeviceStatus = { ...item, isSaving: false };
            if (openModal) bootstrap.Modal.getOrCreateInstance(document.getElementById('card-widget-note-modal')).show();
        },
        async submitInlineNote(item) {
            if (!item?.notes?.trim()) return;
            item.isSaving = true;
            try {
                await this.apiPost('/admin/device_status/notes', { device_status_id: item.id, notes: item.notes });
                this.selectedStatusId = null;
                await this.triggerFetch();
            } catch { alert('Failed to save note.'); }
            finally { item.isSaving = false; }
        },
        async submitNote() {
            if (!this.selectedDeviceStatus?.notes?.trim()) return;
            this.selectedDeviceStatus.isSaving = true;
            try {
                await this.apiPost('/admin/device_status/notes', { device_status_id: this.selectedDeviceStatus.id, notes: this.selectedDeviceStatus.notes });
                bootstrap.Modal.getInstance(document.getElementById('card-widget-note-modal'))?.hide();
                this.selectedStatusId = null;
                await this.triggerFetch();
            } catch { alert('Failed to save note.'); }
            finally { this.selectedDeviceStatus.isSaving = false; }
        },

        // DATA
        async triggerFetch() {
            let params = new URLSearchParams();
            (this.branches || []).forEach(b => params.append('branches[]', b));
            (this.buildings || []).forEach(b => params.append('buildings[]', b));
            (this.rooms || []).forEach(r => params.append('rooms[]', r));
            try {
                const res = await fetch('{{ route("dashboard.ajax") }}?' + params.toString(), { headers: { 'Accept': 'application/json' } });
                this.originalData = await res.json();
            } catch(e) { console.error('Fetch error', e); }
        },
        async getRegisteredLocation() {
            try {
                const res = await this.apiPost('/admin/devices/get-registered-locations', {});
                this.registeredLocations = res.data.registeredLocations || {};
                this.activeLocations     = res.data.activeLocations || {};
                this.inactiveLocations   = res.data.inactiveLocations || {};
            } catch(e) { console.error('Location error', e); }
        },

        // ACTIONS
        async publishAction(actionId) {
            if (!this.selectedDeviceStatus) return;
            try {
                await this.apiPost('/admin/devices/publish', {
                    id: actionId,
                    device_status_id: this.selectedDeviceStatus.id,
                    log_id: this.selectedDeviceStatus.device_log.id,
                    notes: this.selectedDeviceStatus.notes
                });
                bootstrap.Modal.getInstance(document.getElementById('card-widget-note-modal'))?.hide();
                await this.triggerFetch();
            } catch { alert('Action failed.'); }
        },
        async stream(deviceId) {
            this.streamingDeviceId = deviceId;
            this.isStreamingLoading = true;
            this.isStreaming = false;
            this.iFrameUrl = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('video-stream-modal')).show();
            try { await this.apiPost('/admin/devices/publish-streaming', { device_id: deviceId }); }
            catch { this.isStreamingLoading = false; }
        },
        async getHour(deviceId) { await this.apiPost('/admin/devices/get-hour', { device_id: deviceId }); alert('Request sent!'); },
        async setActiveHour(deviceId) {
            const time = prompt('Time (HH:mm):');
            if (time) { await this.apiPost('/admin/devices/set-active-hour', { device_id: deviceId, time }); alert('Request sent!'); }
        },
        async setInctiveHour(deviceId) {
            const time = prompt('Time (HH:mm):');
            if (time) { await this.apiPost('/admin/devices/set-inactive-hour', { device_id: deviceId, time }); alert('Request sent!'); }
        },

        // EVENTS
        setupModalListeners() {
            document.querySelectorAll('.modal').forEach(modalEl => {
                modalEl.addEventListener('show.bs.modal', () => { this.isModalOpen = true; });
                modalEl.addEventListener('hidden.bs.modal', () => {
                    this.isModalOpen = false;
                    if (this.pendingUpdate) { this.triggerFetch(); this.pendingUpdate = false; }
                    if (modalEl.id === 'video-stream-modal' && this.streamingDeviceId) {
                        this.apiPost('/admin/devices/publish-streaming-stop', { device_id: this.streamingDeviceId }).catch(() => {});
                    }
                    this.isStreaming = this.isStreamingLoading = false;
                    this.iFrameUrl = '';
                    this.streamingDeviceId = null;
                });
            });
        },
        socketListener() {
            if (window.Echo) {
                window.Echo.channel('laravel_database_newDataChannel')
                    .listen('.newDataEvent', (e) => {
                        if (e?.message?.type === 'stream_listener') {
                            this.iFrameUrl = 'https://' + e?.message?.plain_payload;
                            this.isStreaming = true;
                            this.isStreamingLoading = false;
                        } else {
                            if (this.isModalOpen) this.pendingUpdate = true; else this.triggerFetch();
                            if (e?.message?.data?.length > 0 && e?.message?.data?.[0]?.notes !== 'Normal State') {
                                new Audio('/mcc-notification.wav').play().catch(() => {});
                            }
                        }
                    })
                    .listen('.camDataEvent', () => {
                        if (this.isModalOpen) this.pendingUpdate = true; else this.triggerFetch();
                    });
            }
        }
    }));
});
</script>

{{-- Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
/* ── Floating right dock — fullscreen + theme sync ─────────────── */
(function () {
    const btn  = document.getElementById('zsc-fullscreen-btn');
    const icon = document.getElementById('zsc-fs-icon');
    let on = false;

    btn.addEventListener('click', () => {
        on = !on;
        document.body.classList.toggle('zsc-fullscreen', on);
        icon.className = on ? 'ph-arrows-in' : 'ph-arrows-out';
        btn.title = on ? 'Exit fullscreen' : 'Toggle fullscreen';
        if (window._zscMap) window._zscMap.invalidateSize();
    });

    // Sync the dock theme button with the existing navbar theme toggle
    const floatThemeBtn = document.getElementById('zsc-float-theme');
    if (floatThemeBtn) {
        floatThemeBtn.addEventListener('click', () => {
            // Delegate to the existing navbar theme button if present
            const navThemeBtn = document.querySelector('.navbar button[onclick], .navbar [data-theme-toggle]');
            if (navThemeBtn) { navThemeBtn.click(); return; }
            // Fallback: toggle data-theme on <html>
            const html = document.documentElement;
            const isDark = html.getAttribute('data-theme') === 'dark';
            html.setAttribute('data-theme', isDark ? 'light' : 'dark');
        });
        // Mirror icon state when theme changes
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark'
                        || document.body.getAttribute('data-theme') === 'dark';
            floatThemeBtn.querySelector('.zsc-icon-sun').style.display  = isDark ? 'none'  : '';
            floatThemeBtn.querySelector('.zsc-icon-moon').style.display = isDark ? ''      : 'none';
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        observer.observe(document.body,            { attributes: true, attributeFilter: ['data-theme'] });
    }
})();

/* ── Floating left main menu ────────────────────────────────────── */
(function () {
    const menuBtn   = document.getElementById('zsc-float-menu-btn');
    const panel     = document.getElementById('zsc-float-menu-panel');
    const backdrop  = document.getElementById('zsc-float-menu-backdrop');
    const closeBtn  = document.getElementById('zsc-float-menu-close');

    function openMenu()  { panel.classList.add('open'); backdrop.classList.add('open'); }
    function closeMenu() { panel.classList.remove('open'); backdrop.classList.remove('open'); }

    menuBtn.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    backdrop.addEventListener('click', closeMenu);

    // Close on Escape
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
})();

/* ── OSM Location Map ───────────────────────────────────────────── */
(function () {
    // Java Island centre — good default for Indonesia deployments
    const JAVA = [-7.5, 110.0];
    const JAVA_ZOOM = 7;
    let map = null;
    let markers = [];

    function initMap() {
        if (map) return; // already initialised and container still live
        const el = document.getElementById('zsc-location-map');
        if (!el) return;

        map = L.map(el, { zoomControl: true, scrollWheelZoom: true }).setView(JAVA, JAVA_ZOOM);
        window._zscMap = map;

        // CartoDB Positron — clean, label-light tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);
    }

    function makeIcon(markerClass, size) {
        const s = size || 14;
        return L.divIcon({
            className: '',
            html: `<div class="${markerClass}" style="width:${s}px;height:${s}px;"></div>`,
            iconSize: [s, s],
            iconAnchor: [s/2, s/2],
            popupAnchor: [0, -(s/2 + 4)]
        });
    }

    // Build a room → alert-level map from Alpine's originalData.
    // Returns { 'room-name': 'critical' | 'warning' | null }
    function buildAlertMap(originalData) {
        const alertMap = {};
        if (!originalData?.status_type_widgets) return alertMap;
        originalData.status_type_widgets.forEach(w => {
            const cat = w.status_type.category;
            if (cat !== 'critical' && cat !== 'warning') return;
            w.status_type.device_status
                .filter(ds => !ds.marked_as_read && ds.notes !== 'Normal State')
                .forEach(ds => {
                    const room = ds.device?.room;
                    if (!room) return;
                    // critical beats warning
                    if (cat === 'critical') {
                        alertMap[room] = 'critical';
                    } else if (!alertMap[room]) {
                        alertMap[room] = 'warning';
                    }
                });
        });
        return alertMap;
    }

    function refreshMarkers(registeredLocations, activeLocations, alertMap) {
        if (!map) return;
        markers.forEach(m => m.remove());
        markers = [];
        alertMap = alertMap || {};

        Object.entries(registeredLocations).forEach(([key, values]) => {
            const dev = values[0];
            const latlong = dev.latlong || null;
            if (!latlong) return;

            const parts = latlong.split(',').map(Number);
            if (parts.length < 2 || parts.some(isNaN)) return;
            const [lat, lng] = parts;
            // Skip invalid GPS — covers device defaults like "0,0" and "0,1"
            if (Math.abs(lat) <= 1 && Math.abs(lng) <= 1) return;

            const isActive   = Object.keys(activeLocations).includes(key);
            const alertLevel = alertMap[key] || null; // 'critical' | 'warning' | null

            // Determine marker class and size
            let markerClass, markerSize;
            if (alertLevel === 'critical') {
                markerClass = 'map-marker-critical'; markerSize = 16;
            } else if (alertLevel === 'warning') {
                markerClass = 'map-marker-warning';  markerSize = 16;
            } else if (isActive) {
                markerClass = 'map-marker-active';   markerSize = 14;
            } else {
                markerClass = 'map-marker-inactive'; markerSize = 14;
            }

            const alertLabel = alertLevel === 'critical' ? '🔴 Critical alert active'
                             : alertLevel === 'warning'  ? '🟠 Warning alert active'
                             : isActive                  ? '🟢 Active'
                             :                             '⚫ Inactive';

            const m = L.marker([lat, lng], { icon: makeIcon(markerClass, markerSize) })
                .addTo(map)
                .bindPopup(`
                    <strong>${key}</strong><br>
                    ${alertLabel}<br>
                    Coordinates: ${lat}, ${lng}<br>
                    Last ping: ${dev.last_ping_at || 'N/A'}
                `);
            markers.push(m);
        });

        if (markers.length) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        } else {
            map.setView(JAVA, JAVA_ZOOM);
        }
    }

    // Watch Alpine for category switch to locations → init map then plot markers
    document.addEventListener('alpine:init', () => {
        const wait = setInterval(() => {
            const el = document.querySelector('[x-data="dashboard"]');
            if (!el || !el._x_dataStack) return;
            clearInterval(wait);

            const component = Alpine.$data(el);

            // Watch activeCategory — destroy map when leaving locations so
            // Leaflet doesn't point to a detached DOM node next time.
            Alpine.effect(() => {
                const cat = component.activeCategory;

                if (cat === 'locations') {
                    // x-if adds the container after Alpine resolves — wait for it
                    setTimeout(() => {
                        // If map points to a detached element, destroy it first
                        if (map && !document.body.contains(map.getContainer())) {
                            map.remove();
                            map = null;
                        }
                        initMap();
                        map.invalidateSize();

                        // Expose reset function for the overlay button
                        window._zscMapReset = () => {
                            if (!map) return;
                            if (markers.length) {
                                const group = L.featureGroup(markers);
                                map.fitBounds(group.getBounds().pad(0.3));
                            } else {
                                map.setView(JAVA, JAVA_ZOOM);
                            }
                        };

                        const alertMap = buildAlertMap(component.originalData);
                        refreshMarkers(component.registeredLocations, component.activeLocations, alertMap);
                    }, 120);
                } else {
                    // Leaving locations — destroy map so next visit starts clean
                    if (map) {
                        map.remove();
                        map = null;
                        markers = [];
                    }
                }
            });

            // Re-render whenever location data OR status data updates
            Alpine.effect(() => {
                const reg  = component.registeredLocations;
                const act  = component.activeLocations;
                const data = component.originalData;
                if (map && document.body.contains(map.getContainer()) && component.activeCategory === 'locations') {
                    const alertMap = buildAlertMap(data);
                    refreshMarkers(reg, act, alertMap);
                }
            });
        }, 100);
    });
})();
</script>
@endpush