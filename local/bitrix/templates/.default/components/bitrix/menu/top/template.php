<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="menu">
	<ul>

	<?
	foreach($arResult as $arItem):
		if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
			continue;
	?>
		<?if($arItem["SELECTED"]):?>
			<li><a href="<?=$arItem["LINK"]?>" class="selected waves-effect"><span><?=$arItem["TEXT"]?></span></a></li>
		<?else:?>
			<li><a href="<?=$arItem["LINK"]?>" class="waves-effect <?= ($arItem['PARAMS']['class']) ?>"><?=$arItem["TEXT"]?></a></li>
		<?endif?>

	<?endforeach?>

	</ul>
</div>
<?endif?>
