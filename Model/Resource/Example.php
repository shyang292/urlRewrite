<?php
namespace Abm\urlRewrite\Model\Resource;
class Example extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
    $this->_init('catalog_product_entity', 'entity_id');   //here id is the primary key of custom table
    }
}