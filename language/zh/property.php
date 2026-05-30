<?php
return [
    // 模块标题
    'title'             => '房产',
    'title_list'        => '房产列表',
    'title_add'         => '添加房产',
    'title_edit'        => '编辑房产',

    // 类型
    'type'              => '类型',
    'type.sell'         => '出售',
    'type.rent'         => '出租',
    'type.select'       => '选择类型',

    // 状态
    'status'            => '状态',
    'status.select'     => '选择状态',
    'status.not_appraised' => '未验证',
    'status.appraised'  => '已验证',
    'status.sold'       => '已售出',

    // 房产类型
    'property_type'     => '房产类型',
    'property_type.apartment' => '公寓',
    'property_type.house'     => '独立住宅',
    'property_type.land'      => '地皮',

    // 卧室
    'bedroom'           => '卧室',
    'bedroom.select'    => '选择卧室数',
    'bedroom.more'      => '超过6间卧室',

    // 卫生间
    'bathroom'          => '卫生间',
    'bathroom.select'   => '选择卫生间数',
    'bathroom.more'     => '超过6间卫生间',

    // 朝向
    'direction'         => '朝向',
    'direction.select'  => '选择朝向',
    'direction.east'    => '东',
    'direction.west'    => '西',
    'direction.south'   => '南',
    'direction.north'   => '北',
    'direction.northeast' => '东北',
    'direction.northwest' => '西北',
    'direction.southeast' => '东南',
    'direction.southwest' => '西南',

    // 位置
    'location'          => '位置',
    'location.not_updated' => '未更新',
    'location.facade'   => '临街',
    'location.alley'    => '巷子',

    // 设施类型
    'features_type.utilities' => '公共设施',
    'features_type.furniture' => '家具',

    // 价格单位
    'unit'              => '单位',
    'unit.select'       => '选择单位',
    'unit.month'        => '月',
    'unit.year'         => '年',
    'unit.m2'           => '平方米',
    'unit.base'         => '套',

    // 内部标签
    'collection.potential' => '潜力',
    'collection.love'      => '收藏',
    'collection.hot'       => '急售',

    // 详细信息
    'name'              => '房产名称',
    'price'             => '价格',
    'area'              => '面积（m²）',
    'address'           => '地址',
    'city'              => '省/市',
    'ward'              => '区/县',
    'city.select'       => '选择省/市',
    'ward.select'       => '选择区/县',
    'map_code'          => '地图代码',
    'juridical'         => '产权状态',
    'slug'              => 'Slug',
    'image'             => '图片',

    // 表单分组
    'form.info'         => '基本信息',
    'form.more_info'    => '更多信息',
    'form.category.sell' => '分类（出售）',
    'form.category.rent' => '分类（出租）',
    'form.type_label'   => '项目类型',

    // 规格
    'specification'             => '规格',
    'specification.more'        => '其他规格',
    'specification.icon'        => '图标',
    'specification.title'       => '标题',
    'specification.content'     => '内容',
    'specification.location'    => '位置信息',
    'specification.location_detail' => '巷道（临街）宽度（米）',
    'specification.features'    => '设施与家具',

    // 列表表格
    'table.image'       => '图片',
    'table.name'        => '房产',
    'table.room'        => '卧/卫',
    'table.city'        => '省/市',
    'table.category'    => '分类',
    'table.status'      => '状态',
    'table.price'       => '价格',
    'table.action'      => '操作',
    'table.all'         => '全部',

    // 显示/隐藏
    'public.show'       => '房产正在显示',
    'public.hidden'     => '房产已隐藏',
    'btn.update'        => '更新',

    // 时间
    'time.year'         => '年',
    'time.month'        => '月',
    'time.week'         => '周',
    'time.day'          => '天',
    'time.hour'         => '小时',
    'time.minute'       => '分钟',
    'time.second'       => '秒',

    // 房产详情标签
    'detail.not_available'       => '更新中',
    'detail.area_label'          => '面积',
    'detail.price_label'         => '价格',
    'detail.broker_contact'      => '联系经纪人',
    'location.not_updated_detail' => '位置未更新',
    'location.alley_detail'      => '巷道宽度 :width (m²)',
    'location.facade_house'      => '临街住宅',

    // 布局构建器
    'layout.all'                 => '所有房产列表页',
    'layout.index'               => '房产分类页',
    'layout.detail'              => '房产详情页',

    // 出售价格范围
    'price.sell.below_500m'      => '5亿以下',
    'price.sell.500_800'         => '5亿至8亿',
    'price.sell.800_1b'          => '8亿至10亿',
    'price.sell.1_2b'            => '10亿 - 20亿',
    'price.sell.2_3b'            => '20亿 - 30亿',
    'price.sell.3_5b'            => '30亿 - 50亿',
    'price.sell.5_10b'           => '50亿 - 100亿',
    'price.sell.above_10b'       => '100亿以上',

    // 出租价格范围
    'price.rent.below_5m'        => '500万以下',
    'price.rent.5_10'            => '500万至1000万',
    'price.rent.10_20'           => '1000万至2000万',
    'price.rent.20_35'           => '2000万至3500万',
    'price.rent.35_50'           => '3500万至5000万',
    'price.rent.50_80'           => '5000万至8000万',
    'price.rent.above_80'        => '8000万以上',

    // 反馈主题
    'feedback.topic.address'     => '房产地址',
    'feedback.topic.info'        => '关于：价格、面积、描述等信息',
    'feedback.topic.photo'       => '照片',
    'feedback.topic.duplicate'   => '重复发布',
    'feedback.topic.no_contact'  => '无法联系',
    'feedback.topic.fake'        => '虚假信息',
    'feedback.topic.sold'        => '房产已售出',
    'feedback.topic.other'       => '他',

    // 反馈表单
    'feedback.form.topic'        => '主题',
    'feedback.form.other'        => '其他反馈',
    'feedback.form.enter_content' => '请输入内容',
    'feedback.form.full_name'    => '姓名',
    'feedback.form.phone'        => '电话号码',
];
