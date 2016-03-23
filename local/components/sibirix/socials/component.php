<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

$arParams["CACHE_TIME"] = 36000000;

if ($this->StartResultCache(false, array($arParams))) {
    if (!CModule::IncludeModule("iblock")) {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }

    $sections = IBlockSectionSelect::instance(IB_SETTINGS)
        ->where(array('CODE' => $arParams['CODE']))
        ->fetch();
    $section = $sections[0];

    $arResult["ITEMS"] = IBlockWrap::instance(IB_SETTINGS)
        ->cache(0)
        ->orderBy(array('SORT' => 'ASC'))
        ->select(array('CODE', 'PROPERTY_VALUE', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'ACTIVE_FROM'))
        ->where(array('SECTION_ID' => $section['ID']))
        ->fetch();

    $this->IncludeComponentTemplate();
}
