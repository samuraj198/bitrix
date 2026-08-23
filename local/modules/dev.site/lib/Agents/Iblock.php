<?php

namespace Dev\Site\Agents;

use CIBlock;
use CIBlockElement;

class Iblock
{
    public static function clearOldLogs()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $IBlockLog = \Bitrix\Iblock\IblockTable::GetList([
                'select' => ["ID"],
                'filter' => ["=CODE" => "LOG"]
            ])->Fetch();

            if (!$IBlockLog) {
                return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
            }
            $IBlockID = $IBlockLog["ID"];
            $logs = CIBlockElement::GetList(
                [
                    "ACTIVE_FROM" => "DESC",
                    "ID" => "DESC"
                ],
                [
                    "IBLOCK_ID" => $IBlockID,
                ],
                false,
                false,
                ["ID"]
            );

            $count = 0;
            while ($log = $logs->Fetch()) {
                $count++;
                if ($count <= 10) {
                    continue;
                }
                CIBlockElement::Delete($log["ID"]);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
