<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */
?>

    <?if($arResult["AUTH_SERVICES"]):?>
        <?
        $APPLICATION->IncludeComponent("bitrix:socserv.auth.form",
            "custom",
            array(
                "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                "AUTH_URL" => EZendManager::url([], 'profile'),
                "POST" => $arResult["POST"],
            ),
            $component,
            array("HIDE_ICONS"=>"Y")
        );
        ?>
    <?endif?>



