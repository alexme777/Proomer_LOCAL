<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
$mrh_login = $paySysParams["ShopLogin"]["VALUE"];
$mrh_pass1 =  $paySysParams["ShopPassword"]["VALUE"];
$inv_id = $paySystemActionData["OrderDescr"];
$inv_number = $paySystemActionData["OrderDescr"];
$inv_desc =  $paySystemActionData["OrderDescr"];
$user_mail = $paySystemActionData["EMAIL_USER"];
$out_summ = number_format(floatval($paySystemActionData["SHOULD_PAY"]), 2, ".", "");
$isTest = trim($paySysParams["IS_TEST"]["VALUE"]);
$crc = md5($mrh_login.":".$out_summ.":".$inv_id.":".$mrh_pass1);
$paymentType = trim($paySysParams["PAYMENT_VALUE"]["VALUE"]);
?>

<?if(strlen($isTest) > 0):
	?>
	<form action="http://test.robokassa.ru/Index.aspx" method="post" target="_blank">
<?else:
	?>
	<form action="https://merchant.roboxchange.com/Index.aspx" method="post" target="_blank">
<?endif;?>

<input type="hidden" name="FinalStep" value="1">
<input type="hidden" name="MrchLogin" value="<?=$mrh_login?>">
<input type="hidden" name="OutSum" value="<?=$out_summ?>">
<input type="hidden" name="InvId" value="<?=$inv_id?>">
<input type="hidden" name="Desc" value="<?=$inv_desc?>">
<input type="hidden" name="SignatureValue" value="<?=$crc?>">
<input type="hidden" name="Email" value="<?=$user_mail?>">
<?
if (strlen($paymentType) > 0 && $paymentType != "0")
{
	?>
	<input type="hidden" name="IncCurrLabel" value="<?=$paymentType?>">
	<?
}
?>
<div class="btn-wrapper">
    <a class="btn blue js-submit-btn waves-effect" href="javascript:void(0);">Оплатить заказ</a>
</div>

</p>
</form>