<?php
namespace Abm\urlRewrite\Model\Resource\Example;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
    $this->_init(
        'Abm\urlRewrite\Model\Example',
        'Abm\urlRewrite\Model\Resource\Example'
    );

    }
}