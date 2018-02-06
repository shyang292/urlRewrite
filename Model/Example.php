<?php
namespace Abm\urlRewrite\Model;
use Magento\Framework\Model\AbstractModel;

class Example extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
    $this->_init('Abm\urlRewrite\Model\Resource\Example');
    }
}