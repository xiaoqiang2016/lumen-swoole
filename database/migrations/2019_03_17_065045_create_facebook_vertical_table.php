<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookVerticalTable extends Migration
{
    private $tableName = 't_facebook_vertical';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('key',50)->nullable(false)->comment("键名");
            $table->string('parent_key',50)->nullable(false)->default('')->comment('上级键名');
            $table->string('name_cn',50)->nullable(false)->default('')->comment('中文名');
            $table->string('name_en',50)->nullable(false)->default('')->comment('英文名');
            $table->tinyInteger('level')->nullable(false)->default(0)->comment('层级');
            $table->tinyInteger('status')->nullable(false)->default(0)->comment('状态');
            $table->timestamps();
        });
        $this->seeder();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
    private function seeder(){
        $verticals = [
            [
                'key' => 'ADVERTISING_AND_MARKETING',
                'name_cn' => '广告与营销',
                'name_en' => 'Advertising & Marketing',
                'children' => [
                    [
                        'key' => 'PR',
                        'name_cn' => 'PR',
                        'name_en' => 'PR',
                    ],
                    [
                        'key' => 'DIGITAL_ADVERTISING_AND_MARKETING_OR_UNTAGGED_AGENCIES',
                        'name_cn' => '数字广告/营销/无标签代理商',
                        'name_en' => 'Digital Advertising & Marketing/Untagged Agencies',
                    ],
                    [
                        'key' => 'MEDIA',
                        'name_cn' => '媒体',
                        'name_en' => 'Media',
                    ],
                ]
            ],
            [
                'key' => 'AUTOMOTIVE',
                'parent_key' => '',
                'name_cn' => '汽车',
                'name_en' => 'Automotive',
                'children' => [
                    [
                        'key' => 'AUTO_AGENCY',
                        'name_cn' => '汽车代理',
                        'name_en' => 'Auto Agency',
                    ],
                    [
                        'key' => 'AUTOMOTIVE_MANUFACTURER',
                        'name_cn' => '汽车制造商',
                        'name_en' => 'Automotive Manufacturer',
                    ],
                    [
                        'key' => 'DEALERSHIP',
                        'name_cn' => '经销商',
                        'name_en' => 'Dealership',
                    ],
                    [
                        'key' => 'INDUSTRIAL_AND_FARM_VEHICLE',
                        'name_cn' => '工业和农用车辆',
                        'name_en' => 'Industrial & Farm Vehicle',
                    ],
                    [
                        'key' => 'MOTORCYCLES',
                        'name_cn' => '摩托车',
                        'name_en' => 'Motorcycles',
                    ],
                    [
                        'key' => 'PARTS_AND_SERVICE',
                        'name_cn' => '零件和服务',
                        'name_en' => 'Parts & Service',
                    ],
                    [
                        'key' => 'RECREATIONAL',
                        'name_cn' => '娱乐',
                        'name_en' => 'Recreational',
                    ],
                ]
            ],[
                'key' => 'CONSUMER_PACKAGED_GOODS',
                'parent_key' => '',
                'name_cn' => '包装消费品',
                'name_en' => 'Consumer Packaged Goods',
                'children' => [
                    [
                        'key' => 'BEAUTY_AND_PERSONAL_CARE',
                        'name_cn' => '美容与个人护理',
                        'name_en' => 'Beauty & Personal Care',
                    ],
                    [
                        'key' => 'BEER_AND_WINE_AND_LIQUOR',
                        'name_cn' => '啤酒，葡萄酒和白酒',
                        'name_en' => 'Beer, Wine & Liquor',
                    ],
                    [
                        'key' => 'FOOD',
                        'name_cn' => '餐饮',
                        'name_en' => 'Food',
                    ],
                    [
                        'key' => 'HOUSEHOLD_GOODS',
                        'name_cn' => '家庭用品',
                        'name_en' => 'Household Goods',
                    ],
                    [
                        'key' => 'OFFICE',
                        'name_cn' => '办公室',
                        'name_en' => 'Office',
                    ],
                    [
                        'key' => 'PET',
                        'name_cn' => '宠物',
                        'name_en' => 'Pet',
                    ],
                    [
                        'key' => 'PHARMACEUTICAL_OR_HEALTH',
                        'name_cn' => '制药/保健',
                        'name_en' => 'Pharmaceutical/Health',
                    ],
                    [
                        'key' => 'TOBACCO',
                        'name_cn' => '烟草',
                        'name_en' => 'Tobacco',
                    ],
                    [
                        'key' => 'WATER_AND_SOFT_DRINK_AND_BAVERAGE',
                        'name_cn' => '水，软饮料和饮料',
                        'name_en' => 'Water, Soft Drink & Beverage',
                    ],
                ]
            ],
            [
                'key' => 'ECOMMERCE',
                'parent_key' => '',
                'name_cn' => '电子商务',
                'name_en' => 'Ecommerce',
                'children' => [
                    [
                        'key' => 'AUCTIONS',
                        'name_cn' => '拍卖会',
                        'name_en' => 'Auctions',
                    ],
                    [
                        'key' => 'DAILYDEALS',
                        'name_cn' => '每日交易',
                        'name_en' => 'Dailydeals',
                    ],
                    [
                        'key' => 'ECATALOG',
                        'name_cn' => '电子目录',
                        'name_en' => 'Ecatalog',
                    ],
                    [
                        'key' => 'SMB_CATALOG',
                        'name_cn' => '目录（SMB）',
                        'name_en' => 'Catalog (SMB)',
                    ],
                ]
            ],
            [
                'key' => 'EDUCATION',
                'parent_key' => '',
                'name_cn' => '教育',
                'name_en' => 'Education',
                'children' => [
                    [
                        'key' => 'ED_TECH',
                        'name_cn' => 'Ed Tech',
                        'name_en' => 'Ed Tech',
                    ],
                    [
                        'key' => 'EDUCATION_RESOURCES',
                        'name_cn' => '教育资源',
                        'name_en' => 'Education Resources',
                    ],
                    [
                        'key' => 'ELEARNING_AND_MASSIVE_ONLINE_OPEN_COURSES',
                        'name_cn' => '电子教学和大规模在线公开课程（MOOCs）',
                        'name_en' => 'elearning & Massive Online Open Courses (MOOCs)',
                    ],
                    [
                        'key' => 'FOR_PROFIT_COLLEGES_AND_UNIVERSITIES',
                        'name_cn' => '对于利润学院和大学',
                        'name_en' => 'For Profit Colleges & Universities',
                    ],
                    [
                        'key' => 'NOT_FOR_PROFIT_COLLEGES_AND_UNIVERSITIES',
                        'name_cn' => '非营利性学院和大学',
                        'name_en' => 'Not-for Profit Colleges & Universities',
                    ],
                    [
                        'key' => 'SCHOOL_AND_EARLY_CHILDREN_EDCATION',
                        'name_cn' => '学校和幼儿教育',
                        'name_en' => 'School & Early Childhood Education',
                    ],
                    [
                        'key' => 'TRADE_SCHOOL',
                        'name_cn' => '贸易学校',
                        'name_en' => 'Trade School',
                    ],
                ]
            ],
            [
                'key' => 'ENERGY_AND_UTILITIES',
                'parent_key' => '',
                'name_cn' => '能源与公用事业',
                'name_en' => 'Energy & Utilities',
                'children' => [
                    [
                        'key' => 'OIL_AND_GAS_AND_CONSUMABLE_FUEL',
                        'name_cn' => '石油，天然气和消耗性燃料',
                        'name_en' => 'Oil, Gas & Consumable Fuel',
                    ],
                    [
                        'key' => 'SMB_ENERGY',
                        'name_cn' => 'Energy (SMB)',
                        'name_en' => '能源（SMB）',
                    ],
                    [
                        'key' => 'UTILITIES_AND_ENERGY_EQUIPMENT_AND_SERVICES',
                        'name_cn' => '公用事业，能源设备和服务',
                        'name_en' => 'Utilities, Energy Equipment & Services',
                    ],
                ]
            ],
            [
                'key' => 'ENTERTAINMENT_AND_MEDIA',
                'parent_key' => '',
                'name_cn' => '娱乐与媒体',
                'name_en' => 'Entertainment & Media',
                'children' => [
                    [
                        'key' => 'ARTS',
                        'name_cn' => '艺术',
                        'name_en' => 'Arts',
                    ],
                    [
                        'key' => 'EVENTS',
                        'name_cn' => '活动',
                        'name_en' => 'Events',
                    ],
                    [
                        'key' => 'FITNESS',
                        'name_cn' => '身体素质',
                        'name_en' => 'Fitness',
                    ],
                    [
                        'key' => 'GAMBLING',
                        'name_cn' => '赌博',
                        'name_en' => 'Gambling',
                    ],
                    [
                        'key' => 'MOVIES',
                        'name_cn' => '电影',
                        'name_en' => 'Movies',
                    ],
                    [
                        'key' => 'MUSEUMS_AND_PARKS_AND_LIBRARIES',
                        'name_cn' => '博物馆，公园，图书馆',
                        'name_en' => 'Museums, Parks, Libraries',
                    ],
                    [
                        'key' => 'MUSIC_AND_RADIO',
                        'name_cn' => '音乐与广播',
                        'name_en' => 'Music & Radio',
                    ],
                    [
                        'key' => 'PUBLISHING_INTERNET',
                        'name_cn' => '发布互联网',
                        'name_en' => 'Publishing Internet',
                    ],
                    [
                        'key' => 'SMB_AGENTS_AND_PROMOTERS',
                        'name_cn' => '代理商和促销商（SMB）',
                        'name_en' => 'Agents & Promoters (SMB)',
                    ],
                    [
                        'key' => 'SMB_ARTISTS_AND_PERFORMERS',
                        'name_cn' => '艺术家和表演者（SMB）',
                        'name_en' => 'Artists & Performers (SMB)',
                    ],
                    [
                        'key' => 'SMB_INFORMATION',
                        'name_cn' => '信息（SMB）',
                        'name_en' => 'Information (SMB)',
                    ],
                    [
                        'key' => 'SPORTS',
                        'name_cn' => '活动',
                        'name_en' => 'Sports',
                    ],
                    [
                        'key' => 'STREAMING',
                        'name_cn' => '流',
                        'name_en' => 'Streaming',
                    ],
                    [
                        'key' => 'TELEVISION',
                        'name_cn' => '电视',
                        'name_en' => 'Television',
                    ],
                ]
            ],
            [
                'key' => 'FINANCIAL_SERVICES',
                'parent_key' => '',
                'name_cn' => '金融服务',
                'name_en' => 'Financial Services',
                'children' => [
                    [
                        'key' => 'CREDIT_AND_FINANCING_AND_MORTAGES',
                        'name_cn' => '信贷，融资，抵押贷款',
                        'name_en' => 'Credit, Financing, Mortgages',
                    ],
                    [
                        'key' => 'INSURANCE',
                        'name_cn' => '保险',
                        'name_en' => 'Insurance',
                    ],
                    [
                        'key' => 'INVESTMENT_BANK_AND_BROKERAGE',
                        'name_cn' => '投资银行和经纪业务',
                        'name_en' => 'Investment Bank & Brokerage',
                    ],
                    [
                        'key' => 'REAL_ESTATE',
                        'name_cn' => '房地产',
                        'name_en' => 'Real Estate',
                    ],
                    [
                        'key' => 'RETAIL_AND_CREDIT_UNION_AND_COMMERCIAL_BANK',
                        'name_cn' => '零售，信用社和商业银行',
                        'name_en' => 'Retail, Credit Union & Commercial Bank',
                    ],
                ]
            ],
            [
                'key' => 'GAMING',
                'parent_key' => '',
                'name_cn' => '赌博',
                'name_en' => 'Gaming',
                'children' => [
                    [
                        'key' => 'CONSOLE_DEVELOPER',
                        'name_cn' => '控制台，开发者',
                        'name_en' => 'Console-Developer',
                    ],
                    [
                        'key' => 'CONSOLE_DEVICE',
                        'name_cn' => '控制台设备',
                        'name_en' => 'Console-Device',
                    ],
                    [
                        'key' => 'MOBILE_AND_SOCIAL',
                        'name_cn' => '移动与社交',
                        'name_en' => 'Mobile & Social',
                    ],
                    [
                        'key' => 'ONLINE_OR_SOFTWARE',
                        'name_cn' => '在线/软件',
                        'name_en' => 'Online/Software',
                    ],
                    [
                        'key' => 'REAL_MONEY_OR_SKILLED_GAMING',
                        'name_cn' => '真钱/熟练游戏',
                        'name_en' => 'Real Money/Skilled Gaming',
                    ],
                    [
                        'key' => 'SMB_CANVAS',
                        'name_cn' => '画布（SMB）',
                        'name_en' => 'Canvas (SMB)',
                    ],
                    [
                        'key' => 'SMB_CROSS_PLATFORM',
                        'name_cn' => '跨平台（SMB）',
                        'name_en' => 'Cross Platform (SMB)',
                    ],
                    [
                        'key' => 'SMB_GAME_AND_TOY',
                        'name_cn' => '游戏与玩具（SMB）',
                        'name_en' => 'Game & Toy (SMB)',
                    ],
                    [
                        'key' => 'SOFTWARE',
                        'name_cn' => '软件',
                        'name_en' => 'Software',
                    ],
                ]
            ],
            [
                'key' => 'GOVERMENT_AND_POLITICS',
                'parent_key' => '',
                'name_cn' => '政府与政治',
                'name_en' => 'Government & Politics',
                'children' => [
                    [
                        'key' => 'GOVERNMENT',
                        'name_cn' => '政府',
                        'name_en' => 'Government',
                    ],
                    [
                        'key' => 'POLITICAL',
                        'name_cn' => '政治',
                        'name_en' => 'Political',
                    ],
                    [
                        'key' => 'SEASONAL_POLITICAL_SPENDERS',
                        'name_cn' => '季节性政治人物',
                        'name_en' => 'Seasonal Political Spenders',
                    ],
                ]
            ],
            [
                'key' => 'ORGANIZATIONS_AND_ASSOCIATIONS',
                'parent_key' => '',
                'name_cn' => '组织和协会',
                'name_en' => 'Organizations & Associations',
                'children' => [
                    [
                        'key' => 'NON_PROFIT',
                        'name_cn' => '非盈利',
                        'name_en' => 'Non-Profit',
                    ],
                    [
                        'key' => 'RELIGIOUS',
                        'name_cn' => '宗教',
                        'name_en' => 'Religious',
                    ],
                    [
                        'key' => 'SMB_RELIGIOUS',
                        'name_cn' => '宗教（SMB）',
                        'name_en' => 'Religious (SMB)',
                    ],
                ]
            ],
            [
                'key' => 'OTHER',
                'parent_key' => '',
                'name_cn' => '其他',
                'name_en' => 'Other',
                'children' => [
                    [
                        'key' => 'ECOMMERCE_AGRICULTURE_AND_FARMING',
                        'name_cn' => '电子商务农业与农业',
                        'name_en' => 'Ecommerce Agriculture & Farming',
                    ],
                    [
                        'key' => 'B2B_MANUFACTURING',
                        'name_cn' => 'B2B制造业',
                        'name_en' => 'B2B Manufacturing',
                    ],
                    [
                        'key' => 'CONSTRUCTION_AND_MINING',
                        'name_cn' => '建筑与采矿',
                        'name_en' => 'Construction & Mining',
                    ],
                    [
                        'key' => 'TRANSPORTATION_EQUIPMENT',
                        'name_cn' => '运输设备',
                        'name_en' => 'Transportation Equipment',
                    ],
                ]
            ],
            [
                'key' => 'PROFESSIONAL_SERVICES',
                'parent_key' => '',
                'name_cn' => '专业的服务',
                'name_en' => 'Professional Services',
                'children' => [
                    [
                        'key' => 'ACCOUNTING_AND_TAXES_AND_LEGAL',
                        'name_cn' => '会计，税务与法律',
                        'name_en' => 'Accounting, Taxes & Legal',
                    ],
                    [
                        'key' => 'BUSINESS_SUPPORT_SERVICES',
                        'name_cn' => '业务支持服务',
                        'name_en' => 'Business Support Services',
                    ],
                    [
                        'key' => 'CAREER',
                        'name_cn' => '事业',
                        'name_en' => 'Career',
                    ],
                    [
                        'key' => 'CONSULTING',
                        'name_cn' => '咨询',
                        'name_en' => 'Consulting',
                    ],
                    [
                        'key' => 'DATING',
                        'name_cn' => 'Dating',
                        'name_en' => 'Dating',
                    ],
                    [
                        'key' => 'ENGINEERING_AND_DESIGN',
                        'name_cn' => '工程设计',
                        'name_en' => 'Engineering & Design',
                    ],
                    [
                        'key' => 'FAMILY_AND_HEALTH',
                        'name_cn' => '家庭与健康',
                        'name_en' => 'Family & Health',
                    ],
                    [
                        'key' => 'HOME_SERVICE',
                        'name_cn' => '家政服务',
                        'name_en' => 'Home Service',
                    ],
                    [
                        'key' => 'PHOTOGRAPHY_AND_FILMING_SERVICES',
                        'name_cn' => '摄影和拍摄服务',
                        'name_en' => 'Photography & Filming Services',
                    ],
                    [
                        'key' => 'SMB_REPAIR_AND_MAINTENANCE',
                        'name_cn' => '维修和维护（SMB）',
                        'name_en' => 'Repair & Maintenance (SMB)',
                    ],
                ]
            ],
            [
                'key' => 'RETAIL',
                'parent_key' => '',
                'name_cn' => '零售',
                'name_en' => 'Retail',
                'children' => [
                    [
                        'key' => 'APPAREL_AND_ACCESSORIES',
                        'name_cn' => '服饰与配饰',
                        'name_en' => 'Apparel & Accessories',
                    ],
                    [
                        'key' => 'BOOKSTORES',
                        'name_cn' => '书店',
                        'name_en' => 'Bookstores',
                    ],
                    [
                        'key' => 'DEPARTMENT_STORE',
                        'name_cn' => '百货商店',
                        'name_en' => 'Department Store',
                    ],
                    [
                        'key' => 'FOOTWEAR',
                        'name_cn' => '鞋',
                        'name_en' => 'Footwear',
                    ],
                    [
                        'key' => 'GROCERY_AND_DRUG_AND_CONVENIENCE',
                        'name_cn' => '杂货，药物和便利',
                        'name_en' => 'Grocery, Drug & Convenience',
                    ],
                    [
                        'key' => 'HOME_AND_OFFICE',
                        'name_cn' => '在家办公',
                        'name_en' => 'Home & Office',
                    ],
                    [
                        'key' => 'HOME_IMPROVEMENT',
                        'name_cn' => '家居装修',
                        'name_en' => 'Home Improvement',
                    ],
                    [
                        'key' => 'PET_RETAIL',
                        'name_cn' => '宠物零售',
                        'name_en' => 'Pet Retail',
                    ],
                    [
                        'key' => 'RESTAURANT',
                        'name_cn' => '餐厅',
                        'name_en' => 'Restaurant',
                    ],
                    [
                        'key' => 'SMB_ELECTRONICS_AND_APPLIANCES',
                        'name_cn' => '电子与电器（SMB）',
                        'name_en' => 'Electronics & Appliances (SMB)',
                    ],
                    [
                        'key' => 'SMB_RENTALS',
                        'name_cn' => '租赁（SMB）',
                        'name_en' => 'Rentals (SMB)',
                    ],
                    [
                        'key' => 'SPORTING',
                        'name_cn' => '运动的',
                        'name_en' => 'Sporting',
                    ],
                    [
                        'key' => 'TOY_AND_HOBBY',
                        'name_cn' => '玩具和爱好',
                        'name_en' => 'Toy & Hobby',
                    ],
                ]
            ],
            [
                'key' => 'TECHNOLOGY',
                'parent_key' => '',
                'name_cn' => '技术',
                'name_en' => 'Technology',
                'children' => [
                    [
                        'key' => 'B2B',
                        'name_cn' => 'B2B',
                        'name_en' => 'B2B',
                    ],
                    [
                        'key' => 'COMPUTING_AND_PERIPHERALS',
                        'name_cn' => '计算机和外围设备',
                        'name_en' => 'Computing & Peripherals',
                    ],
                    [
                        'key' => 'CONSUMER_ELECTRONICS',
                        'name_cn' => '消费类电子产品',
                        'name_en' => 'Consumer Electronics',
                    ],
                    [
                        'key' => 'CONSUMER_TECH',
                        'name_cn' => '消费者技术',
                        'name_en' => 'Consumer Tech',
                    ],
                    [
                        'key' => 'DESKTOP_SOFTWARE',
                        'name_cn' => '桌面软件',
                        'name_en' => 'Desktop Software',
                    ],
                    [
                        'key' => 'MOBILE_APPS',
                        'name_cn' => '移动应用',
                        'name_en' => 'Mobile Apps',
                    ],
                    [
                        'key' => 'SMB_CONSUMER_MOBILE_DEVICE',
                        'name_cn' => '消费者移动设备（SMB）',
                        'name_en' => 'Consumer Mobile Device (SMB)',
                    ],
                    [
                        'key' => 'SMB_NAVIGATION_AND_MEASUREMENT',
                        'name_cn' => '导航与测量（SMB）',
                        'name_en' => 'Navigation & Measurement (SMB)',
                    ],
                ]
            ],
            [
                'key' => 'TELECOM',
                'parent_key' => '',
                'name_cn' => '电信',
                'name_en' => 'Telecom',
                'children' => [
                    [
                        'key' => 'CABLE_AND_SATELLITE',
                        'name_cn' => '有线和卫星',
                        'name_en' => 'Cable & Satellite',
                    ],
                    [
                        'key' => 'OTHER_WIRELINE_SERVICES',
                        'name_cn' => '其他有线服务',
                        'name_en' => 'Other Wireless Services',
                    ],
                    [
                        'key' => 'WIRELESS_SERVICES',
                        'name_cn' => '无线服务',
                        'name_en' => 'Wireless Services',
                    ],
                ]
            ],
            [
                'key' => 'TRAVEL',
                'parent_key' => '',
                'name_cn' => '旅行',
                'name_en' => 'Travel',
                'children' => [
                    [
                        'key' => 'AIR',
                        'name_cn' => '空气',
                        'name_en' => 'Air',
                    ],
                    [
                        'key' => 'AIR_FREIGHT_OR_PACKAGE',
                        'name_cn' => '空运/包裹',
                        'name_en' => 'Air Freight/Package',
                    ],
                    [
                        'key' => 'AUTO_RENTAL',
                        'name_cn' => '汽车租赁',
                        'name_en' => 'Auto Rental',
                    ],
                    [
                        'key' => 'BUS_AND_TAXI_AND_AUTO_RETAL',
                        'name_cn' => '巴士，出租车，汽车租赁',
                        'name_en' => 'Bus, Taxi, Auto Rental',
                    ],
                    [
                        'key' => 'CRUISES_AND_MARINE',
                        'name_cn' => '游轮和海洋',
                        'name_en' => 'Cruises & Marine',
                    ],
                    [
                        'key' => 'CVB_CONVENTION_AND_VISITORS_BUREAU',
                        'name_cn' => '会议及旅游局（CVB）',
                        'name_en' => 'Convention & Visitors Bureau (CVB)',
                    ],
                    [
                        'key' => 'HIGHWAYS',
                        'name_cn' => '公路',
                        'name_en' => 'Highways',
                    ],
                    [
                        'key' => 'HOTEL_AND_ACCOMODATION',
                        'name_cn' => '酒店和住宿',
                        'name_en' => 'Hotel & Accomodation',
                    ],
                    [
                        'key' => 'RAILROADS',
                        'name_cn' => '铁路',
                        'name_en' => 'Railroads',
                    ],
                    [
                        'key' => 'SMB_OPERATIONS_AND_OTHER',
                        'name_cn' => '运营及其他（SMB）',
                        'name_en' => 'Operations & Other (SMB)',
                    ],
                    [
                        'key' => 'TRAVAL_AGENCY',
                        'name_cn' => '旅行社',
                        'name_en' => 'Travel Agency',
                    ],
                    [
                        'key' => 'TRUCK_AND_MOVING',
                        'name_cn' => '卡车和移动',
                        'name_en' => 'Truck & Moving',
                    ],

                ]
            ],
        ];
        foreach($verticals as $vertical){
            $vertical['status'] = 1;
            $vertical['level'] = 0;
            $vertical['parent_key'] = '';
            $children = $vertical['children'] ?? [];
            unset($vertical['children']);
            DB::table('t_facebook_vertical')->insert($vertical);
            if($children) foreach($children as $sub_vertical){
                $sub_vertical['level'] = 1;
                $sub_vertical['status'] = 1;
                $sub_vertical['parent_key'] = $vertical['key'];
                DB::table('t_facebook_vertical')->insert($sub_vertical);
            }

        }
    }
}
