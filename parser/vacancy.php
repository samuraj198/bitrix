<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!$USER->IsAdmin()) {
	LocalRedirect("/");
}

\Bitrix\Main\Loader::includeModule('iblock');
$row = 1;
$IBLOCK_ID = 8;

$el = new CIBlockElement;
$arProps = [];

$rsProps = CIBlockPropertyEnum::getList(
	["SORT" => "ASC", "VALUE" => "ASC"],
	["IBLOCK_ID" => $IBLOCK_ID]
);
while ($prop = $rsProps->Fetch()) {
	$key = trim($prop["VALUE"]);
	if ($prop["PROPERTY_CODE"] == "OFFICE") {
		$key = mb_strtolower($key);
	}
	$arProps[$prop["PROPERTY_CODE"]][$key] = $prop["ID"];
}

$rsElements = CIBlockElement::getList([], ["IBLOCK_ID" => $IBLOCK_ID], false, false, ["ID"]);
while ($element = $rsElements->GetNext()) {
	CIBlockElement::Delete($element["ID"]);
}

if (($file = fopen("vacancy.csv", "r")) !== false) {
	while (($data = fgetcsv($file, 0, ",")) !== false) {
		if ($row === 1) {
			$row++;
			continue;
		}

		$PROP["ACTIVITY"] = $data[9];
		$PROP["FIELD"] = $data[11];
		$PROP["OFFICE"] = $data[1];
		$PROP["LOCATION"] = $data[2];
		$PROP["REQUIRE"] = $data[4];
		$PROP["DUTY"] = $data[5];
		$PROP["CONDITIONS"] = $data[6];
		$PROP["EMAIL"] = $data[12];
		$PROP["DATE"] = date("d.m.Y");
		$PROP["TYPE"] = $data[8];
		$PROP["SALARY_TYPE"] = '';
		$PROP["SALARY_VALUE"] = $data[7];
		$PROP["SCHEDULE"] = $data[10];

		foreach ($PROP as $key => &$value) {
			$value = trim($value);
			$value = str_replace("\n", "", $value);
			if (stripos($value, "•") !== false) {
				$value = explode("•", $value);
				array_splice($value, 0, 1);
				foreach ($value as &$str) {
					$str = trim($str);
				}
			} elseif ($arProps[$key]) {
				if ($key == "OFFICE") {
					$value = mb_strtolower($value);
				}

				if (array_key_exists($value, $arProps[$key])) {
					$value = $arProps[$key][$value];
				} else {
					$CIBlockProperty = new CIBlockProperty;
					$idProperty = $CIBlockProperty->GetList(
						[],
						[
							"IBLOCK_ID" => $IBLOCK_ID,
							"CODE" => $key 
						]
					)->Fetch();
					$idProperty = $idProperty["ID"];

					$CIBlockPropertyEnum = new CIBlockPropertyEnum;
					$arFields = [
						"PROPERTY_ID" => $idProperty,
						"VALUE" => $value,
						"SORT" => 500
					];
					$ID = $CIBlockPropertyEnum->Add($arFields);
					$arProps[$key][$value] = $ID;
					$value = $ID;
				}
			}
		}

		if ($PROP["SALARY_VALUE"] == "-" || $PROP["SALARY_VALUE"] == "") {
			$PROP["SALARY_VALUE"] = "";
		} elseif ($PROP["SALARY_VALUE"] == "по договоренности") {
			$PROP["SALARY_VALUE"] = "";
			$PROP["SALARY_TYPE"] = $arProps["SALARY_TYPE"]["договорная"];
		} else {
			$arSalary = explode(' ', $PROP["SALARY_VALUE"]);
			if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
				$PROP["SALARY_TYPE"] = $arProps["SALARY_TYPE"][mb_strtoupper($arSalary[0])];
				array_splice($arSalary, 0, 1);
				$PROP["SALARY_VALUE"] = implode(' ', $arSalary);
			} else {
				$PROP["SALARY_TYPE"] = $arProps["SALARY_TYPE"]["="];
			}
		}

		$arLoadProductArray = [
			"MODIFIED_BY" => $USER->GetID(),
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $IBLOCK_ID,
			"PROPERTY_VALUES" => $PROP,
			"NAME" => $data[3],
			"ACTIVE" => end($data) ? "Y" : "N"
		];

		if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			echo "Добавлен элемент с ID: " . $PRODUCT_ID . "<br>";
		} else {
			echo "Error: " . $el->LAST_ERROR . "<br>";
		}
	}
	fclose($file);
}
