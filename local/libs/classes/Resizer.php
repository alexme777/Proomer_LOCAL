<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/**
 * Интерфейс для пережатия картинок, в том числе массового, по заранее определенным типам
 *
 * работа осуществляется путем добавления именованных "типов пережатия" с последующим применением
 * этих типов пережатия над одним или многими элементами инфоблока
 */
class Resizer
{
    private static $imageTypes = array(
        /*
        '<CODE>' => array(
            'width'   => 990,
            'height'  => 487,
            'type'    => BX_RESIZE_IMAGE_EXACT
        ),
        */
        'LOGO' => [
            'width'  => 100,
            'height' => 30,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        'PROMO_PHOTO' => [
            'width'  => 150,
            'height' => 120,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		'MAIN_PAGE_USER_PREVIEW_PHOTO' => [
            'width'  => 180,
            'height' => 180,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		'PLAN_PREVIEW_PHOTO' => [
            'width'  => 150,
            'height' => 150,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		'PLAN_MEDIUM_PHOTO' => [
            'width'  => 275,
            'height' => 275,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        'COMPLEX_LIST' => [
            'width'  => 280,
            'height' => 279,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		'MAIN_PAGE_DESIGNERS_PREVIEW' => [
            'width'  => 181,
            'height' => 165,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
		'DESIGN_LIST' => [
            'width'  => 320,
            'height' => 210,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        'COMPLEX_SLIDER' => [
            'width'  => 300,
            'height' => 280,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        'DESIGN_LIST' => [
            'width'  => 280,
            'height' => 280,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        'DESIGNER_IMG_CARD' => [
            'width'  => 42,
            'height' => 42,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        'SHARE_IMG' => [
            'width'  => 130,
            'height' => 130,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        "SEARCH_SERVICE_PLAN" => [
            'width'  => 1024,
            'height' => 493,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
		"SERVICE_PLAN" => [
            'width'  => 450,
            'height' => 450,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
		"PREVIEW_PLAN_PROJECT" => [
            'width'  => 75,
            'height' => 90,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        "MAIN_MENU" => [
            'width'  => 60,
            'height' => 60,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        "PROFILE_MENU" => [
            'width'  => 20,
            'height' => 20,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        "PROFILE_AVATAR" => [
            'width'  => 300,
            'height' => 250,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        "HEADER_AVATAR" => [
            'width'  => 43,
            'height' => 43,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        "DROPZONE_MAIN_PHOTO" => [
            'width'  => 471,
            'height' => 357,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		"DROPZONE_ANONS_PHOTO" => [
            'width'  => 235,
            'height' => 179,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
		"DROPZONE_PREVIEW_PHOTO" => [
            'width'  => 471,
            'height' => 357,
            'type'   => BX_RESIZE_IMAGE_PROPORTIONAL,
        ],
        "DROPZONE_ROOMS_PHOTO" => [
            'width'  => 170,
            'height' => 170,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        "BASKET_PHOTO" => [
            'width'  => 210,
            'height' => 210,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ],
        "BASKET_SIDEBAR" => [
            'width'  => 120,
            'height' => 120,
            'type'   => BX_RESIZE_IMAGE_EXACT,
        ]
    );

    /**
     * Проверка что нужный тип пережатия существует
     * @param $name
     * @return bool
     */
    static function imageTypeExists($name) {
        return array_key_exists($name, self::$imageTypes);
    }

    /**
     * Добавление нового ТИПА ПЕРЕЖАТИЯ
     *
     * @param $name string название ТИПА ПЕРЕЖАТИЯ
     * @param $width int ширина пережатого изображения
     * @param $height int высота пережатого изображения
     * @param $type string Код типа пережатия битрикса
     */
    static function addImageType($name, $width, $height, $type) {
        Resizer::$imageTypes[$name] = array(
            'width'   => $width,
            'height'  => $height,
            'type'    => $type
        );
    }

    /**
     * Масштабирует изображения из инфоблока по заранее определенного шаблону определенному через Resizer::addImageType
     *
     * @param $file
     * @param $typeName string имя ТИПА ПЕРЕЖАТИЯ
     *
     * @return string URL нового изображения
     * @throws Exception
     */
    static function resizeImage($file, $typeName) {
        if (!self::imageTypeExists($typeName)) {
            throw new Exception(sprintf('There is no resize type "%s"', $typeName));
        }

        $type = Resizer::$imageTypes[$typeName];
        $cFile = new CFile();
        $image = $cFile->ResizeImageGet(
            $file,
            array('width' => $type['width'], 'height' => $type['height']),
            $type['type']
        );

        if (empty($image['src'])) {
            $imgSrc = P_IMAGES . "img-not-found.jpg";
        } else {
            $imgSrc = $image['src'];
        }

        return $imgSrc;
    }

}
