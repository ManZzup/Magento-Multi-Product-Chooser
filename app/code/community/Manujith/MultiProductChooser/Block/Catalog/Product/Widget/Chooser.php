<?php
/**
 * @author      manujith pallewatte <manujith.nc@gmail.com>
 * @website     http://manujith.me
 * Code was taken from the Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser class and
 * fixed to allow the custom chooser to work.
 */
class Manujith_MultiProductChooser_Block_Catalog_Product_Widget_Chooser extends Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser {


    protected $_massactionBlockName = 'multiproductchooser/widget_product_massaction';
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setMassactionBlockName('multiproductchooser/catalog_product_massaction');
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
        $this->useMassaction = true;
    }
    /**
     *
     * @return Mage_Adminhtml_Block_Widget_Grid|void
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('chooser_sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));
    }

    /**
     * Prepare the massaction
     *  - Block,
     *  - Item
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->setMassactionIdFilter('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('catalog')->__('Add Products'),
            'url'  => $this->getUrl('*/*/addProducts')
        ));

        Mage::dispatchEvent('multiproductchooser_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return "function (grid, element) {
            $(grid.containerId).fire('product:changed', {element: element});
        }";
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var productId = trElement.down("td").innerHTML;
                    var productName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = productName;
                    var optionValue = "product/" + productId.replace(/^\s+|\s+$/g,"");
                    if (grid.categoryId) {
                        optionValue += "/" + grid.categoryId;
                    }
                    if (grid.categoryName) {
                        optionLabel = grid.categoryName + " / " + optionLabel;
                    }
                    '.$chooserJsObject.'.setElementValue(optionValue);
                    '.$chooserJsObject.'.setElementLabel(optionLabel);
                    '.$chooserJsObject.'.close();
                }
            ';
    }

    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/multiProductChooser/chooser', array(
            'uniq_id' => $uniqId,
            'use_massaction' => true,
        ));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $productId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'product') {
                $productId = $value[1];
            }
            $categoryId = isset($value[2]) ? $value[2] : false;
            $label = '';
            if ($categoryId) {
                $label = Mage::getResourceSingleton('catalog/category')
                    ->getAttributeRawValue($categoryId, 'name', Mage::app()->getStore()) . '/';
            }
            if ($productId) {
                $label .= Mage::getResourceSingleton('catalog/product')
                    ->getAttributeRawValue($productId, 'name', Mage::app()->getStore());
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/multiProductChooser/chooser', array(
            'products_grid' => true,
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
            'product_type_id' => $this->getProductTypeId()
        ));
    }
}