<div class="popups">
    <div class="popup feedback-popup" id="feedback">
        <h2>Написать нам</h2>
        <?= new Sibirix_Form_Feedback(); ?>
    </div>

    <div class="popup success-popup" id="success">
        <h2></h2>
        <div class="text">
            <div class="descr"></div>
        </div>
    </div>
    <?
    $bxSite = new CSite();
    if($bxSite->InDir('/design/') || EHelper::isMain()):
        ?>
        <div class="search-service" id="searchService">
            <?= EZendManager::action("index", "search-service"); ?>
        </div>
    <? endif ?>
    <div class="popup info-popup" id="info-popup">
        <div class="popup-title">
            <h2><?= Settings::getOption('aboutDesignProductLink'); ?></h2>
            <div class="title-info"><?= Settings::getOption('aboutDesignProductTitle'); ?></div>
        </div>
        <div class="info">
            <h3><?= Settings::getOption('aboutDesignProductText'); ?></h3>
            <div class="static-content">
                <?= Settings::getOptionText('aboutDesignProductText'); ?>
            </div>
        </div>
    </div>

    <? if (!Sibirix_Model_User::isAuthorized()) { ?>
        <div class="popup register-popup" id="registration-popup">
            <?= new Sibirix_Form_Registration(); ?>
			<!-- <div class="socials">
                <div class="label">Войти с помощью:</div>

                <?/* $APPLICATION->IncludeComponent(
                    "sibirix:auth.authorize",
                    "custom",
                    Array(
                        "REGISTER_URL" => "/",
                        "PROFILE_URL" => "/profile/",
                        "SHOW_ERRORS" => "Y"
                    ),
                    false
                );*/?>
            </div>-->
        </div>

        <div class="popup login-popup" id="login-popup">
            <?= new Sibirix_Form_Auth(); ?>
            <!--<div class="socials">
                <div class="label">Войти с помощью:</div>

                <?/*$APPLICATION->IncludeComponent(
                    "sibirix:auth.authorize",
                    "custom",
                    Array(
                        "REGISTER_URL" => "/",
                        "PROFILE_URL" => "/profile/",
                        "SHOW_ERRORS" => "Y"
                    ),
                    false
                );*/?>
            </div>-->
        </div>
		
    <? } ?>

    <div class="popup register-popup" id="type-popup">
        <?= new Sibirix_Form_ProfileType(); ?>
    </div>
	
	<div class="popup planirovka-popup" id="planirovka">
		<div class="row">
			<?= new Sibirix_Form_Planirovka(); ?>
		</div>
    </div>
	
	<div class="popup address-popup" id="address-popup">
		<div class="row">
			<form enctype="application/x-www-form-urlencoded" class="js-address-form addressForm formTabber" data-title="Регистрация прошла успешно" data-text="" action="/search-service/step-plan-name/search
" method="post" novalidate="novalidate">
				<h2 class="win_title">Ваш адрес?</h2>
				<div class="input-row">
					<input type="text" name="address" id="address-popup-input" value="" class="required" autocomplete="off" placeholder="">
									<div class="result preloaderController"><ul>
				</ul></div>
				</div>
				<div class="btn-wrapper">
				<a class="btn blue waves-effect view_n3 js-search-form">Искать мой дом</a>
			</div>
			</form>
		</div>
    </div>
	
	<div class="popup super-man-popup" id="call-super-man">
		<div class="row">
			<?= new Sibirix_Form_SuperMan(); ?>
		</div>
    </div>
	
    <div class="popup send-pass-popup" id="send-pass-popup">
        <?= new Sibirix_Form_Remind(); ?>
    </div>

    <div class="popup change-pass-popup" id="change-pass-popup">
        <?= new Sibirix_Form_ChangePassword(); ?>
    </div>


    <div class="popup delete-popup" id="delete-popup">
        <div class="popup-inner">
            Вы уверены, что хотите удалить эту фотографию?
        </div>
        <div class="btn-wrapper-double">
            <a href="javascript:void(0);" id="ok" class="btn blue waves-effect">Да</a>
            <a href="javascript:void(0);" id="cancel" class="btn blue waves-effect">Нет</a>
        </div>
    </div>

</div>