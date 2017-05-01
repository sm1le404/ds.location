<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu[] = array(
	'parent_menu' => 'global_menu_content',
	'sort'        => 400,
	'text'        => Loc::getMessage('LOCATION_MENU_REF'),
	'title'       => Loc::getMessage('LOCATION_MENU_REF'),
	'url'         => 'location_index.php',
	'items_id'    => 'menu_references',
	'items'       => array(
		array(
            'text'     => Loc::getMessage('LOCATION_MENU_COUNTRY'),
            'url'      => 'location_index.php?ref=CountryTable&lang=' . LANGUAGE_ID,
            'more_url' => array('location_index.php?ref=CountryTable&lang=' . LANGUAGE_ID),
            'title'    => Loc::getMessage('LOCATION_MENU_COUNTRY'),
        ),
        array(
            'text'     => Loc::getMessage('LOCATION_MENU_REGION'),
            'url'      => 'location_index.php?ref=RegionTable&lang=' . LANGUAGE_ID,
            'more_url' => array('location_index.php?ref=RegionTable&lang=' . LANGUAGE_ID),
            'title'    => Loc::getMessage('LOCATION_MENU_REGION'),
        ),
        array(
            'text'     => Loc::getMessage('LOCATION_MENU_CITY'),
            'url'      => 'location_index.php?ref=CityTable&lang=' . LANGUAGE_ID,
            'more_url' => array('location_index.php?ref=CityTable&lang=' . LANGUAGE_ID),
            'title'    => Loc::getMessage('LOCATION_MENU_CITY'),
        ),
	)
);

return $aMenu;

?>