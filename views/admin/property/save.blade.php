{!! Admin::partial('resources/page-default/page-save', [
    'module' => 'property',
    'object' => $object ?? [],
    'form'   => $form,
]) !!}
<script>
    $(function () {
        let propertyTypeInput = $('#box_type input');
        let boxCategorySellId = $('#box_category_sell_id');
        let boxCategoryRentId = $('#box_category_rent_id');

        function categoryView(type) {
            if (type === 'sell') {
                boxCategorySellId.show();
                boxCategoryRentId.hide();
            } else {
                boxCategorySellId.hide();
                boxCategoryRentId.show();
            }
        }

        let currentType = propertyTypeInput.filter(':checked').val() || propertyTypeInput.first().val();
        categoryView(currentType);

        propertyTypeInput.on('change', function () {
            categoryView($(this).val());
        });
    });
</script>

<div class="modal fade" id="re_map_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fad fa-map-marked-alt me-2"></i> Chọn vị trí trên bản đồ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="position:relative;">
                <div style="position:absolute;top:12px;left:50%;transform:translateX(-50%);z-index:1000;width:420px;max-width:90vw;">
                    <div class="input-group shadow-sm">
                        <input type="text" id="re_map_search" class="form-control" placeholder="Tìm địa chỉ…" autocomplete="off">
                        <button class="btn btn-primary" id="re_map_search_btn" type="button">
                            <i class="fad fa-search"></i>
                        </button>
                    </div>
                    <div id="re_map_search_results" class="list-group mt-1 shadow" style="display:none;max-height:200px;overflow-y:auto;"></div>
                </div>
                <div id="re_map_picker" style="height:500px;"></div>
                <div style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);z-index:1000;">
                    <span class="badge bg-dark fs-6 px-3 py-2" id="re_map_coords">Nhấn vào bản đồ để chọn vị trí</span>
                </div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto">
                    <i class="fad fa-info-circle"></i> Kéo ghim hoặc nhấn vào bản đồ để đặt vị trí chính xác.
                </small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                <button type="button" class="btn btn-primary" id="re_map_confirm">
                    <i class="fad fa-check-circle me-1"></i> Xác nhận toạ độ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {

    /* ════ NÚT MỞ PICKER ════ */
    const $btnPicker = $(
        '<div class="col-md-12 form-group form-group-stacked">' +
        '<button type="button" class="btn btn-blue mt-2" id="re_open_map_picker">' +
        '<i class="fad fa-map-marker-alt me-1"></i> Chọn toạ độ từ bản đồ' +
        '</button>' +
        '</div>'
    );

    $('#admin_property_location .row').after($btnPicker);

    $btnPicker.on('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('re_map_modal'));
        modal.show();

        document.getElementById('re_map_modal').addEventListener('shown.bs.modal', function ()
        {
            const currentLat = parseFloat($('#lat').val()) || 10.8525693;
            const currentLng = parseFloat($('#lng').val()) || 106.7193772;

            if (window._rePickerMap)
            {
                window._rePickerMap.invalidateSize();
                window._rePickerMap.setView([currentLat, currentLng], 15);
                window._rePickerMarker.setLatLng([currentLat, currentLng]);
                updateTempCoords(currentLat, currentLng);
                return;
            }

            const _leafletAssets = '{{ \SkillDo\Cms\Support\Url::base("plugins/real-estate/assets/add-on/leaflet/images/") }}';
            const _pickerIcon = L.icon({
                iconUrl:       _leafletAssets + 'marker-icon.png',
                iconRetinaUrl: _leafletAssets + 'marker-icon-2x.png',
                shadowUrl:     _leafletAssets + 'marker-shadow.png',
                iconSize:    [25, 41],
                iconAnchor:  [12, 41],
                popupAnchor: [1, -34],
                shadowSize:  [41, 41],
            });

            const initLat = currentLat;
            const initLng = currentLng;

            const map = L.map('re_map_picker').setView([initLat, initLng], 13);

            L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google Maps'
            }).addTo(map);

            const marker = L.marker([initLat, initLng], { draggable: true, icon: _pickerIcon }).addTo(map);

            window._rePickerMap    = map;
            window._rePickerMarker = marker;

            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                updateTempCoords(pos.lat, pos.lng);
            });

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateTempCoords(e.latlng.lat, e.latlng.lng);
            });

            updateTempCoords(initLat, initLng);

        }, { once: true });
    });

    /* ── Toạ độ tạm ── */
    let _tempLat = null, _tempLng = null;

    function updateTempCoords(lat, lng) {
        _tempLat = lat;
        _tempLng = lng;
        $('#re_map_coords').text('Lat: ' + lat.toFixed(6) + '  |  Lng: ' + lng.toFixed(6));
    }

    /* ════ XÁC NHẬN ════ */
    $('#re_map_confirm').on('click', function () {
        if (_tempLat === null) {
            SkilldoMessage.response({ status: 'warning', message: 'Vui lòng chọn vị trí trên bản đồ trước.' });
            return;
        }
        $('#lat').val(_tempLat.toFixed(7)).trigger('input');
        $('#lng').val(_tempLng.toFixed(7)).trigger('input');
        bootstrap.Modal.getInstance(document.getElementById('re_map_modal')).hide();
        SkilldoMessage.response({ status: 'success', message: 'Đã cập nhật toạ độ.' });
    });

    /* ════ TÌM KIẾM ĐỊA CHỈ (qua Nominatim proxy của locator-store) ════ */
    let _searchTimer = null;

    $('#re_map_search').on('input', function () {
        clearTimeout(_searchTimer);
        const q = $(this).val().trim();
        if (q.length < 3) { $('#re_map_search_results').hide().empty(); return; }
        _searchTimer = setTimeout(() => searchAddress(q), 400);
    });

    $('#re_map_search_btn').on('click', function () {
        const q = $('#re_map_search').val().trim();
        if (q.length >= 2) searchAddress(q);
    });

    $('#re_map_search').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $(this).trigger('input'); }
    });

    function searchAddress(q) {
        const $results = $('#re_map_search_results');
        $results.html('<li class="list-group-item text-muted py-2"><i class="fad fa-spinner fa-spin me-1"></i> Đang tìm…</li>').show();

        request.post(ajax, {
            action: 'RealEstate\\Ajax\\Admin\\PropertyAjax::searchAddress',
            q: q,
        }).then(function (response) {
            $results.empty();
            if (!response.data || response.status !== 'success') {
                $results.html('<li class="list-group-item text-muted py-2">Không tìm thấy kết quả.</li>');
                return;
            }
            const places = response.data;
            if (!places.length) {
                $results.html('<li class="list-group-item text-muted py-2">Không tìm thấy kết quả.</li>');
                return;
            }
            places.forEach(function (place) {
                $('<a href="#" class="list-group-item list-group-item-action py-1 px-2" style="font-size:.85rem">')
                    .text(place.display_name)
                    .on('click', function (e) {
                        e.preventDefault();
                        window._rePickerMap.setView([place.lat, place.lng], 16);
                        window._rePickerMarker.setLatLng([place.lat, place.lng]);
                        updateTempCoords(place.lat, place.lng);
                        $results.hide().empty();
                        $('#re_map_search').val(place.display_name);
                    })
                    .appendTo($results);
            });
        }).catch(function () {
            $results.html('<li class="list-group-item text-danger py-2"><i class="fad fa-exclamation-circle me-1"></i> Lỗi kết nối.</li>');
        });
    }

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#re_map_search, #re_map_search_results').length) {
            $('#re_map_search_results').hide();
        }
    });

});
</script>
