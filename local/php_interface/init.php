<?php

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

if (Loader::includeModule("dev.site")) {
	$eventManager = EventManager::getInstance();

	$eventManager->addEventHandler(
		"iblock",
		"OnAfterIBlockElementAdd",
		["\\Dev\\Site\\Handlers\\Iblock", "addLog"]
	);

	$eventManager->addEventHandler(
		"iblock",
		"OnAfterIBlockElementUpdate",
		["\\Dev\\Site\\Handlers\\Iblock", "addLog"]
	);
}