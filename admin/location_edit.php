<?php

use Bitrix\Main\Localization\Loc;

define('ADMIN_MODULE_NAME', 'ds.geolocation');

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
GLOBAL $USER;
Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . '/menu.php');

if (!CModule::IncludeModule(ADMIN_MODULE_NAME)) {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$refClass = '\Ds\Geolocation\Tables\\'.$_REQUEST['ref'];

if (!class_exists($refClass) || !is_subclass_of($refClass, '\Bitrix\Main\Entity\DataManager')) {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$tabControl = new CAdminTabControl('tabControl', array(
	array(
		'TAB'   => Loc::getMessage('SEOTAG_TAB'),
		'TITLE' => Loc::getMessage('SEOTAG_MENU_' . strtoupper($_REQUEST['ref']))
	)
));

$isNew = true;
if (!empty($_REQUEST['ID']) && $_REQUEST['ID'] > 0) {
	$rsRow = $refClass::getById($_REQUEST['ID']);
	$arRow = $rsRow->fetch();
	if ($arRow['ID'] == $_REQUEST['ID']) {
		$isNew = false;
	}
}

if ((!empty($save) || !empty($apply)) && $REQUEST_METHOD == "POST" && check_bitrix_sessid()) {
	$arFields = array();
	$errors = array();

	if (!$isNew && $_REQUEST['IMAGE_del'] == 'Y') {
		CFile::DoDelete($arRow['IMAGE']);
		$arFields['IMAGE'] = 0;
	}

	if (array_key_exists('IMAGE', $_FILES) && $_FILES['IMAGE']['error'] !== UPLOAD_ERR_NO_FILE) {
		$_FILES['IMAGE']["MODULE_ID"] = ADMIN_MODULE_NAME;
		if (
			CFile::ResizeImage(
				$_FILES['IMAGE'],
				array(
					'width'  => COption::GetOptionString(ADMIN_MODULE_NAME, "max_image_size"),
					'height' => COption::GetOptionString(ADMIN_MODULE_NAME, "max_image_size")
				)
			)
		) {
			if (!$isNew && $arRow['IMAGE'] > 0) {
				CFile::DoDelete($arRow['IMAGE']);
			}
			$arFields['IMAGE'] = CFile::SaveFile($_FILES['IMAGE'], ADMIN_MODULE_NAME);
		} else {
			$errors[] = Loc::getMessage('SEOTAG_IMAGE_ERROR');
		}
	}

	$map = $refClass::getMap();
	foreach ($_REQUEST as $field => $value) {
		if (array_key_exists($field, $map)) {
			$arFields[$field] = $value;
		}
	}

	if ($isNew) {
		$result = $refClass::add($arFields);
		$arRow['ID'] = $result->getId();
	} else {
		$result = $refClass::update($_REQUEST["ID"], $arFields);
	}

	$arRow = array_merge($arRow, $arFields);

	if ($result->isSuccess() && !$errors) {
		$isNew = false;
		if (!empty($save)) {
			LocalRedirect('location_index.php?&lang=' . LANGUAGE_ID.'&ref='.$_REQUEST['ref']);
		} else {
			LocalRedirect(
				'location_index.php?&lang=' . LANGUAGE_ID. '&ref='.$_REQUEST['ref']
			);
		}
	} else {
		$errors = array_merge($result->getErrorMessages(), $errors);
	}
}

$APPLICATION->SetTitle(Loc::getMessage($isNew ? 'SEOTAG_ADD_PAGE' : 'SEOTAG_EDIT_PAGE'));

$context = new CAdminContextMenu(array(
	array(
		'TEXT'  => Loc::getMessage('SEOTAG_RETURN_TO_LIST_BUTTON'),
		'TITLE' => Loc::getMessage('SEOTAG_RETURN_TO_LIST_BUTTON'),
		'LINK'  => 'location_index.php?lang=' . LANGUAGE_ID . '&ref='. $_REQUEST['ref'],
		'ICON'  => 'btn_list',
	)
));

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$context->Show();

if (!empty($errors)) {
	CAdminMessage::ShowMessage(implode("\n", $errors));
}

?>

<form method="POST" action="<?= $APPLICATION->GetCurUri() ?>" enctype="multipart/form-data">
	<?= bitrix_sessid_post() ?>
	<? if (!$isNew): ?>
		<input type="hidden" name="ID" value="<?= htmlspecialcharsbx($arRow['ID']) ?>">
	<? endif ?>
	<input type="hidden" name="ref" value="<?= htmlspecialcharsbx($_REQUEST['ref']) ?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	$map = $refClass::getMap();
	?>
	<? foreach ($map as $field => $params): ?>
		<? if ((!isset($params['primary']) || !$params['primary']) && !isset($params['reference'])): ?>
			<tr>
				<td width="40%"><?= htmlspecialcharsbx($params['title']) ?></td>
				<td>
					<? if ($params['data_type'] == 'text'): ?>
                        <textarea name="<?= htmlspecialcharsbx($field) ?>" rows="10"><?= ($isNew ? '' : htmlspecialcharsbx($arRow[$field])) ?></textarea>
					<? else: ?>
						<input type="text" name="<?= htmlspecialcharsbx($field) ?>" size="20"
							   value="<?= ($isNew ? '' : htmlspecialcharsbx($arRow[$field])) ?>"/>
					<? endif ?>
				</td>
			</tr>
		<? endif ?>
	<? endforeach ?>
	<?
	$tabControl->Buttons(array('back_url' => 'location_index.php?ref=' . $_REQUEST['ref'] . '&lang=' . LANGUAGE_ID));
	$tabControl->End();
	?>
</form>