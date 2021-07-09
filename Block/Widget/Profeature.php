<?php

namespace Coderkube\Featuredproduct\Block\Widget;

class Profeature extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/featureslider.phtml';

    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_LIMIT = 10;
    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $_pager;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->_imageHelper = $context->getImageHelper();
        $this->reviewRenderer = $context->getReviewRenderer();
        $this->_cartHelper = $context->getCartHelper();
        parent::__construct(
            $context,
            $data
        );
    }
    /**
     * Image helper Object
     */
    public function imageHelperObj()
    {
        return $this->_imageHelper;
    }
    /**
     * get featured product collection
     */
    public function getFeaturedProduct()
    {
        $limit = $this->getProductLimit();

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->setPageSize($limit)
            ->addAttributeToSelect('*');

        $collection->addAttributeToFilter('is_profeature', '1');
        return $collection;
    }

    /**
     * Prepare collection with new products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->getFeaturedProduct($this->getFeaturedProduct());
        return parent::_beforeToHtml();
    }
    
    /**
     * Get product reviews summary
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->reviewRenderer->getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Get the configured limit of products
     * @return int
     */
    public function getProductLimit()
    {
        if ($this->getData('productlimit') == '') {
            return DEFAULT_PRODUCTS_LIMIT;
        }
        return $this->getData('productlimit');
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }
    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
