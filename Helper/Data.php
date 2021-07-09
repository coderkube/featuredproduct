<?php
namespace Coderkube\Featuredproduct\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Catalog data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    /**
     * ScopeConfigInterface scopeConfig
     *
     * @var scopeConfig
     */
    protected $scopeConfig;
    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CustomerSession $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    public function allowExtension()
    {
        return  $this->scopeConfig->getValue('ck_profeature_config/general/status', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
 
    public function getPFCustomCss()
    {
        return  $this->scopeConfig->getValue('ck_profeature_config/pro_design/profeature_custom_css', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
