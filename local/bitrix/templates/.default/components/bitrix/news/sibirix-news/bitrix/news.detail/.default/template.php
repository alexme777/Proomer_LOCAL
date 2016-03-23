<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-detail">
    <img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
    <span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
    <h3><?=$arResult["NAME"]?></h3>

    <?
    if (strlen($arResult["DETAIL_TEXT"]) > 0) {
        ?><?= $arResult["DETAIL_TEXT"]; ?><?
    } else {
        ?><?= $arResult["PREVIEW_TEXT"]; ?><?
    } ?>

    <div class="news-detail-share">
        <noindex>
        <?
        $APPLICATION->IncludeComponent("bitrix:main.share", "", array(
                "HANDLERS" => $arParams["SHARE_HANDLERS"],
                "PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
                "PAGE_TITLE" => $arResult["~NAME"],
                "SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
                "SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
                "HIDE" => $arParams["SHARE_HIDE"],
            ),
            $component,
            array("HIDE_ICONS" => "Y")
        );
        ?>
        </noindex>
    </div>
</div>
<? if (isset($arResult['PREV'])) { ?>
    <a href='<?= $arResult['PREV']['DETAIL_PAGE_URL'] ?>'>&larr; <?= $arResult['PREV']['NAME'] ?></a>
<? } ?>

<a href="<?= $arResult["LIST_PAGE_URL"] ?>"><?= GetMessage("T_NEWS_DETAIL_BACK") ?></a>

<? if (isset($arResult['NEXT'])) { ?>
    <a href='<?= $arResult['NEXT']['DETAIL_PAGE_URL'] ?>'><?= $arResult['NEXT']['NAME'] ?> &rarr;</a>
<? } ?>