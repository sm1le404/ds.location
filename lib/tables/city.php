<?php

namespace Ds\Geolocation\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CityTable
 *
 * @package Ds\Geolocation\Tables;
 **/
class CityTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ds_location_cities';
	}

	public static function getMap()
	{
        return array(
            'ID'       => array(
                'data_type'    => 'integer',
                'primary'      => true,
                'autocomplete' => true,
                'title'        => 'ID',
            ),
            'city_id'       => array(
                'data_type'    => 'integer',
                'title'        => 'city_id',
            ),
            'region_id'       => array(
                'data_type'    => 'integer',
                'title'        => 'region_id',
            ),
            'country_id'       => array(
                'data_type'    => 'integer',
                'title'        => 'country_id',
            ),
            'title_ru'      => array(
                'data_type'  => 'string',
                'title'      => 'title_ru'
            ),
            'title_ua'      => array(
                'data_type'  => 'string',
                'title'      => 'title_ua',
            ),
            'title_be'      => array(
                'data_type'  => 'string',
                'title'      => 'title_be'
            ),
            'title_en'      => array(
                'data_type'  => 'string',
                'title'      => 'title_en',
            ),
            'title_es'      => array(
                'data_type'  => 'string',
                'title'      => 'title_es'
            ),
            'title_pt'      => array(
                'data_type'  => 'string',
                'title'      => 'title_pt'
            ),
            'title_de'      => array(
                'data_type'  => 'string',
                'title'      => 'title_de'
            ),
            'title_fr'      => array(
                'data_type'  => 'string',
                'title'      => 'title_fr'
            ),
            'title_it'      => array(
                'data_type'  => 'string',
                'title'      => 'title_it'
            ),
            'title_pl'      => array(
                'data_type'  => 'string',
                'title'      => 'title_pl'
            ),
            'title_ja'      => array(
                'data_type'  => 'string',
                'title'      => 'title_ja'
            ),
            'title_lt'      => array(
                'data_type'  => 'string',
                'title'      => 'title_lt'
            ),
            'title_lv'      => array(
                'data_type'  => 'string',
                'title'      => 'title_lv'
            ),
            'title_cz'      => array(
                'data_type'  => 'string',
                'title'      => 'title_cz'
            ),
            'area_ru'      => array(
                'data_type'  => 'string',
                'title'      => 'area_ru'
            ),
            'area_ua'      => array(
                'data_type'  => 'string',
                'title'      => 'area_ua',
            ),
            'area_be'      => array(
                'data_type'  => 'string',
                'title'      => 'area_be'
            ),
            'area_en'      => array(
                'data_type'  => 'string',
                'title'      => 'area_en',
            ),
            'area_es'      => array(
                'data_type'  => 'string',
                'title'      => 'area_es'
            ),
            'area_pt'      => array(
                'data_type'  => 'string',
                'title'      => 'area_pt'
            ),
            'area_de'      => array(
                'data_type'  => 'string',
                'title'      => 'area_de'
            ),
            'area_fr'      => array(
                'data_type'  => 'string',
                'title'      => 'area_fr'
            ),
            'area_it'      => array(
                'data_type'  => 'string',
                'title'      => 'area_it'
            ),
            'area_pl'      => array(
                'data_type'  => 'string',
                'title'      => 'area_pl'
            ),
            'area_ja'      => array(
                'data_type'  => 'string',
                'title'      => 'area_ja'
            ),
            'area_lt'      => array(
                'data_type'  => 'string',
                'title'      => 'area_lt'
            ),
            'area_lv'      => array(
                'data_type'  => 'string',
                'title'      => 'area_lv'
            ),
            'area_cz'      => array(
                'data_type'  => 'string',
                'title'      => 'area_cz'
            ),
            'region_ru'      => array(
                'data_type'  => 'string',
                'title'      => 'region_ru'
            ),
            'region_ua'      => array(
                'data_type'  => 'string',
                'title'      => 'region_ua',
            ),
            'region_be'      => array(
                'data_type'  => 'string',
                'title'      => 'region_be'
            ),
            'region_en'      => array(
                'data_type'  => 'string',
                'title'      => 'region_en',
            ),
            'region_es'      => array(
                'data_type'  => 'string',
                'title'      => 'region_es'
            ),
            'region_pt'      => array(
                'data_type'  => 'string',
                'title'      => 'region_pt'
            ),
            'region_de'      => array(
                'data_type'  => 'string',
                'title'      => 'region_de'
            ),
            'region_fr'      => array(
                'data_type'  => 'string',
                'title'      => 'region_fr'
            ),
            'region_it'      => array(
                'data_type'  => 'string',
                'title'      => 'region_it'
            ),
            'region_pl'      => array(
                'data_type'  => 'string',
                'title'      => 'region_pl'
            ),
            'region_ja'      => array(
                'data_type'  => 'string',
                'title'      => 'region_ja'
            ),
            'region_lt'      => array(
                'data_type'  => 'string',
                'title'      => 'region_lt'
            ),
            'region_lv'      => array(
                'data_type'  => 'string',
                'title'      => 'region_lv'
            ),
            'region_cz'      => array(
                'data_type'  => 'string',
                'title'      => 'region_cz'
            ),
        );
	}
}