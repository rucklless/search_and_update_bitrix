<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
class SearchAndUpdate{

	/*
	 * получаем элементы с пустым PROPERTY_H2? вытягиваем из PREVIEW_TEXT заголовок h3 и суем его его в свойство h2
	 * **/
	private static $ibId = 12;
	/*
	 * Получаем элементы
	 * **/
	private static function getList(){
		$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_TEXT", "PROPERTY_H2");
		$arFilter = Array("IBLOCK_ID"=>IntVal(12), "ACTIVE_DATE"=>"Y", "PROPERTY_H2" => false);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			yield $arFields;
		}
	}
	private static function uploadPreviewText($arr){
		$el = new CIBlockElement;
		$arLoadProductArray = array(
			"PREVIEW_TEXT" => self::replace($arr['PREVIEW_TEXT'])[1],
		);
		$res = $el->Update($arr['ID'], $arLoadProductArray);
	}
	private static function uploadH2($id, $str){
		CIBlockElement::SetPropertyValuesEx($id, self::$ibId, array('H2' => $str));
	}
	/*
	 * Модифицируем элементы
	 * */
	public static function uploadElems(){
		$timestamp = time();
		$cnt = '0';
		?><pre><?print_r($timestamp)?></pre><?
		foreach (self::getList() as $item){

			$replaceArr = self::replace($item['PREVIEW_TEXT']);
			/*self::uploadPreviewText($item);
			self::uploadH2($item['ID'],$replaceArr[0]);*/
			?><pre><?print_r($replaceArr)?></pre><?
			$sec = time()-$timestamp;
			if($sec>20)
				break;
			else
				echo $sec.' sec<br>';
			echo ++$cnt.'elem<br>';
		}
	}
	/*
	 * Ищет в тексте заголовки и возвращает массив с заголовком и текстом с вырезанным заголовком
	 * **/
	public static function replace($text){
		$pattern = '|^<h3>(.*)</h3>?\s*(.+)|';
		$perlacement = '$2';
		preg_match($pattern, $text, $matches);
		return array($matches[1],preg_filter($pattern, $perlacement, $text),'');
	}
}
SearchAndUpdate::uploadElems();
?>