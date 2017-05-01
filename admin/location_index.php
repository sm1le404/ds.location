<?php

use Bitrix\Main\Localization\Loc;

define('ADMIN_MODULE_NAME', 'ds.geolocation');

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
GLOBAL $USER;
Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . '/menu.php');

if (!CModule::IncludeModule(ADMIN_MODULE_NAME) || !CModule::IncludeModule('fileman')) {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$refClass = '\Ds\Geolocation\Tables\\'.$_REQUEST['ref'];

if (!class_exists($refClass) || !is_subclass_of($refClass, '\Bitrix\Main\Entity\DataManager')) {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

CJSCore::Init('file_input');

$APPLICATION->SetTitle(Loc::getMessage('SEOTAG_MENU_' . strtoupper($_REQUEST['ref'])));

$sTableID = $refClass::getTableName();

$lAdmin = new CAdminList($sTableID, new CAdminSorting($sTableID, 'ID', 'DESC'));
$lAdmin->bMultipart = true;

$lAdmin->AddAdminContextMenu(
    array(
        array(
            'TEXT'  => Loc::getMessage('SEOTAG_ADD_BUTTON'),
            'LINK'  => 'location_edit.php?lang=' . LANGUAGE_ID.'&ref='.$_REQUEST['ref'],
            'TITLE' => Loc::getMessage('SEOTAG_ADD_BUTTON'),
            'ICON'  => 'btn_new',
        )
    )
);

$arHeaders = array();
$arMap = $refClass::getMap();
foreach ($arMap as $field => $params) {
    $arHeaders[] = array(
        'id'      => $field,
        'content' => $params['title'],
        'sort'    => $field,
        'default' => true
    );
}

$lAdmin->AddHeaders($arHeaders);

if (!empty($_REQUEST['action']) && !empty($_REQUEST['ID']) && $_REQUEST['action'] == 'delete' && $_REQUEST['ID'] > 0) {
	$result = $refClass::delete($_REQUEST['ID']);
	if (!$result->isSuccess()) {
		$lAdmin->AddGroupError(
			Loc::getMessage('SEOTAG_DELETE_ERROR') .
			' (ID = ' . $_REQUEST['ID'] . ': ' . implode('<br>', $result->getErrorMessages()) . ')', $_REQUEST['ID']
		);
	}
}


if ($lAdmin->EditAction()) {
	if (is_array($_FILES['FIELDS'])) {
		CAllFile::ConvertFilesToPost($_FILES['FIELDS'], $_REQUEST['FIELDS']);
	}

	foreach ($_REQUEST['FIELDS'] as $ID => $arFields) {
		if ($lAdmin->IsUpdated($ID)) {
			$fields = $arFields;
			unset($fields['IMAGE']);
			$fields = array_map('trim', $fields);
			$DB->StartTransaction();
			if ($_REQUEST['FIELDS_OLD'][$ID]['IMAGE'] > 0 && $_REQUEST["FIELDS_del"][$ID]['IMAGE'] == 'Y') {
				CFile::DoDelete($_REQUEST['FIELDS_OLD'][$ID]['IMAGE']);
				$fields['IMAGE'] = 0;
			}

			if (array_key_exists('IMAGE', $arFields) && $arFields['IMAGE']['error'] !== UPLOAD_ERR_NO_FILE) {
				$image = $arFields['IMAGE'];
				$image["MODULE_ID"] = ADMIN_MODULE_NAME;
				if (
					CFile::ResizeImage(
						$image,
						array(
							'width'  => COption::GetOptionString(ADMIN_MODULE_NAME, "max_image_size"),
							'height' => COption::GetOptionString(ADMIN_MODULE_NAME, "max_image_size")
						)
					)
				) {
					if ($_REQUEST['FIELDS_OLD'][$ID]['IMAGE'] > 0) {
						CFile::DoDelete($_REQUEST['FIELDS_OLD'][$ID]['IMAGE']);
					}
					$fields['IMAGE'] = CFile::SaveFile($image, ADMIN_MODULE_NAME);
				} else {
					$lAdmin->AddGroupError(Loc::getMessage('SEOTAG_IMAGE_ERROR'));
				}
			}
			$result = $refClass::update($ID, $fields);
			if (!$result->isSuccess()) {
				$DB->Rollback();
				$lAdmin->AddGroupError(
					Loc::getMessage('SEOTAG_SAVE_ERROR') .
					' (ID = ' . $ID . ': ' . implode('<br>', $result->getErrorMessages()) . ')',
					$ID
				);
			}
			$DB->Commit();
		}
	}
}

if ($arID = $lAdmin->GroupAction()) {
	if ($_REQUEST['action_target'] == 'selected') {
		$rsData = $refClass::getList(array('select' => array('ID')));
		$arID = array();
		while ($arRes = $rsData->fetch()) {
			$arID[] = $arRes['ID'];
		}
	}

	if ($_REQUEST['action'] === 'delete' && is_array($arID) && $arID) {
		$DB->StartTransaction();
		foreach ($arID as $ID) {
			$result = $refClass::delete($ID);
			if (!$result->isSuccess()) {
				$DB->Rollback();
				$lAdmin->AddGroupError(
					Loc::getMessage('SEOTAG_DELETE_ERROR') .
					' (ID = ' . $ID . ': ' . implode('<br>', $result->getErrorMessages()) . ')',
					$ID
				);
			}
		}
		$DB->Commit();
	}
}

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

if (!in_array($by, $arVisibleColumns)) {
	$by = 'ID';
	$order = 'ASC';
}

//Filter
$arFilter = array();
if ($_REQUEST['set_filter'] == 'Y')
{
    foreach ($arMap as $code => $map)
    {
        if ($map['data_type'] == 'datetime')
        {
            if (!empty($_REQUEST['FILTER_'.$code.'_FROM']))
            {
                $arFilter['>='.$code] = $_REQUEST['FILTER_'.$code.'_FROM'].' 00:00:00';
            }
            if (!empty($_REQUEST['FILTER_'.$code.'_TO']))
            {
                $arFilter['<='.$code] = $_REQUEST['FILTER_'.$code.'_TO'].' 23:59:59';
            }
        }
        else
        {
            $arFilter[$code] = $_REQUEST['FILTER_'.$code];
        }
    }
    $arFilter = array_filter($arFilter);
}

$findLink = array_search('LINK',$arVisibleColumns);
if ($findLink !== false)
{
    unset($arVisibleColumns[$findLink]);
}
$rsData = $refClass::getList(array(
    'select' => $arVisibleColumns,
    'order' => array($by => strtoupper($order)),
    'filter' => $arFilter
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage('SEOTAG_MENU_TITLE')));
while ($arRes = $rsData->NavNext(false)) {

	$row = $lAdmin->AddRow($arRes['ID'], $arRes);

	foreach ($arRes as $field => $value) {
		if ($field == 'IMAGE') {
			$row->AddFileField(
				"IMAGE",
				array(
					"IMAGE"       => "Y",
					"PATH"        => "Y",
					"FILE_SIZE"   => "Y",
					"DIMENSIONS"  => "Y",
					"IMAGE_POPUP" => "Y",
					"MAX_SIZE" => array(
						"W" => 100,
						"H" => 100
					)
				),
				array(
					'upload'      => true,
					'medialib'    => true,
					'file_dialog' => true,
					'cloud'       => true,
					'del'         => true,
					'description' => false,
				)
			);
		} elseif ($field != 'ID') {
			$row->AddInputField($field);
		}
	}


    $row->AddActions(
        array(
            array(
                'ICON'   => 'edit',
                'TEXT'   => GetMessage('MAIN_ADMIN_MENU_EDIT'),
                'ACTION' => $lAdmin->ActionRedirect(
                    'location_edit.php?ID=' . $arRes['ID'] .'&lang=' . LANGUAGE_ID . '&ref=' . $_REQUEST['ref']
                )
            ),
            array(
                'ICON'   => 'delete',
                'TEXT'   => GetMessage('MAIN_ADMIN_MENU_DELETE'),
                'ACTION' =>
                    'if(confirm("' . GetMessageJS('SEOTAG_DELETE_CONFIRM') . '")) ' .
                    $lAdmin->ActionRedirect(
                        'location_index.php?action=delete&ID=' . $arRes['ID'] . '&lang=' . LANGUAGE_ID . '&ref='. $_REQUEST['ref']
                    )
            )
        )
    );
}


$lAdmin->AddGroupActionTable(array('delete' => Loc::getMessage('MAIN_ADMIN_LIST_DELETE')));


$lAdmin->CheckListMode();

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
?>
<form method="GET" name="find_form" id="find_form" action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="ref" value="<?=$_REQUEST['ref']?>"/>
    <input type="hidden" name="edit" value="<?=$_REQUEST['edit']?>"/>
    <?
    $oFilter = new CAdminFilter($sTableID."_filter");
    ?><script type="text/javascript">
        var arClearHiddenFields = [];
        function applyFilter(el)
        {
            BX.adminPanel.showWait(el);
            <?=$sTableID."_filter";?>.OnSet('<?echo CUtil::JSEscape($sTableID)?>', '<?echo CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.urlencode($IBLOCK_ID).'&lang='.LANGUAGE_ID.'&')?>');
            return false;
        }

        function deleteFilter(el)
        {
            BX.adminPanel.showWait(el);
            if (0 < arClearHiddenFields.length)
            {
                for (var index = 0; index < arClearHiddenFields.length; index++)
                {
                    if (undefined != window[arClearHiddenFields[index]])
                    {
                        if ('ClearForm' in window[arClearHiddenFields[index]])
                        {
                            window[arClearHiddenFields[index]].ClearForm();
                        }
                    }
                }
            }
            <?=$sTableID."_filter"?>.OnClear('<?echo CUtil::JSEscape($sTableID)?>', '<?echo CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.urlencode($IBLOCK_ID).'&lang='.urlencode(LANGUAGE_ID).'&')?>');
            return false;
        }
    </script><?
    $oFilter->Begin();
    ?>
    <?foreach ($arMap as $code => $map):?>
        <?if ($map['data_type'] == 'datetime'):?>
            <tr>
                <td><?=$map['title']?>:</td>
                <td><?echo CalendarPeriod('FILTER_'.$code.'_FROM', htmlspecialcharsex($_REQUEST['FILTER_'.$code.'_FROM']), 'FILTER_'.$code.'_TO', htmlspecialcharsex($_REQUEST['FILTER_'.$code.'_TO']), "find_form", "Y")?></td>
            </tr>
        <?else:?>
            <tr>
                <td><?=$map['title']?>:</td>
                <td><input type="text" name="FILTER_<?=$code?>" value="<?=$_REQUEST['FILTER_'.$code]?>" size="30"></td>
            </tr>
        <?endif?>
    <?endforeach?>
    <?
    $oFilter->Buttons();
    ?>
    <span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<? echo GetMessage("admin_lib_filter_set_butt"); ?>" title="<? echo GetMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return applyFilter(this);"></span>
    <span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<? echo GetMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo GetMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="deleteFilter(this); return false;"></span>
    <?
    $oFilter->End();
    ?>
</form>
<?
$lAdmin->DisplayList();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');

?>