<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @var array $arParams
 */

CUtil::InitJSCore(array("popup"));

$arAuthServices = $arPost = array();
if(is_array($arParams["~AUTH_SERVICES"]))
{
    $arAuthServices = $arParams["~AUTH_SERVICES"];
}
if(is_array($arParams["~POST"]))
{
    $arPost = $arParams["~POST"];
}

$hiddens = "";
foreach($arPost as $key => $value)
{
    if(!preg_match("|OPENID_IDENTITY|", $key))
    {
        $hiddens .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
    }
}
?>
<script type="text/javascript">
    function BxSocServPopup(id)
    {
        var content = BX("bx_socserv_form_"+id);
        if(content)
        {
            var popup = BX.PopupWindowManager.create("socServPopup"+id, BX("bx_socserv_icon_"+id), {
                autoHide: true,
                closeByEsc: true,
                angle: {offset: 24},
                content: content,
                offsetTop: 3
            });

            popup.show();

            var input = BX.findChild(content, {'tag':'input', 'attribute':{'type':'text'}}, true);
            if(input)
            {
                input.focus();
            }

            var button = BX.findChild(content, {'tag':'input', 'attribute':{'type':'submit'}}, true);
            if(button)
            {
                button.className = 'btn btn-primary';
            }
        }
    }
</script>

<div class="bx-authform-social">
    <ul>
        <?
        foreach($arAuthServices as $service):
            $onclick = ($service["ONCLICK"] <> ''? $service["ONCLICK"] : "BxSocServPopup('".$service["ID"]."')");
            ?>
            <a id="bx_socserv_icon_<?=$service["ID"]?>" class="<?=$service["ICON"]?> bx-authform-social-icon" href="javascript:void(0)" onclick="<?=htmlspecialcharsbx($onclick)?>" title="<?=htmlspecialcharsbx($service["NAME"])?>"></a>

        <?
        endforeach;
        ?>
    </ul>
</div>