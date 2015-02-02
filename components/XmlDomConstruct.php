<?php
/**
 * Extends the DOMDocument to implement personal (utility) methods.
*
* @author Toni Van de Voorde
*/
class XmlDomConstruct extends DOMDocument {

	/**
	 * Constructs elements and texts from an array or string.
	 * The array can contain an element's name in the index part
	 * and an element's text in the value part.
	 *
	 * It can also creates an xml with the same element tagName on the same
	 * level.
	 *
	 * ex:
	 * <nodes>
	 *   <node>text</node>
	 *   <node>
	 *     <field>hello</field>
	 *     <field>world</field>
	 *   </node>
	 * </nodes>
	 *
	 * Array should then look like:
	 *
	 * Array (
	 *   "nodes" => Array (
	 *     "node" => Array (
	 *       0 => "text"
	 *       1 => Array (
	 *         "field" => Array (
	 *           0 => "hello"
	 *           1 => "world"
	 *         )
	 *       )
	 *     )
	 *   )
	 * )
	 *
	 * @param mixed $mixed An array or string.
	 *
	 * @param DOMElement[optional] $domElement Then element
	 * from where the array will be construct to.
	 *
	 */
	public function fromMixed($mixed, DOMElement $domElement = null) {

		$domElement = is_null($domElement) ? $this : $domElement;

		if (is_array($mixed)) {
			foreach( $mixed as $index => $mixedElement ) {

				if ( is_int($index) ) {
					if ( $index == 0 ) {
						$node = $domElement;
					} else {
						$node = $this->createElement($domElement->tagName);
						$domElement->parentNode->appendChild($node);
					}
				}
				 
				else {
					$node = $this->createElement($index);
					$domElement->appendChild($node);
				}
				 
				$this->fromMixed($mixedElement, $node);
				 
			}
		} else {
			$domElement->appendChild($this->createTextNode($mixed));
		}
		 
	}
	
	public function toArray(DOMNode $oDomNode = null)
	{
		// return empty array if dom is blank
		if (is_null($oDomNode) && !$this->hasChildNodes()) {
			return array();
		}
		$oDomNode = (is_null($oDomNode)) ? $this->documentElement : $oDomNode;
		$arResult = array();
		if (!$oDomNode->hasChildNodes()) {
			$arResult[$oDomNode->nodeName] = $oDomNode->nodeValue;
		} else {
			foreach ($oDomNode->childNodes as $oChildNode) {
				// how many of these child nodes do we have?
				$oChildNodeList = $oDomNode->getElementsByTagName($oChildNode->nodeName); // count = 0
				$iChildCount = 0;
				// there are x number of childs in this node that have the same tag name
				// however, we are only interested in the # of siblings with the same tag name
				foreach ($oChildNodeList as $oNode) {
					if ($oNode->parentNode->isSameNode($oChildNode->parentNode)) {
						$iChildCount++;
					}
				}
				$mValue = $this->toArray($oChildNode);
				$mValue = is_array($mValue) ? $mValue[$oChildNode->nodeName] : $mValue;
				$sKey = ($oChildNode->nodeName{0} == '#') ? 0 : $oChildNode->nodeName;
				// this will give us a clue as to what the result structure should be
				// how many of thse child nodes do we have?
				
				if($iChildCount == 1) { // only one child – make associative array
					$arResult[$sKey] = $mValue;
				} elseif ($iChildCount > 1) { // more than one child like this – make numeric array
					$arResult[$sKey][] = $mValue;
				} elseif ($iChildCount == 0) { // no child records found, this is DOMText or DOMCDataSection
					$arResult[$sKey] = $mValue;
				}
			}
			// if the child is bar, the result will be array(bar)
			// make the result just ‘bar’
			if (count($arResult) == 1 && isset($arResult[0]) && !is_array($arResult[0])) {
				$arResult = $arResult[0];
			}
			$arResult = array($oDomNode->nodeName=>$arResult);
		}
		// get our attributes if we have any
		if ($oDomNode->hasAttributes()) {
			foreach ($oDomNode->attributes as $sAttrName=>$oAttrNode) {
				// retain namespace prefixes
				$arResult["@{$oAttrNode->nodeName}"] = $oAttrNode->nodeValue;
			}
		}
		return $arResult;
	}	
	 
}
?>