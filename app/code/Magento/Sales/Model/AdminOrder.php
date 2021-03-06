<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order control model
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model;

class AdminOrder
{
    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory]
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        $this->messageManager = $messageManager;
    }

    public function checkRelation(\Magento\Sales\Model\Order $order)
    {
        /**
         * Check customer existing
         */
        $customer = $this->_customerFactory->create()->load($order->getCustomerId());
        if (!$customer->getId()) {
            $this->messageManager->addNotice(__(' The customer does not exist in the system anymore.'));
        }

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($order->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = $this->_productFactory->create()->getCollection()
            ->addIdFilter($productIds)
            ->load();

        $hasBadItems = false;
        foreach ($order->getAllItems() as $item) {
            if (!$productCollection->getItemById($item->getProductId())) {
                $this->messageManager->addError(
                   __('The item %1 (SKU %2) does not exist in the catalog anymore.', $item->getName(), $item->getSku()
                ));
                $hasBadItems = true;
            }
        }
        if ($hasBadItems) {
            $this->messageManager->addError(
                __('Some items in this order are no longer in our catalog.')
            );
        }
        return $this;
    }

}
