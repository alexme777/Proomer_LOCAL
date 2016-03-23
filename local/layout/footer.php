   <?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
    </main>
    <?= EZendManager::sidebarBasket() ?>
    </div><!-- /.wrapper -->

    <footer>
        <div class="top">
            <div class="content-container">
                <div class="left-column">
                    <?Zend_Registry::get('BX_APPLICATION')->IncludeComponent(
                        "bitrix:menu",
                        "bottom",
                        Array(
                            "ROOT_MENU_TYPE" => "bottom1",
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
                    <div class="copyright">&copy; <?= date('Y') ?> <? EHelper::includeArea("footer/copyright") ?></div>
                </div>

                <div class="center-column">
					<?/*Zend_Registry::get('BX_APPLICATION')->IncludeComponent(
                        "bitrix:menu",
                        "bottom",
                        Array(
                            "ROOT_MENU_TYPE" => "bottom2",
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
                    );*/?>
					<div class="payment">
						<a class="visa" href="http://visa.com" title="Visa" target="_blank"></a>
						<a class="master" href="http://.mastercard.com/" title="Master Card" target="_blank"></a>
						<a class="webm" href="http://webmoney.com" title="Webmoney" target="_blank"></a>
						<a class="yandex" href="http://yandex.ru" title="Yandex" target="_blank"></a>
					</div>
                </div>

                <div class="right-column">
                      <!--<a class="to-top js-scroll-top" href="javascript:void(0);"></a>-->
                    <?Zend_Registry::get('BX_APPLICATION')->IncludeComponent("sibirix:socials", "", array('CODE'=>'SOCIALS'), false);?>
					<div class="cont">г. Кемерово: <span class="number">+7 (975) 856-14-56</span></div>
                </div>
            </div>
        </div>
        <!--<div class="bottom">
            <div class="content-container">
                <div class="sibirix">
                    <div class="slon">
                        <span class="slon-icon"></span>
                        <span class="js-slon-animation slon-animation"><img src="<?= P_IMAGES?>slon.gif"></span>
                    </div>

                    Разработка сайта — <a href="http://www.sibirix.ru" target="_blank" class="under-link">«Сибирикс»</a>
                </div>
            </div>
        </div>-->
    </footer>
	<? include(P_DR . P_APP . 'layout' . DIRECTORY_SEPARATOR . 'popups.php') ?>
	<? include(P_DR . P_APP . 'layout' . DIRECTORY_SEPARATOR . 'templates.php') ?>
	<? include(P_DR . P_APP . 'layout' . DIRECTORY_SEPARATOR . 'footer-scripts.php') ?>
		
</body>
</html>