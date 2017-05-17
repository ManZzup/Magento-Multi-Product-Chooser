<?php
/**
 * @author      manujith pallewatte <manujith.nc@gmail.com>
 * @website     http://manujith.me
 * Code was taken from the Mage_Adminhtml_Controller_Action class and
 * fixed to allow the custom chooser to work.
 */
class Manujith_MultiProductChooser_Adminhtml_MultiProductChooserController extends Mage_Adminhtml_Controller_Action {


  public function chooserAction()
    {
        $oRequest = $this->getRequest();
        $iUniqId = $oRequest->getParam('uniq_id');
        $bMassAction = $oRequest->getParam('use_massaction', false);
        $iProductTypeId = $oRequest->getParam('product_type_id', null);

        $oLayout = $this->getLayout();
        $oProductsGrid = $oLayout->createBlock(
            'multiproductchooser/catalog_product_widget_chooser',
            '',
            [
                'id' => $iUniqId,
                'use_massaction' => $bMassAction,
                'product_type_id' => $iProductTypeId,
                'category_id' => $this->getRequest()->getParam('category_id')
            ]
        );

        $sChooserHtml = $oProductsGrid->toHtml();

        if (!$oRequest->getParam('products_grid')) {
            $oCategoriesTree = $oLayout->createBlock(
                'adminhtml/catalog_category_widget_chooser',
                '',
                [
                    'id' => $iUniqId . 'Tree',
                    'node_click_listener' => $oProductsGrid->getCategoryClickListenerJs(),
                    'with_empty_node' => true
                ]
            );

            $oChooserContainer = $oLayout->createBlock('adminhtml/catalog_product_widget_chooser_container');
            $oChooserContainer->setTreeHtml($oCategoriesTree->toHtml());
            $oChooserContainer->setGridHtml($sChooserHtml);
            $sChooserHtml = $oChooserContainer->toHtml();
        }

        $this->getResponse()->setBody($sChooserHtml);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/widget_instance');
    }
}

?>