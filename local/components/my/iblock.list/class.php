<?php

use Bitrix\Iblock\Component\Tools;
use Bitrix\Iblock\ElementTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class MyComponent extends \CBitrixComponent
{
    /**
     * @param array $arParams
     *
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams["IBLOCK_ID"] ??= 0;

        return $arParams;
    }

    /**
     * 
     * @return void
     */
    public function executeComponent()
    {
        $arExternalFilter = [];
        if (!empty($this->arParams["FILTER_NAME"])) {
            global ${$this->arParams["FILTER_NAME"]};

            if (is_array(${$this->arParams["FILTER_NAME"]})) {
                $arExternalFilter = ${$this->arParams["FILTER_NAME"]};
            }
        }

        if ($this->startResultCache(false, $arExternalFilter)) {
            $this->initResult();

            if (empty($this->arResult["ITEMS"])) {
                $this->abortResultCache();
                ShowError("Инфоблоки пустые");

                return;
            }

            $this->includeComponentTemplate();
        }
    }

    /**
     * 
     * @return void
     */
    private function initResult() 
    {
        $arFilters = [
            "ACTIVE" => "Y"
        ];

        if ($this->arParams["IBLOCK_ID"] < 1) {
            $arFilters["IBLOCK_TYPE"] = $this->arParams["IBLOCK_TYPE"];
        } else {
            $arFilters["IBLOCK_ID"] = $this->arParams["IBLOCK_ID"];
        }

        if (!empty($this->arParams["FILTER_NAME"])) {
            global ${$this->arParams["FILTER_NAME"]};

            if (is_array(${$this->arParams["FILTER_NAME"]})) {
                $arFilters = array_merge($arFilters, ${$this->arParams["FILTER_NAME"]});
            }
        }

        $arElements = CIBlockElement::GetList(
            [],
            $arFilters,
            false,
            false,
            [
                "ID",
                "IBLOCK_ID",
                "IBLOCK_SECTION_ID",
                "NAME",
                "ACTIVE_FROM",
                "TIMESTAMP_X",
                "DETAIL_PAGE_URL",
                "LIST_PAGE_URL",
                "DETAIL_TEXT",
                "DETAIL_TEXT_TYPE",
                "PREVIEW_TEXT",
                "PREVIEW_TEXT_TYPE",
                "PREVIEW_PICTURE",
            ]);
        $this->arResult["ITEMS"] = [];
        while ($el = $arElements->GetNext()) {
            Tools::getFieldImageData(
                $el,
                array("PREVIEW_PICTURE"),
                Tools::IPROPERTY_ENTITY_ELEMENT
            );

            $this->arResult["ITEMS"][$el["IBLOCK_ID"]][] = $el;
        }
    }
}