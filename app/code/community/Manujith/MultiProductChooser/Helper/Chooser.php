<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@extendix.com>
 * @website     http://www.extendix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 * Credits to the original author for creating the product chooser helper that can be used,
 * easily to get the "Select Products" button.
 * Original git repo at https://github.com/extendix/Extendix_AdminFormChooserButton
 */

class Manujith_MultiProductChooser_Helper_Chooser
    extends Mage_Core_Helper_Abstract
{

    const PRODUCT_CHOOSER_BLOCK_ALIAS     = 'multiproductchooser/catalog_product_widget_chooser';

    const XML_PATH_DEFAULT_CHOOSER_CONFIG = 'multiproductchooser/chooser_defaults';

    protected $_requiredConfigValues = array('input_name');

    public function createProductChooser(
        Mage_Core_Model_Abstract $dataModel,
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $config
    )
    {
        $blockAlias = self::PRODUCT_CHOOSER_BLOCK_ALIAS;
        $this->_prepareChooser($dataModel, $fieldset, $config, $blockAlias);
        return $this;
    }

    public function createChooser(
        Mage_Core_Model_Abstract $dataModel,
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $config,
        $blockAlias
    )
    {
        $this->_prepareChooser($dataModel, $fieldset, $config, $blockAlias);
        return $this;
    }

    protected function _prepareChooser(
        Mage_Core_Model_Abstract $dataModel,
        Varien_Data_Form_Element_Fieldset $fieldset,
        array $config,
        $blockAlias
    )
    {
        $this->_checkRequiredConfigs($config)
                ->_populateMissingConfigValues($config, $blockAlias);
        
        $chooserConfigData = $this->_prepareChooserConfig($config, $blockAlias);
        $chooserBlock = Mage::app()->getLayout()->createBlock($blockAlias, '', $chooserConfigData);
        
        $element = $this->_createFormElement($dataModel, $fieldset, $config);
        
        $chooserBlock
            ->setConfig($chooserConfigData)
            ->setFieldsetId($fieldset->getId())
            ->prepareElementHtml($element);

        $this->_fixChooserAjaxUrl($element);

        return $this;
    }

    protected function _checkRequiredConfigs(array $config)
    {
        foreach ($this->_requiredConfigValues as $value) {
            if (!isset($config[$value])) {
                throw new Exception("Required input config value \"" . $value . "\" is missing.");
            }
        }

        return $this;
    }

    protected function _populateMissingConfigValues(array &$config, $blockAlias)
    {
        $blockAliasStringParts = explode('/', $blockAlias);
        $currentWidgetKey = $blockAliasStringParts[1];
        $chooserDefaults = Mage::getStoreConfig(self::XML_PATH_DEFAULT_CHOOSER_CONFIG);

        if (!isset($chooserDefaults[$currentWidgetKey])) {
            $currentWidgetKey = 'default';
        }

        foreach ($chooserDefaults[$currentWidgetKey] as $configKey => $value) {
            if (!isset($config[$configKey])) {
                $config[$configKey] = $value;
            }
        }

        return $this;
    }

    protected function _createFormElement(
        Mage_Core_Model_Abstract $dataModel,
        Varien_Data_Form_Element_Fieldset $fieldset,
        array$config
    )
    {
        $isRequired = (isset($config['required']) && true === $config['required']) ? true : false;

        $inputConfig = array(
            'name'  => $config['input_name'],
            'label' => $config['input_label'],
            'required' => $isRequired
        );

        if (!isset($config['input_id'])) {
            $config['input_id'] = $config['input_name'];
        }

        $element = $fieldset->addField($config['input_id'], 'label', $inputConfig);
        $element->setValue($dataModel->getData($element->getId()));
        $dataModel->setData($element->getId(), '');

        return $element;
    }

    protected function _prepareChooserConfig(array $config, $blockAlias)
    {
        return array(
            'button' =>
                array(
                    'open' => $config['button_text'],
                    'type' => $blockAlias
                )
        );
    }

    protected function _fixChooserAjaxUrl(Varien_Data_Form_Element_Abstract $element)
    {
        $adminPath = (string)Mage::getConfig()
            ->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME);

        $currentRouterName = Mage::app()->getRequest()->getRouteName();

        if($adminPath != $currentRouterName) {
            $afterElementHtml = $element->getAfterElementHtml();
            $afterElementHtml = str_replace('/' . $currentRouterName . '/','/' . $adminPath . '/', $afterElementHtml);
            $element->setAfterElementHtml($afterElementHtml);
        }
    }
}
