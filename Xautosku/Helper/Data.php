<?php

namespace Zs\Xautosku\Helper;

use Braintree\Exception;
use \Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $collection;

    public function __construct(Context $context, Product $collection)
    {
        parent::__construct($context);
        $this->collection = $collection;
    }

    public function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    public function slug($str)
    {
        //$str = strtolower(trim($str));
        $str = preg_replace('/[^a-zA-Z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        return strtolower($str);
    }

    public function getProductBySku($sku)
    {
        if ($this->collection->getIdBySku($sku)){
            return true;
        }
    }
}
