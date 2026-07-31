<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Курс валют");
?><?$APPLICATION->IncludeComponent(
	"bitrix:currency.rates",
	"",
	Array(
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CURRENCY_BASE" => "RUB",
		"RATE_DAY" => "",
		"SHOW_CB" => "Y",
		"arrCURRENCY_FROM" => array("USD","EUR")
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>