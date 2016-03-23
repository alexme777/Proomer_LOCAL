<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/* @var array $arParams */
/* @var array $arResult */
$this->setFrameMode(true);?>

<div class="socials">
    <? foreach ($arResult["ITEMS"] as $item) { ?>
        <? if (!empty($item['PROPERTY_VALUE_VALUE'])) {?>
            <a class="<?= $item['CODE']?>" href="<?= $item['PROPERTY_VALUE_VALUE'] ?>" title="<?= $item['NAME'] ?>" target="_blank"></a>
        <? } ?>
    <? } ?>
</div>
