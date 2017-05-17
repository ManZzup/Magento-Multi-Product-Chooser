<?php 
/**
 * @author      manujith pallewatte <manujith.nc@gmail.com>
 * @website     http://manujith.me
 * Code was taken from the Mage_Adminhtml_Block_Widget_Grid_Massaction class and
 * fixed to allow the custom chooser to work.
 */
class Manujith_MultiProductChooser_Block_Catalog_Product_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction{
    public function getApplyButtonHtml()
    {
    	$js = '
            	console.log({jsObject1});
            	{jsObject2}.setElementLabel({jsObject1}.getTextValue());
            	{jsObject2}.setElementValue({jsObject1}.getCheckedValues());
            	{jsObject2}.close();
        ';
        $js = str_replace('{jsObject1}', $this->getJsObjectName(), $js);
        $js = str_replace('{jsObject2}', $this->getParentBlock()->getHtmlId(), $js);

        return $this->getButtonHtml($this->__('Done'),$js);
    }
    public function getJavaScript()
    {
        return " {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', "
                . "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
                . ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
                . "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
                . "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
                . ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
                . ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
                . "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
    }
}
?>