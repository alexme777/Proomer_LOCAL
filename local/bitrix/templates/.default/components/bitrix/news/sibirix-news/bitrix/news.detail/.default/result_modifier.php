<?
/**
 * @var $arParams array
 * @var $arResult array
 */

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if (strlen($arParams["SORT_BY1"]) <= 0) $arParams["SORT_BY1"] = "ACTIVE_FROM";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"])) {
    $arParams["SORT_ORDER1"] = "DESC";
}

if (strlen($arParams["SORT_BY2"]) <= 0) $arParams["SORT_BY2"] = "SORT";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"])) {
    $arParams["SORT_ORDER2"] = "ASC";
}

$arSort = array(
    $arParams["SORT_BY1"] => $arParams["SORT_ORDER1"],
    $arParams["SORT_BY2"] => $arParams["SORT_ORDER2"],
);

$cIBlockElement = new CIBlockElement();
$rsElement = $cIBlockElement->GetList(
    $arSort,
    array('IBLOCK_ID' => $arResult['IBLOCK_ID']),
    false,
    array('nPageSize' => 1, 'nElementID' => $arResult['ID']),
    array('ID', 'NAME', 'DETAIL_PAGE_URL')
);
$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);

$neighbours = array();
while ($nei = $rsElement->GetNext()) {
    $neighbours[] = $nei;
}

if (count($neighbours) == 3) {
    $arResult['PREV'] = $neighbours[0];
    $arResult['NEXT'] = $neighbours[2];
} elseif (count($neighbours) == 2) {
    if ($neighbours[0]['ID'] == $arResult['ID']) {
        $arResult['NEXT'] = $neighbours[1];
    } else {
        $arResult['PREV'] = $neighbours[0];
    }
}
