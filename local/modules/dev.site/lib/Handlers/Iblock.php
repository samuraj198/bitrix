<?php

namespace Dev\Site\Handlers;

use CIBlock;
use CIBlockElement;
use CIBlockSection;

class Iblock
{
    public static function addLog(&$arFields)
    {
		// echo ''; print_r($arFields); echo ''; die(); Вывод данных в $arFields
        $CIBlockSection = new CIBlockSection;

		$IBlockID = $arFields["IBLOCK_ID"];
		$IBlock = CIBlock::GetByID($IBlockID)->Fetch();
        $IBlockCode = $IBlock["CODE"];

        if ($IBlockCode == "LOG") {
            return;
        }

        $IBlockLog = CIBlock::GetList(
            [], 
            [
                "CODE" => "LOG"
            ]
        )->Fetch();
        
        if (!$IBlockLog) {
            return;
        }

        $checkSections = $CIBlockSection->GetList(
            [], 
            [
                "IBLOCK_ID" => $IBlockLog["ID"],
                "CODE" => $IBlock["CODE"]
            ],
            false,
            [
                "ID"
            ]
        )->Fetch();

        if (!$checkSections) {
            $sectionID = $CIBlockSection->Add([
                "IBLOCK_ID" => $IBlockLog["ID"],
                "NAME" => $IBlock["NAME"],
                "CODE" => $IBlock["CODE"],
                "ACTIVE" => "Y"
            ]);
        } else {
            $sectionID = $checkSections["ID"];
        }

        $currentElement = CIBlockElement::GetByID($arFields["ID"])->Fetch();

        if ($currentElement["IBLOCK_SECTION_ID"] == 0) {
            $breadCrumbs = "$arFields[NAME]";
        } else {
            $breadCrumbs = self::getBreadCrumbs($currentElement, "$arFields[NAME]");
        }

        $CIBlockElement = new CIBlockElement;

        $CIBlockElement->Add([
            "IBLOCK_ID" => $IBlockLog["ID"],
            "IBLOCK_SECTION_ID" => $sectionID,
            "NAME" => $arFields["ID"],
            "ACTIVE_FROM" => date("d.m.Y H:i:s"),
            "PREVIEW_TEXT" => "$IBlock[NAME] -> $breadCrumbs"
        ]);
    }

    private static function getBreadCrumbs($element, $breadCrumbs = "")
    {
        $section = CIBlockSection::GetByID($element["IBLOCK_SECTION_ID"])->Fetch();

        $breadCrumbs = "$section[NAME] -> " . $breadCrumbs;

        if ($section["IBLOCK_SECTION_ID"] > 0) {
            return self::getBreadCrumbs($section, $breadCrumbs);
        }

        return $breadCrumbs;
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;
        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));
        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];
        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }
        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality);
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');
            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();
                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ], false, false, ['ID'])
                                ->Fetch();
                            if ($arOtherElement) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arOtherElement['ID'], $OTHER_IBLOCK_ID, $arFiles, 'FILE');
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }
                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => \Only\Site\Helpers\IBlock::getIblockID('PRODUCTS', 'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'),
                                    'ID' => $elementIds
                                ], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
                            while ($arElement = $rsElement->Fetch()) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arElement['ID'], $arElement['IBLOCK_ID'], $arFiles, 'FILE');
                            }
                        }
                    }
                }
            }
        }
    }

}
