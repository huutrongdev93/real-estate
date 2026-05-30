{{-- Leaflet --}}
<link rel="stylesheet" href="{{ asset('real-estate::add-on/leaflet/leaflet.css') }}">
<script src="{{ asset('real-estate::add-on/leaflet/leaflet.js') }}"></script>

<div class="section-panel section-map-panel">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="heading mb-0">Xem trên bản đồ</p>
        @if(!empty($googleMapLink))
            <a href="{{ $googleMapLink }}" target="_blank" rel="noopener noreferrer"
               class="btn btn-sm btn-outline-danger">
                <i class="fab fa-google me-1"></i> Xem trên Google Maps
            </a>
        @else
            <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
               target="_blank" rel="noopener noreferrer"
               class="btn btn-sm btn-outline-danger">
                <i class="fab fa-google me-1"></i> Xem trên Google Maps
            </a>
        @endif
    </div>

    <div id="re_detail_map" style="height:400px;width:100%;border-radius:8px;overflow:hidden;"></div>
</div>

<script>
(function () {
    const lat = {{ (float) $lat }};
    const lng = {{ (float) $lng }};

    const _leafletAssets = '{{ \SkillDo\Cms\Support\Url::base("plugins/real-estate/assets/add-on/leaflet/images/") }}';

    const _icon = L.icon({
        iconUrl:       _leafletAssets + 'marker-icon.png',
        iconRetinaUrl: _leafletAssets + 'marker-icon-2x.png',
        shadowUrl:     _leafletAssets + 'marker-shadow.png',
        iconSize:    [25, 41],
        iconAnchor:  [12, 41],
        popupAnchor: [1, -34],
        shadowSize:  [41, 41],
    });

    const map = L.map('re_detail_map', { scrollWheelZoom: false }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    }).addTo(map);

    L.marker([lat, lng], { icon: _icon })
        .addTo(map)
        .bindPopup('{{ addslashes($object->name ?? '') }}')
        .openPopup();
})();
</script>
