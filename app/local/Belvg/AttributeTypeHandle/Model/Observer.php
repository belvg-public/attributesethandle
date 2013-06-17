<?php
/**
 * @author: A.A.Treitjak
 * @copyright: 2012 - 2013 BelVG.com
 */

class Belvg_AttributeTypeHandle_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function addAttributeSetHandle(Varien_Event_Observer $observer)
    {
        if(!Mage::helper('belvg_attributetypehandle')->isEnabled()) {
            return $this;
        }

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('current_product');

        /**
         * Return if it is not product page
         */
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return FALSE;
        }

        $attributeSet = Mage::getModel('eav/entity_attribute_set')
            ->load($product->getAttributeSetId());

        /**
         * Convert attribute set name.
         */
        $nameAttributeSet = strtoupper(str_replace('-', '_', $product->formatUrlKey(
            $attributeSet->getAttributeSetName()
        )));

        /* @var $update Mage_Core_Model_Layout_Update */
        $update = $observer->getEvent()->getLayout()->getUpdate();

        /* Remember all handles in a variable */
        $handles = $update->getHandles();

        $update->addHandle('PRODUCT_ATTRIBUTE_SET_' . $nameAttributeSet);

        $update->resetHandles(); // Remove all handles

        foreach ($handles as $handle) {

            $update->addHandle($handle);

            if ($handle == 'PRODUCT_TYPE_' . $product->getTypeId()) {
                $update->addHandle('PRODUCT_ATTRIBUTE_SET_' . $nameAttributeSet);
            }
        }

        return $this;
    }

}