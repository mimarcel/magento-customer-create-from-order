<?php
class Max_CustomerCreateFromOrder_Adminhtml_Sales_Order_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function createAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $this->_getSession()->addSuccess($this->__('Customer was successfully created.'));
            } catch (\Exception $ex) {
                $this->_getSession()->addError($this->__('An unexpected error occurred during customer creation.'));
                Mage::logException($ex);
            }

            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Initialize order model instance
     *
     * @return false|Mage_Sales_Model_Order
     *
     * @see Mage_Adminhtml_Sales_OrderController::_initOrder
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/sales_order/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return $order;
    }
}
