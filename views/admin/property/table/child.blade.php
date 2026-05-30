<div class="re-child-detail">

    <div class="re-child-header">
        <div class="re-child-header-left">
            @if(!empty($item->image))
                <div class="re-child-thumb">
                    <img src="{{ Image::source($item->image)->link() }}" alt="{{ $item->name }}">
                </div>
            @endif
            <div class="re-child-headline">
                <div class="re-child-badges">
                    @if(!empty($typeLabel))
                        <span class="re-badge" style="background:{{ $typeColor }}20; color:{{ $typeColor }}; border:1px solid {{ $typeColor }}40;">
                            <i class="fa-light fa-tag"></i> {{ $typeLabel }}
                        </span>
                    @endif
                    @if(!empty($propertyTypeLabel))
                        <span class="re-badge re-badge-gray">
                            <i class="fa-light fa-building"></i> {{ $propertyTypeLabel }}
                        </span>
                    @endif
                    @if(!empty($statusLabel))
                        <span class="re-badge" style="background:{{ $statusColor }}20; color:{{ $statusColor }}; border:1px solid {{ $statusColor }}40;">
                            <i class="fa-light fa-circle-check"></i> {{ $statusLabel }}
                        </span>
                    @endif
                    <span class="re-badge re-badge-id">
                        # {{ $item->id }}
                    </span>
                </div>
                <h2 class="re-child-title">{!! $item->name !!}</h2>
                @if(!empty($item->excerpt))
                    <p class="re-child-excerpt">{!! strip_tags($item->excerpt) !!}</p>
                @endif
            </div>
        </div>
        <div class="re-child-header-right">
            <div class="re-child-price-box">
                <span class="re-child-price-label">Giá</span>
                <span class="re-child-price-value">{!! PropertyHelper::getPriceFull($item->price ?? 0, $item->unit ?? '') !!}</span>
            </div>
            <div class="re-child-actions">
                <a href="{{ Url::admin('property/edit/' . $item->id) }}" class="re-btn re-btn-primary">
                    <i class="fa-light fa-pen-to-square"></i> Chỉnh sửa
                </a>
                <a href="{{ Url::permalink($item->slug) }}" target="_blank" class="re-btn re-btn-outline">
                    <i class="fa-light fa-arrow-up-right-from-square"></i> Xem trang
                </a>
            </div>
        </div>
    </div>

    <div class="re-child-body">

        {{-- ===== THÔNG SỐ KỸ THUẬT ===== --}}
        <div class="re-child-section">
            <div class="re-child-section-title"><i class="fa-light fa-list-check"></i> Thông số</div>
            <div class="re-child-stats">
                @if(!empty($item->area))
                    <div class="re-stat-item">
                        <i class="fa-light fa-vector-square"></i>
                        <div>
                            <span class="re-stat-val">{{ $item->area }} m²</span>
                            <span class="re-stat-key">Diện tích</span>
                        </div>
                    </div>
                @endif
                @if(!empty($item->bedroom))
                    <div class="re-stat-item">
                        <i class="fa-light fa-bed"></i>
                        <div>
                            <span class="re-stat-val">{{ PropertyHelper::getBedroom($item->bedroom) }}</span>
                            <span class="re-stat-key">Phòng ngủ</span>
                        </div>
                    </div>
                @endif
                @if(!empty($item->bathroom))
                    <div class="re-stat-item">
                        <i class="fa-light fa-bath"></i>
                        <div>
                            <span class="re-stat-val">{{ PropertyHelper::getBathroom($item->bathroom) }}</span>
                            <span class="re-stat-key">Phòng tắm</span>
                        </div>
                    </div>
                @endif
                @if(!empty($directionLabel))
                    <div class="re-stat-item">
                        <i class="fa-light fa-compass"></i>
                        <div>
                            <span class="re-stat-val">{{ $directionLabel }}</span>
                            <span class="re-stat-key">Hướng</span>
                        </div>
                    </div>
                @endif
                @if(!empty($locationLabel))
                    <div class="re-stat-item">
                        <i class="fa-light fa-road"></i>
                        <div>
                            <span class="re-stat-val">{{ $locationLabel }}</span>
                            <span class="re-stat-key">Vị trí</span>
                        </div>
                    </div>
                @endif
                @if(!empty($locationDetail))
                    <div class="re-stat-item">
                        <i class="fa-light fa-ruler-horizontal"></i>
                        <div>
                            <span class="re-stat-val">{{ $locationDetail }} m</span>
                            <span class="re-stat-key">Chiều rộng hẻm</span>
                        </div>
                    </div>
                @endif
                @if(!empty($juridical))
                    <div class="re-stat-item">
                        <i class="fa-light fa-file-certificate"></i>
                        <div>
                            <span class="re-stat-val">{{ $juridical }}</span>
                            <span class="re-stat-key">Pháp lý</span>
                        </div>
                    </div>
                @endif
                @if($item->category)
                    <div class="re-stat-item">
                        <i class="fa-light fa-folder-open"></i>
                        <div>
                            <span class="re-stat-val">{{ $item->category->name }}</span>
                            <span class="re-stat-key">Chuyên mục</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== VỊ TRÍ ===== --}}
        @if(!empty($item->address) || !empty($cityName))
            <div class="re-child-section">
                <div class="re-child-section-title"><i class="fa-light fa-location-dot"></i> Vị trí</div>
                <div class="re-child-location">
                    <i class="fa-light fa-map-pin re-location-icon"></i>
                    <span>{!! \RealEstate\Supports\RealEstateHelper::getLocation($item) !!}</span>
                </div>
                @if($wardName)
                    <div class="re-child-location-detail">
                        <span class="re-detail-chip"><i class="fa-light fa-city"></i> {{ $wardName }}</span>
                        @if($cityName) <span class="re-detail-chip"><i class="fa-light fa-map"></i> {{ $cityName }}</span> @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- ===== ĐẶC ĐIỂM KHÁC ===== --}}
        @if(have_posts($moreSpecs))
            <div class="re-child-section">
                <div class="re-child-section-title"><i class="fa-light fa-sparkles"></i> Đặc điểm nổi bật</div>
                <div class="re-more-specs">
                    @foreach($moreSpecs as $spec)
                        @if(!empty($spec['title']) || !empty($spec['content']))
                            <div class="re-spec-chip">
                                @if(!empty($spec['icon']))
                                    <img src="{{ \SkillDo\Cms\Support\File::url($spec['icon']) }}" alt="" class="re-spec-icon">
                                @else
                                    <i class="fa-light fa-check-circle"></i>
                                @endif
                                <div>
                                    @if(!empty($spec['title'])) <strong>{{ $spec['title'] }}</strong> @endif
                                    @if(!empty($spec['content'])) <span>{{ $spec['content'] }}</span> @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== TIỆN ÍCH & NỘI THẤT ===== --}}
        @if((is_array($utilities) && count($utilities)) || (is_array($furniture) && count($furniture)))
            <div class="re-child-section">
                <div class="re-child-section-title"><i class="fa-light fa-couch"></i> Tiện ích & Nội thất</div>
                <div class="re-features-grid">
                    @if(is_array($utilities) && count($utilities))
                        @foreach($utilities as $featureId)
                            @php $feat = \RealEstate\Models\Features::find($featureId); @endphp
                            @if($feat)
                                <span class="re-feature-tag re-feature-utility">
                                    <i class="fa-light fa-circle-check"></i> {{ $feat->name }}
                                </span>
                            @endif
                        @endforeach
                    @endif
                    @if(is_array($furniture) && count($furniture))
                        @foreach($furniture as $featureId)
                            @php $feat = \RealEstate\Models\Features::find($featureId); @endphp
                            @if($feat)
                                <span class="re-feature-tag re-feature-furniture">
                                    <i class="fa-light fa-couch"></i> {{ $feat->name }}
                                </span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        {{-- ===== BẢN ĐỒ ===== --}}
        @if(!empty($mapCode))
            <div class="re-child-section">
                <div class="re-child-section-title"><i class="fa-light fa-map"></i> Bản đồ</div>
                <div class="re-map-wrapper">{!! $mapCode !!}</div>
            </div>
        @endif

    </div>{{-- /.re-child-body --}}

    <div class="re-child-footer">
        <span><i class="fa-light fa-calendar"></i> Ngày tạo: {{ !empty($item->created) ? date('d/m/Y H:i', strtotime($item->created)) : '—' }}</span>
        <span><i class="fa-light fa-sort"></i> Thứ tự: {{ $item->order ?? 0 }}</span>
        <span>
            <i class="fa-light fa-eye{{ ($item->public ?? 0) ? '' : '-slash' }}"></i>
            {{ ($item->public ?? 0) ? 'Đang hiển thị' : 'Đang ẩn' }}
        </span>
    </div>

</div>{{-- /.re-child-detail --}}
