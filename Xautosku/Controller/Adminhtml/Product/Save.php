<?php

namespace Zs\Xautosku\Controller\Adminhtml\Product;

use Braintree\Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Category;
use Zs\Xautosku\Helper\Data;

class Save
{
    protected $scopeConfig;
    protected $category;
    protected $helper;

    public function __construct(ScopeConfigInterface $scopeConfig,
                                Category $category,
                                Data $helper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->category = $category;
        $this->helper = $helper;
    }

    public function beforeExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $save)
    {
        $post = $save->getRequest()->getPostValue();
        $enabled = $this->scopeConfig->getValue(
            'autosku_section/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $generationType = $this->scopeConfig->getValue(
            'autosku_section/general/generate_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enabled && $generationType == 0) {
            $defaultSku = $this->_buildSku($post['product']);
            if($defaultSku && !empty($defaultSku)) {
                $post['product']['sku'] = $defaultSku;
                $save->getRequest()->setPostValue($post);
            }
        }
    }

    private function _buildSku($productData)
    {
        if (isset($productData['category_ids']) && !isset($productData['stock_data']['product_id'])) {
            $sku_prefix = $this->getCategoryPrefix($productData['category_ids']);
            if ($sku_prefix && strpos($productData['sku'], $sku_prefix . '-') === false) {
                $sku = $sku_prefix . '-' . ucwords($productData['name']);
                $skuToassign = $this->helper->slug($sku);
            } else {
                $skuToassign = $this->helper->slug($productData['name']);
            }
            return $skuToassign;
        }else{
            return false;
        }
    }


    private function getCategoryPrefix($categoryIds){
        if(count($categoryIds)) {
            $lastId = $categoryIds[count($categoryIds) - 1];
            try {
                $category = $this->category->load($lastId);
                if ($category->getId()) {
                    $prefix = $category->getSkuPrefix() ? $category->getSkuPrefix() : null;
                    return $prefix;
                }
            }catch (Exception $e){
                return false;
            }
        }
    }

}

