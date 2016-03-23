<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-list">
    <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?><br />
    <? endif; ?>

    <? foreach ($arResult["ITEMS"] as $arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <p class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <? if (is_array($arItem["PREVIEW_PICTURE"])): ?>
                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                    <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                         alt="<?= $arItem["NAME"] ?>"
                         title="<?= $arItem["NAME"] ?>" />
                </a>
            <? endif ?>
            <span class="news-date-time"><? echo $arItem["DISPLAY_ACTIVE_FROM"] ?></span>

            <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>"><b><? echo $arItem["NAME"] ?></b></a><br />
            <?= $arItem["PREVIEW_TEXT"]; ?>
        </p>
    <? endforeach; ?>
    <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <br /><?= $arResult["NAV_STRING"] ?>
    <? endif; ?>
</div>
