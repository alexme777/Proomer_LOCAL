<?php


class XML extends SimpleXMLReader
{
	//будут содержать в себе св-ва товаров
	public $arr_items = array();
	
    public function __construct()
    {
        // можно просто указать название тега и коллбыка для обработки данных
        // или указать полный путь(рекомендуется)
		//$this->registerCallback("/yml_catalog/shop/offers/offer/price", array($this, "callbackPrice"));
		//$this->registerCallback("/yml_catalog/shop/offers/offer/author", array($this, "callbackAuthor"));
		$this->registerCallback("/yml_catalog/shop/offers/offer", array($this, "callbackOffer"));
    }
	protected function callbackOffer($reader)
    {
		$xml = $reader->expandSimpleXml();
		array_push($this->arr_items, $xml);
       /* $attributes = $xml->attributes();
        $ref = (string) $attributes->{"parentId"};
       // if ($ref) {
            $price = $xml;
            $xpath = $this->currentXpath();
			//echo "$xpath: $ref = $price;\n";
			
      //  }*/
		
        return true;
    }
    protected function callbackPrice($reader)
    {
		
        $xml = $reader->expandSimpleXml();
        $attributes = $xml->attributes();
        $ref = (string) $attributes->{"parentId"};
       // if ($ref) {
            $price = $xml;
            $xpath = $this->currentXpath();
			echo "$xpath: $ref = $price;\n";
			
      //  }
		
        return true;
    }
	protected function callbackAuthor($reader)
    {
		
        $xml = $reader->expandSimpleXml();
        $attributes = $xml->attributes();
        $ref = (string) $attributes->{"parentId"};
       // if ($ref) {
            $price = $xml;
            $xpath = $this->currentXpath();
			echo "$xpath: $ref = $price;\n";
			
      //  }
		
        return true;
    }
}








