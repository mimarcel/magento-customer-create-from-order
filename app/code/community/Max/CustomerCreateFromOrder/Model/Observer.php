<?php

class Max_CustomerCreateFromOrder_Model_Observer
{
    public function adminhtmlWidgetContainerHtmlBefore(Varien_Event_Observer $event)
    {
        $block = $event->getData('block');

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View
            && $block->getOrder()->getCustomerGroupId() == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID) {
            $block->addButton('create_customer_from_order', array(
                'label' => $this->_getHelper()->__('Create Customer'),
                'onclick' => "confirmSetLocation('{$this->_getConfirmMessage()}', '{$this->_getUrl($block)}')",
                'class' => 'go'
            ));
        }
    }

    /**
     * @return Max_CustomerCreateFromOrder_Helper_Data
     */
    private function _getHelper()
    {
        return Mage::helper('customerCreateFromOrder');
    }

    private function _getConfirmMessage()
    {
        $message = $this->_getHelper()->__('Are you sure you want to create a customer using the details from this order?')
            . ' '
            . $this->_getHelper()->__('The new customer will be notified by email.');

        return $this->_getHelper()->jsQuoteEscape($message);
    }

    /**
     * @param Mage_Adminhtml_Block_Sales_Order_View $block
     *
     * @return string
     */
    private function _getUrl($block)
    {
        return $block->getUrl('*/sales_order_customer/create');
    }
}
