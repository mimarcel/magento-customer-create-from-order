<?php
class Max_CustomerCreateFromOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return string
     */
    public function getCustomerGroupId()
    {
        return Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
    }
}
