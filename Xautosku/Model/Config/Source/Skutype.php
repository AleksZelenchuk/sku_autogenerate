<?php

namespace Zs\Xautosku\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Skutype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Random Number')], ['value' => 0, 'label' => __('Category\'s Prefix And Product Name')], ['value' => 2, 'label' => __('Custom Prefix')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Category\'s Prefix And Product Name'), 1 => __('Random Number'), 2 => __('Custom Prefix')];
    }
}
