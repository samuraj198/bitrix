<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MY_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MY_COMPONENT_DESCRIPTION"),
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "my",
			"NAME" => GetMessage("MY_COMPONENT_PATH_NAME"),
			"SORT" => 10,
		),
	),
);

?>