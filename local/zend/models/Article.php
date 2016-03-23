<?

/**
 * Class Sibirix_Model_Complex
 *
 */
class Sibirix_Model_Article extends Sibirix_Model_Bitrix {

    protected $_iblockId = IB_ARTICLE;
    protected $_selectFields = array(
        'ID',
        'NAME',
		'IBLOCK_SECTION_ID',
		'PREVIEW_PICTURE',
		'DETAIL_PICTURE',
		'PREVIEW_TEXT',
		'DETAIL_TEXT',
		'CREATED_BY',
		'CREATED_USER_NAME',
		'DATE_CREATE',
		'SHOW_COUNTER',
		"NUM_COMMENTS",
		'PROPERTY_FORUM_MESSAGE_CNT',
		'FORUM_MESSAGE_CNT',
		'PROPERTY_COMMENTS_COUNT',
		'PROPERTY_BLOG_DATA'
    );
	
/*=================================================================================*/
//	Берет по id
/*=================================================================================*/	
	public function getArticle($where) {
		$artice = $this->select($this->_selectFields, true)->where(["=ID" => $where])->orderBy([], true)->getElement();
		return $artice;
    }
	
	public function getArticles($where) {
		$artice = $this->select($this->_selectFields, true)->where(["=ID" => $where])->orderBy([], true)->getElements();
		return $artice;
    }
	
/*=================================================================================*/
//	Берет картинки у категорий
/*=================================================================================*/	
	public function getDataImg($categories) {
	
		return $this->getImageData($categories);
     
    }
/*=================================================================================*/
//	Категории
/*=================================================================================*/
	public function getCategory($params = []) {
	
		return $this->getSections($params);
     
    }
/*=================================================================================*/
//	Категории
/*=================================================================================*/
	public function getCatChild($where) {
		$params['where'] = ["=SECTION_ID" => $where];
		return $this->getSections($params);
     
    }
/*=================================================================================*/
//	Добавляет один товар
/*=================================================================================*/
	public function addGoods($data) {
	
		return $this->add($data);
        //echo $element->SetPropertyValuesEx(100, IB_GOODS, $data);
    }
	
/*=================================================================================*/
//	Возвращает список товаров
/*=================================================================================*/
	public function getGoods($where) {
		$goods['ITEMS'] = $this->select($this->_selectFields, true)->where(["=PROPERTY_ID_USER" => $where])->orderBy([], true)->getElements();
		return $goods;
    }
    	public function getGoodsDiscount($where = 1) {
			$goods = $this->select($this->_selectFields, true)->where([$where])->orderBy([], true)->getElements();
			return $goods;
    }
	public function getItems($where, $offset = '', $limit = '', $sort = ''){
		$goods = $this->select($this->_selectFields, true)->where($where)->page($offset, $limit)->orderBy($sort, true)->getElements();
		return $goods;
    }

    public function getImgItems($arr_row) {
    	foreach($arr_row as $row){
    		if(!empty($row->PREVIEW_PICTURE)){$row->PREVIEW_PICTURE = CFile::GetPath($row->PREVIEW_PICTURE);};
    		if(!empty($row->DETAIL_PICTURE)){$row->DETAIL_PICTURE = CFile::GetPath($row->DETAIL_PICTURE);};
    		if(!empty($row->PROPERTY_IMG_VALUE)){$row->PROPERTY_IMG_VALUE = CFile::GetPath($row->PROPERTY_IMG_VALUE);};
    	};
		return $arr_row;
    }
    public function getImgItemsPrev($arr_row) {
    	$arr_img = array();
    	foreach($arr_row as $row){
    		if(!empty($row->DETAIL_PICTURE)){$row->DETAIL_PICTURE = CFile::GetPath($row->DETAIL_PICTURE);};
    		if(!empty($row->PROPERTY_PREVIEW_VALUE)){

    			//$path = CFile::GetPath($row->PROPERTY_PREVIEW_VALUE);
    			array_push($arr_img, CFile::GetPath($row->PROPERTY_PREVIEW_VALUE));
    		}
    	};
    	$arr_row[0]->PROPERTY_PREVIEW_VALUE = $arr_img;

		return $arr_row;
    }
	public function getGoodsItem($where){
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $where])->orderBy([], true)->getElements();
		return $goods;
    }
    	public function getGoodsAItem($where){
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $where])->orderBy([], true)->getElement();
		return $goods;
    }
    public function getItemList($where){
		$goods = $this->select($this->_selectFields, true)->where($where)->orderBy([], true)->getElements();
		return $goods;
    }

/*=================================================================================*/
//	Обновляет товары
/*=================================================================================*/
	public function getGoodsId($id) {
		$goods = $this->select($this->_selectFields, true)->where(["=ID" => $id])->getElements();
		return $goods;
    }
/*=================================================================================*/
//	Обновляет товары
/*=================================================================================*/
	public function changestatusGoods($id, $fields) {
		$result = $this->updateValue($id, $fields);
		return $result;
    }
	public function updateGoods($id, $fields, $prop_fields) {
		$result = $this->update($id, $fields);
		$result = $this->updateValue($id, $prop_fields);
		return $result;
    }

/*=================================================================================*/
//	Удаляет товары
/*=================================================================================*/	
	public function delGoods($id, $where) {
		$this->remove($id);
    }
/*=================================================================================*/
/*=================================================================================*/		
	 public function addPlan($id, $imageFile) {
        $imageExist = $this->select(["PROPERTY_PREVIEW"], true)->getElement($id);
        $alreadyImages = array();
		
        foreach ($imageExist->PROPERTY_PREVIEW_VALUE as $key => $image) {
            $alreadyImages[$imageExist->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key]] = CIBlock::makeFilePropArray($image);
        }
        $fields["ID"] = $id;
        $fields["PROPERTY_VALUES"] = array(
            "PREVIEW" => $alreadyImages + array("n0" => array("VALUE" => $imageFile))
        );
	
        $element = new CIBlockElement();
		
        $element->SetPropertyValuesEx($id, IB_GOODS, $fields["PROPERTY_VALUES"]);

        $newImage = $this->select(["PROPERTY_PREVIEW"], true)->getElement($id);
		//echo print_r($newImage);
		//exit;
        $resultImages = array();
        foreach ($newImage->PROPERTY_PREVIEW_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_PREVIEW_PHOTO")
            );
        }

        return $resultImages;
    }

    /**
     * Удаляет файл из множетсвенного свойства
     * возвращает список новых файлов
     * @param $imageItemId
     * @return array
     * @throws Exception
     */
    public function deletePlan($designId, $imageItemId) {
        $arFile["MODULE_ID"] = "iblock";
        $arFile["del"] = "Y";

        $element = new CIBlockElement();
        $element->SetPropertyValueCode($designId, "PREVIEW", Array($imageItemId => Array("VALUE" => $arFile)));

        $newImage = $this->select(["PROPERTY_PREVIEW"], true)->getElement($designId);
        $resultImages = array();
        foreach ($newImage->PROPERTY_PREVIEW_VALUE as $key => $imageId) {
            $resultImages[] = array(
                "valueId" => $newImage->PROPERTY_PREVIEW_PROPERTY_VALUE_ID[$key],
                "imgSrc" => Resizer::resizeImage($imageId, "DROPZONE_PREVIEW_PHOTO")
            );
        }

        return $resultImages;
    }
}








