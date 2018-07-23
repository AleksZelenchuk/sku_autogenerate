<?php
namespace Zs\Xautosku\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zs\Xautosku\Helper\Data;

class Modifysku extends AbstractModifier
{
    protected $productCollection;
    protected $scopeConfig;
    protected $helper;

    public function __construct(Collection $collection, ScopeConfigInterface $scopeConfig, Data $helper)
    {
        $this->productCollection = $collection;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public function modifyMeta(array $meta)
    {

        $enabled = $this->scopeConfig->getValue(
            'autosku_section/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $generationType = $this->scopeConfig->getValue(
            'autosku_section/general/generate_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $customPrefix = $this->scopeConfig->getValue(
            'autosku_section/general/custom_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $increment = $this->scopeConfig->getValue(
            'autosku_section/general/increment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $length = $this->scopeConfig->getValue(
            'autosku_section/general/sku_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if($enabled){

            $defaultSku = $this->_buildSku($generationType, $increment, $length, $customPrefix);

            $meta['product-details']
            ['children']
            ['container_sku']
            ['children']
            ['sku']
            ['arguments']
            ['data']
            ['config']
            ['value'] = $defaultSku;
        }
        return $meta;
    }


    public function modifyData(array $data)
    {
        return $data;
    }

    private function _buildSku($generationType, $_increment, $_length, $customPrefix){

        $skuToassign = '';

        if($generationType == 1){
            $skuToassign = $this->generateRandomSku($_increment, $_length);
        }elseif ($generationType == 2){
            $collection = $this->productCollection
                ->addAttributeToFilter(
                    [
                        ['attribute' => 'sku', 'like' => $customPrefix.'%']
                    ]
                )
                ->setOrder('entity_id','DESC')
                ->setPageSize(1)
                ->load();


            if($collection->getSize() > 0){

                $product  			= 	$collection->getFirstItem();
                $lastMacthedSku 	=	$product->getSku();
                $findLastIncrement 	=   (int) preg_replace('/[^0-9]/', '', $lastMacthedSku);
                $nextInrement   	= 	++$findLastIncrement;
                $incrementLength 	= 	strlen($findLastIncrement) + strlen($customPrefix);//
                $skuToassign 		=	$customPrefix.str_pad(
                        $skuToassign,
                        ($_length - $incrementLength ),
                        0
                    ).$nextInrement;

            }else{

                $subtractLength = strlen($customPrefix) + strlen($_increment);
                $skuToassign = $customPrefix.str_pad(
                        $skuToassign,
                        ($_length - $subtractLength ),
                        0
                    ).$_increment;

            }
        }

        return $skuToassign;
    }
    protected function generateRandomSku($_increment, $_length){
        $subtractLength = $_length - strlen($_increment);
        $skuToCheck = $_increment . $this->helper->randomNumber($subtractLength);
        $productCheck = $this->helper->getProductBySku($skuToCheck);
        if(!$productCheck){
            return $skuToCheck;
        }else{
            $this->generateRandomSku($_increment, $_length);
        }
    }
}