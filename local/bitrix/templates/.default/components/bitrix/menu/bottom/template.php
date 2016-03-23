<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

    <ul class="bottom-menu">
        <?
        foreach($arResult as $arItem):
            if ($arItem['PARAMS']['COLUMN'] == 1) continue; ?>
            <li><a href="<?=$arItem["LINK"]?>" class="under-link"><?=$arItem["TEXT"]?></a></li>
        <?endforeach?>
    </ul>

<?endif?>




