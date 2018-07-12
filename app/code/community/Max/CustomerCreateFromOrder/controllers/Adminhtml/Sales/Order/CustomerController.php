<?php
class Max_CustomerCreateFromOrder_Adminhtml_Sales_Order_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function createAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $this->_getConnection()->beginTransaction();
                $customer = $this->_createCustomerFromOrder($order);
                $this->_linkCustomerToOrder($customer, $order);

                $this->_getConnection()->commit();
                // @todo Send email to customer
                $this->_getSession()->addSuccess($this->__('Customer was successfully created.'));
            } catch (Mage_Core_Exception $ex) {
                $this->_getConnection()->rollBack();
                $this->_getSession()->addError($ex->getMessage());
            } catch (Exception $ex) {
                $this->_getConnection()->rollBack();
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

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_write');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return Mage_Customer_Model_Customer
     *
     * @throws Exception
     */
    protected function _createCustomerFromOrder($order)
    {
        /* @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $this->_copyCustomerAttributesFromOrder($customer, $order);
        $this->_generatePassword($customer);

        Mage::dispatchEvent('sales_create_customer_from_order', array('customer' => $customer, 'order' => $order));

        $customer->save();

        if (!$customer->getId()) {
            throw new \Exception($this->__('Unable to create customer.'));
        }

        $this->_createCustomerAddressesFromOrder($customer, $order);

        return $customer;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Sales_Model_Order $order
     */
    private function _copyCustomerAttributesFromOrder($customer, $order)
    {
        $customer->addData(array(
            'dob' => $order->getCustomerDob(),
            'email' => $order->getCustomerEmail(),
            'firstname' => $order->getCustomerFirstname(),
            'gender' => $order->getCustomerGender(),
            'group_id' => $this->_getCustomerCreateFromOrderHelper()->getCustomerGroupId(),
            'lastname' => $order->getCustomerLastname(),
            'middlename' => $order->getCustomerMiddlename(),
            'prefix' => $order->getCustomerPrefix(),
            'store_id' => $order->getStoreId(),
            'suffix' => $order->getCustomerSuffix(),
            'taxvat' => $order->getCustomerTaxvat(),
            'website_id' => $order->getStore()->getWebsiteId(),
        ));
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _generatePassword($customer)
    {
        // @todo Set password
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Sales_Model_Order $order
     */
    protected function _createCustomerAddressesFromOrder($customer, $order)
    {
        // @todo add Addresses
    }

    /**
     * @return Max_CustomerCreateFromOrder_Helper_Data
     */
    private function _getCustomerCreateFromOrderHelper()
    {
        return Mage::helper('customerCreateFromOrder');
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Sales_Model_Order $order
     */
    private function _linkCustomerToOrder($customer, $order)
    {
        $order->setCustomerId($customer->getId());
        $order->setCustomerIsGuest(false);
        $order->setCustomerGroupId($customer->getGroupId());

        Mage::dispatchEvent('sales_link_customer_to_order', array('customer' => $customer, 'order' => $order));

        $order->save();
    }
}
