<?php
/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Abm\urlRewrite\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    private $arr1;
    /**
     * Init
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(\Magento\Framework\App\State $state, \Abm\urlRewrite\Model\ExampleFactory $db)
    {
        //$this->moduleHelper = $Data;
         $state->setAreaCode('frontend'); // or 'adminhtml', depending on your needs
        $this->_exampleFactory = $db;

    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $prodCollection=$this->_exampleFactory->create()->getCollection();
        $urlRewriteId = 5; // 1~4 has been occupied by dedfault
        $data = array();//save data to store in table 'url_rewrite'
        $data2 = array(); // save data to store in table 'catalog_url_rewrite_product_category'
        $count = 1; $total = sizeof($prodCollection);
        foreach ($prodCollection as $d) //get all product from table 'catalog_product_entity'
        {
            echo $count.'/'.$total; echo "\n";
            $count++;
            $tmpArray = array();
            $tmpArray2= array();
            $productId = $d->getId();; // YOUR PRODUCT ID
            $product = $productRepository->getById($productId);
            $categoryIds = $product->getCategoryIds();
            $productName = $product->getName();
            $productSku  = $product->getSku();
            $categories = $categoryCollection->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', $categoryIds);
            $tmpArray['entity_type'] = 'product';
            $tmpArray['entity_id'] = $productId;
            $tmpArray['request_path'] = $productName;
            //$tmpArray['target_path'] = 'product';
            $tmpArray['redirect_type'] = 0;
            $tmpArray['store_id'] = 1;
            $tmpArray['is_autogenerated'] = 1;
            $count2 = 1;
            foreach ($categories as $category) {
                $categoryId = $category->getId();
                $categoryName = $category->getName();
                if($count2 == 1){
                    $tmpArray['url_rewrite_id'] = $urlRewriteId++;
                    $tmpArray['target_path'] = str_replace(' ', '-', 'catalog/product/view/id/'.$productId);
                    $tmpArray['request_path'] = str_replace(' ', '-', $productName.'-'.$productSku.'.html');
                    array_push($data, $tmpArray);
                    $count2++;
                }
                $tmpArray2['url_rewrite_id']= $urlRewriteId;
                $tmpArray2['category_id']= $categoryId;
                $tmpArray2['product_id']= $productId;
                $tmpArray['url_rewrite_id'] = $urlRewriteId++;
                $tmpArray['target_path'] = str_replace(' ', '-', 'catalog/product/view/id/'.$productId.'/category/'.$categoryId);
                $tmpArray['request_path'] = str_replace(' ', '-',  $categoryName.'/'.$productName.'-'.$productSku.'.html');
                array_push($data, $tmpArray);
                array_push($data2, $tmpArray2);
            }
        }
        //
        $this->insertData($setup, 'url_rewrite', $data, 40000);
        $this->insertData($setup, 'catalog_url_rewrite_product_category', $data2, 40000);
    }

    public function insertData($setup, $tableName, $arr, $importedNum){
        echo "begin importing into ".$tableName; echo "\n";
        $dataSize = sizeof($arr);
        //echo "arr is the following: "; echo "\n";
        //print_r($arr); echo "\n";
        //$importedNum: the imported number each time 
        $number = ceil($dataSize/$importedNum);
        $count = 1;
        foreach (array_chunk($arr, $importedNum) as $subData) {
            echo $count++.'/'.$number; echo "\n";
            //echo "subData is the following: "; echo "\n";
            //print_r($subData); echo "\n";
            $setup->startSetup();
            $table = $setup->getTable($tableName);
            $setup
                ->getConnection()
                ->insertMultiple($table, $subData);
            $setup->endSetup();
        }
    } 

}