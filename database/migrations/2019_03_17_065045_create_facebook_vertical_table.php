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
                'name_cn' => '',
                'name_en' => 'Advertising & Marketing',
                'children' => [
                    [
                        'key' => 'PR',
                        'name_cn' => '',
                        'name_en' => 'PR',
                    ],
                    [
                        'key' => 'DIGITAL_ADVERTISING_AND_MARKETING_OR_UNTAGGED_AGENCIES',
                        'name_cn' => '',
                        'name_en' => 'Digital Advertising & Marketing/Untagged Agencies',
                    ],
                    [
                        'key' => 'MEDIA',
                        'name_cn' => '',
                        'name_en' => 'Media',
                    ],
                ]
            ],
            [
                'key' => 'AUTOMOTIVE',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Automotive',
                'children' => [
                    [
                        'key' => 'AUTO_AGENCY',
                        'name_cn' => '',
                        'name_en' => 'Auto Agency',
                    ],
                    [
                        'key' => 'AUTOMOTIVE_MANUFACTURER',
                        'name_cn' => '',
                        'name_en' => 'Automotive Manufacturer',
                    ],
                    [
                        'key' => 'DEALERSHIP',
                        'name_cn' => '',
                        'name_en' => 'Dealership',
                    ],
                    [
                        'key' => 'INDUSTRIAL_AND_FARM_VEHICLE',
                        'name_cn' => '',
                        'name_en' => 'Industrial & Farm Vehicle',
                    ],
                    [
                        'key' => 'MOTORCYCLES',
                        'name_cn' => '',
                        'name_en' => 'Motorcycles',
                    ],
                    [
                        'key' => 'PARTS_AND_SERVICE',
                        'name_cn' => '',
                        'name_en' => 'Parts & Service',
                    ],
                    [
                        'key' => 'RECREATIONAL',
                        'name_cn' => '',
                        'name_en' => 'Recreational',
                    ],
                ]
            ],[
                'key' => 'CONSUMER_PACKAGED_GOODS',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Consumer Packaged Goods',
                'children' => [
                    [
                        'key' => 'BEAUTY_AND_PERSONAL_CARE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'BEER_AND_WINE_AND_LIQUOR',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'FOOD',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HOUSEHOLD_GOODS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'OFFICE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'PET',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'PHARMACEUTICAL_OR_HEALTH',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TOBACCO',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'WATER_AND_SOFT_DRINK_AND_BAVERAGE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'ECOMMERCE',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Ecommerce',
                'children' => [
                    [
                        'key' => 'AUCTIONS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'DAILYDEALS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'ECATALOG',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_CATALOG',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'EDUCATION',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Education',
                'children' => [
                    [
                        'key' => 'ED_TECH',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'EDUCATION_RESOURCES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'ELEARNING_AND_MASSIVE_ONLINE_OPEN_COURSES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'FOR_PROFIT_COLLEGES_AND_UNIVERSITIES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'NOT_FOR_PROFIT_COLLEGES_AND_UNIVERSITIES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SCHOOL_AND_EARLY_CHILDREN_EDCATION',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TRADE_SCHOOL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'ENERGY_AND_UTILITIES',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Energy & Utilities',
                'children' => [
                    [
                        'key' => 'OIL_AND_GAS_AND_CONSUMABLE_FUEL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_ENERGY',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'UTILITIES_AND_ENERGY_EQUIPMENT_AND_SERVICES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'ENTERTAINMENT_AND_MEDIA',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Entertainment & Media',
                'children' => [
                    [
                        'key' => 'ARTS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'EVENTS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'FITNESS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'GAMBLING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'MOVIES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'MUSEUMS_AND_PARKS_AND_LIBRARIES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'MUSIC_AND_RADIO',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'PUBLISHING_INTERNET',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_AGENTS_AND_PROMOTERS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_ARTISTS_AND_PERFORMERS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_INFORMATION',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SPORTS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'STREAMING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TELEVISION',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'FINANCIAL_SERVICES',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Financial Services',
                'children' => [
                    [
                        'key' => 'CREDIT_AND_FINANCING_AND_MORTAGES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'INSURANCE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'INVESTMENT_BANK_AND_BROKERAGE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'REAL_ESTATE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'RETAIL_AND_CREDIT_UNION_AND_COMMERCIAL_BANK',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'GAMING',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Gaming',
                'children' => [
                    [
                        'key' => 'CONSOLE_DEVELOPER',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CONSOLE_DEVICE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'MOBILE_AND_SOCIAL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'ONLINE_OR_SOFTWARE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'REAL_MONEY_OR_SKILLED_GAMING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_CANVAS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_CROSS_PLATFORM',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_GAME_AND_TOY',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SOFTWARE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'GOVERMENT_AND_POLITICS',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => '',
                'children' => [
                    [
                        'key' => 'GOVERNMENT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'POLITICAL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SEASONAL_POLITICAL_SPENDERS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'ORGANIZATIONS_AND_ASSOCIATIONS',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => '',
                'children' => [
                    [
                        'key' => 'NON_PROFIT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'RELIGIOUS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_RELIGIOUS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'OTHER',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Other',
                'children' => [
                    [
                        'key' => 'ECOMMERCE_AGRICULTURE_AND_FARMING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'B2B_MANUFACTURING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CONSTRUCTION_AND_MINING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TRANSPORTATION_EQUIPMENT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'PROFESSIONAL_SERVICES',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Professional Services',
                'children' => [
                    [
                        'key' => 'ACCOUNTING_AND_TAXES_AND_LEGAL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'BUSINESS_SUPPORT_SERVICES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CAREER',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CONSULTING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'DATING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'ENGINEERING_AND_DESIGN',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'FAMILY_AND_HEALTH',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HOME_SERVICE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'PHOTOGRAPHY_AND_FILMING_SERVICES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_REPAIR_AND_MAINTENANCE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'RETAIL',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Retail',
                'children' => [
                    [
                        'key' => 'APPAREL_AND_ACCESSORIES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'BOOKSTORES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'DEPARTMENT_STORE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'FOOTWEAR',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'GROCERY_AND_DRUG_AND_CONVENIENCE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HOME_AND_OFFICE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HOME_IMPROVEMENT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'PET_RETAIL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'RESTAURANT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_ELECTRONICS_AND_APPLIANCES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_RENTALS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SPORTING',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TOY_AND_HOBBY',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'TECHNOLOGY',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Technology',
                'children' => [
                    [
                        'key' => 'B2B',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'COMPUTING_AND_PERIPHERALS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CONSUMER_ELECTRONICS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CONSUMER_TECH',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'DESKTOP_SOFTWARE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'MOBILE_APPS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_CONSUMER_MOBILE_DEVICE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_NAVIGATION_AND_MEASUREMENT',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'TELECOM',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Telecom',
                'children' => [
                    [
                        'key' => 'CABLE_AND_SATELLITE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'OTHER_WIRELINE_SERVICES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'WIRELESS_SERVICES',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                ]
            ],
            [
                'key' => 'TRAVEL',
                'parent_key' => '',
                'name_cn' => '',
                'name_en' => 'Travel',
                'children' => [
                    [
                        'key' => 'AIR',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'AIR_FREIGHT_OR_PACKAGE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'AUTO_RENTAL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'BUS_AND_TAXI_AND_AUTO_RETAL',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CRUISES_AND_MARINE',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'CVB_CONVENTION_AND_VISITORS_BUREAU',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HIGHWAYS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'HOTEL_AND_ACCOMODATION',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'RAILROADS',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'SMB_OPERATIONS_AND_OTHER',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TRAVAL_AGENCY',
                        'name_cn' => '',
                        'name_en' => '',
                    ],
                    [
                        'key' => 'TRUCK_AND_MOVING',
                        'name_cn' => '',
                        'name_en' => '',
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
