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
 * @package     Magento_CatalogSearch
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layered Navigation block for search
 *
 */
namespace Magento\CatalogSearch\Block;

class Layer extends \Magento\Catalog\Block\Layer\View
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Catalog search layer
     *
     * @var \Magento\CatalogSearch\Model\Layer
     */
    protected $_catalogSearchLayer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CatalogSearch\Model\Layer $catalogLayer
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CatalogSearch\Model\Layer $catalogLayer,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_coreRegistry = $registry;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($context, $catalogLayer, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_coreRegistry->register('current_layer', $this->getLayer(), true);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        $this->_attributeFilterBlockName = 'Magento\CatalogSearch\Block\Layer\Filter\Attribute';
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $_isLNAllowedByEngine = $this->_engineProvider->get()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)$this->_storeManager->getStore()
            ->getConfig(\Magento\CatalogSearch\Model\Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount
            || ($availableResCount > $this->getLayer()->getProductCollection()->getSize())
        ) {
            return parent::canShowBlock();
        }
        return false;
    }
}
