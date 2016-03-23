<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/* @var $APPLICATION */
?><!doctype html>
<!--[if lt IE 7]>      <html class="no-js ie9 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js ie9 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js ie9 lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="no-js ie9"> <!--<![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="cmsmagazine" content="2c2951bb57cffc1481be768a629d3a6e" />
    <meta name="description" content="">
    <meta name="viewport" content="width=1300">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <meta content="telephone=no" name="format-detection">

    <title><? $APPLICATION->ShowTitle() ?><?= (!EHelper::isMain()) ? ' &mdash; ' . EHelper::get('NAME') : '' ?></title>

	
    <?
	$APPLICATION->SetAdditionalCSS(P_CSS . 'bootstrap.css');
    $APPLICATION->SetAdditionalCSS(P_CSS . 'style.css');
	$APPLICATION->SetAdditionalCSS(P_CSS . 'style2.css');
	$APPLICATION->SetAdditionalCSS(P_CSS . 'marketplace.css');
    echo(EHelper::jsApp());

    $APPLICATION->ShowHead();
    \Bitrix\Main\Page\Asset::getInstance()->addJs(P_JS . 'libs/modernizr-2.6.2.min.js');
    ?>
</head>

<body data-page-type="<?$APPLICATION->ShowProperty("page-type")?>" class="<?$APPLICATION->AddBufferContent(['EHelper', 'showPageClass'])?>">
<? $APPLICATION->ShowPanel(); ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter33207668 = new Ya.Metrika({
                    id:33207668,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/33207668" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<div class="wrapper">
    <!-- Шапка -->
	<?
		$class = "";
		if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == '/index.html'){
			$class = "";
		}
		else{
			$class = "show";
		};
	?>
	<?
	if($_SERVER['REQUEST_URI'] == '/'){
	?>
	<header id="view_n1">
        <div class="js-header-menu">
			<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">
						  <?
                $logoImg = Settings::getOptionFile('logo');
                if (!empty($logoImg)) {
                    $logo = Resizer::resizeImage(EHelper::getFileData($logoImg), "LOGO");
                }
            ?>

            <? if (EHelper::isMain()) { ?>
                <div class="logo"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></div>
            <? } else { ?>
                <a href="/" class="logo inner"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></a>
            <? } ?>
			<div class="select-city-container2">
                <?= EZendManager::citySelector() ?>
            </div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6">
	
			<!--<div class="registration">
				<a href="" class="view_n1 under-link-dotted">Регистрация</a><span class="arrow_view_n1"></span>
			</div>-->
			
						            <!-- Навигация -->
            <?Zend_Registry::get('BX_APPLICATION')->IncludeComponent(
                "bitrix:menu",
                "top",
                Array(
                    "ROOT_MENU_TYPE" => "top",
                    "MAX_LEVEL" => "1",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N",
                    "MENU_CACHE_TYPE" => "N",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(),
                ),
                false
            );?>
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">	
			<div class="dropdown-menu-wrapper js-dropdown-wrapper">
				<div class="mod-no-touchdevice mod-supportreal3d mod-acceptableperf js-open">
					<div class="b-sidebar js-sidebar _projectspage _active">
						<div class="b-sidebar-toggle" data-role="toggle"><i></i><i></i><i></i><b>Menu</b></div>  
					</div>
				</div>
				<div class="dropdown_parent">
					<div class="dropdown js-dropdown">
						<?= EZendManager::dropdownMenu(); ?>
						<?Zend_Registry::get('BX_APPLICATION')->IncludeComponent("sibirix:socials", "", array('CODE'=>'SOCIALS'), false);?>
					</div>
				</div>
			</div>
			<?= EZendManager::menuButtons() ?>
		</div>
            <!--<div class="phone"><?// EHelper::includeArea("header/phone") ?></div>-->
			<!--<div class="askQuestion"><a href="" class="view_n1 under-link-dotted">Задать вопрос</a><span class="arrow_view_n1"></span></div>-->
		

        </div>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-72408316-1', 'auto');
			ga('send', 'pageview');
		</script>
    </header>
	<?}else{?>
		<header id="view_n3" class="<?=$class?>" style="display:block;">
        <div class="js-header2-menu">
			<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">
						  <?
                $logoImg = Settings::getOptionFile('logo');
                if (!empty($logoImg)) {
                    $logo = Resizer::resizeImage(EHelper::getFileData($logoImg), "LOGO");
                }
            ?>

            <? if (EHelper::isMain()) { ?>
                <div class="logo"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></div>
            <? } else { ?>
                <a href="/" class="logo inner"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></a>
            <? } ?>
			<div class="select-city-container">
                <?= EZendManager::citySelector() ?>
            </div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6">
	
			<!--<div class="registration">
				<a href="" class="view_n1 under-link-dotted">Регистрация</a><span class="arrow_view_n1"></span>
			</div>-->
			
						            <!-- Навигация -->
            <?Zend_Registry::get('BX_APPLICATION')->IncludeComponent(
                "bitrix:menu",
                "top",
                Array(
                    "ROOT_MENU_TYPE" => "top",
                    "MAX_LEVEL" => "1",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N",
                    "MENU_CACHE_TYPE" => "N",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(),
                ),
                false
            );?>
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">	
			<div class="dropdown-menu-wrapper js-dropdown-wrapper">
				<div class="mod-no-touchdevice mod-supportreal3d mod-acceptableperf js-open">
					<div class="b-sidebar js-sidebar _projectspage _active">
						<div class="b-sidebar-toggle" data-role="toggle"><i></i><i></i><i></i><b>Menu</b></div>  
					</div>
				</div>
				<div class="dropdown_parent">
					<div class="dropdown js-dropdown">
						<?= EZendManager::dropdownMenu(); ?>
						<?Zend_Registry::get('BX_APPLICATION')->IncludeComponent("sibirix:socials", "", array('CODE'=>'SOCIALS'), false);?>
					</div>
				</div>
			</div>
			<?= EZendManager::menuButtons() ?>
		</div>
            <!--<div class="phone"><?// EHelper::includeArea("header/phone") ?></div>-->
			<!--<div class="askQuestion"><a href="" class="view_n1 under-link-dotted">Задать вопрос</a><span class="arrow_view_n1"></span></div>-->
		

        </div>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-72408316-1', 'auto');
			ga('send', 'pageview');
		</script>
    </header>
	<?}?>
	<?
	if($_SERVER['REQUEST_URI'] == '/'){
	?>
	<header id="view_n2" class="<?=$class?>" style="display:block;">
        <div class="js-header2-menu">
			<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">
						  <?
                $logoImg = Settings::getOptionFile('logo');
                if (!empty($logoImg)) {
                    $logo = Resizer::resizeImage(EHelper::getFileData($logoImg), "LOGO");
                }
            ?>

            <? if (EHelper::isMain()) { ?>
                <div class="logo"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></div>
            <? } else { ?>
                <a href="/" class="logo inner"<?= (!empty($logoImg))?' style="background-image: url('.$logo.')"':''?>></a>
            <? } ?>
			<div class="select-city-container">
                <?= EZendManager::citySelector() ?>
            </div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6">
	
			<!--<div class="registration">
				<a href="" class="view_n1 under-link-dotted">Регистрация</a><span class="arrow_view_n1"></span>
			</div>-->
			
						            <!-- Навигация -->
            <?Zend_Registry::get('BX_APPLICATION')->IncludeComponent(
                "bitrix:menu",
                "top",
                Array(
                    "ROOT_MENU_TYPE" => "top",
                    "MAX_LEVEL" => "1",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N",
                    "MENU_CACHE_TYPE" => "N",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(),
                ),
                false
            );?>
			</div>
			
			
			

			
			
			
			
			
			
			
			
			
			
			
		<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">	
			<div class="dropdown-menu-wrapper js-dropdown-wrapper">
				<div class="mod-no-touchdevice mod-supportreal3d mod-acceptableperf js-open">
					<div class="b-sidebar js-sidebar _projectspage _active">
						<div class="b-sidebar-toggle" data-role="toggle"><i></i><i></i><i></i><b>Menu</b></div>  
					</div>
				</div>
				<div class="dropdown_parent">
					<div class="dropdown js-dropdown">
						<?= EZendManager::dropdownMenu(); ?>
						<?Zend_Registry::get('BX_APPLICATION')->IncludeComponent("sibirix:socials", "", array('CODE'=>'SOCIALS'), false);?>
					</div>
				</div>
			</div>
			<?= EZendManager::menuButtons() ?>
		</div>
			
			
			
			
			
			
			
			
			
			

			<!--<div class="col-xs-3 col-sm-3 col-md-3" style="padding:0">
					<div class="dropdown-menu-wrapper js-dropdown-wrapper" >
				<a href="javascript:void(0);" class="dropdown-menu js-open"><div class="icon"></div></a>
				
				<div style="position:relative;">
				<div class="dropdown js-dropdown">
					<?//= EZendManager::dropdownMenu(); ?>
					<?//Zend_Registry::get('BX_APPLICATION')->IncludeComponent("sibirix:socials", "", array('CODE'=>'SOCIALS'), false);?>
				</div>
				</div>
			</div>
			<?//= EZendManager::menuButtons() ?>
			</div>
            <!--<div class="phone"><?// EHelper::includeArea("header/phone") ?></div>-->
			<!--<div class="askQuestion"><a href="" class="view_n1 under-link-dotted">Задать вопрос</a><span class="arrow_view_n1"></span></div>-->
		

        </div>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-72408316-1', 'auto');
			ga('send', 'pageview');
		</script>
    </header>
	<?}?>

	<div id="edge"></div>

    <main>