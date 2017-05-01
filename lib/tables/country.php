<?php

namespace Ds\Geolocation\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CountryTable
 *
 * @package Ds\Geolocation\Tables
 **/
class CountryTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ds_location_countries';
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
		);
	}
}