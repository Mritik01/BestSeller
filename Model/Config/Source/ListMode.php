<?php
  
declare(strict_types=1);

namespace Bluethink\BestSeller\Model\Config\Source;

class ListMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'best_seller', 'label' => __('Best Seller')],
            ['value' => 'feature', 'label' => __('Feature')],
            ['value' => 'new_arrivals', 'label' => __('New Arrivals Products')],
        ];
    }
}
