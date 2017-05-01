<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (class_exists('ds_geolocation'))
{
	return;
}

class ds_geolocation extends CModule
{
	public $MODULE_ID = 'ds.geolocation';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	public function __construct()
	{
		$arModuleVersion = array();

		include __DIR__ . '/version.php';

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('SEOTAG_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('SEOTAG_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = Loc::getMessage('SEOTAG_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('SEOTAG_PARTNER_URI');
	}

    public function InstallFiles($arParams = array())
    {
        CopyDirFiles(__DIR__ . '/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
        return true;
    }

    public function UnInstallFiles(array $arParams = array())
    {
        DeleteDirFiles(__DIR__ . '/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
        if (!array_key_exists('savedata', $arParams) || $arParams['savedata'] !== 'Y') {
            DeleteDirFilesEx('upload/' . $this->MODULE_ID);
        }
        return true;
    }

	public function DoInstall()
	{
		global $APPLICATION;
		$this->InstallDB();
        $this->InstallFiles();
		$APPLICATION->IncludeAdminFile(Loc::getMessage('SEOTAG_INSTALL_TITLE'), __DIR__ . '/step1.php');
	}

	public function InstallDB()
	{
		global $APPLICATION, $DB;
		$errors = $DB->RunSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/install.sql');
		if ($errors)
		{
			$APPLICATION->ThrowException(implode('<br>', (array)$errors));
			return false;
		}
        /*$errors = $DB->RunSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/ds_location_countries.sql');
        if ($errors)
        {
            $APPLICATION->ThrowException(implode('<br>', (array)$errors));
            return false;
        }
        $errors = $DB->RunSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/ds_location_regions.sql');
        if ($errors)
        {
            $APPLICATION->ThrowException(implode('<br>', (array)$errors));
            return false;
        }*/

		RegisterModule($this->MODULE_ID);

		return true;
	}

	public function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallDB(array('savedata' => $_REQUEST['savedata']));
        $this->UnInstallFiles(array('savedata' => $_REQUEST['savedata']));
		$APPLICATION->IncludeAdminFile(Loc::getMessage('SEOTAG_UNINSTALL_TITLE'), __DIR__ . '/unstep1.php');
	}

	public function UnInstallDB(array $arParams = array())
	{
		global $APPLICATION, $DB;
		if (!array_key_exists('savedata', $arParams) || $arParams['savedata'] !== 'Y')
		{
			$errors = $DB->RunSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/uninstall.sql');
			if ($errors)
			{
				$APPLICATION->ThrowException(implode('<br>', (array)$errors));
				return false;
			}
		}

		UnRegisterModule($this->MODULE_ID);

		return true;
	}
}