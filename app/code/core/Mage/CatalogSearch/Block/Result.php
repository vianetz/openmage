<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product search result block
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class Mage_CatalogSearch_Block_Result extends Mage_Core_Block_Template
{
    /**
     * Catalog Product collection
     *
     * @var Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected $_productCollection;

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return $this->helper('catalogSearch')->getQuery();
    }

    /**
     * Prepare layout
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    protected function _prepareLayout()
    {
        $title = $this->__("Search results for: '%s'", $this->helper('catalogSearch')->getEscapedQueryText());

        // add Home breadcrumb
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label' => $title,
                'title' => $title
            ));
        }
        $this->getLayout()->getBlock('head')->setTitle($title);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getListBlock()
    {
        return $this->getChild('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListOrders() {
        $this->getListBlock()
            ->setAvailableOrders(array(
                'relevance' => $this->__('Relevance'),
                'name'      => $this->__('Name'),
                'price'     => $this->__('Price'),
            ))
            ->setDefaultDirection('desc');
        return $this;
    }

    /**
     * Set available view mode
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListModes() {
        $this->getListBlock()
            ->setModes(array(
                'grid' => $this->__('Grid'),
                'list' => $this->__('List'))
            );
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection() {
        $this->getListBlock()
           ->setCollection($this->_getProductCollection());
       return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
/*
            $this->_productCollection = Mage::getResourceModel('catalogsearch/fulltext_collection')
                ->addSearchFilter($this->helper('catalogSearch')->getEscapedQueryText())
                ->setStore(Mage::app()->getStore())
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addUrlRewrite();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);*/
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->_getQuery()->setNumResults($size);
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        if (Mage::helper('catalogSearch')->isMinQueryLength()) {
            return Mage::helper('catalogSearch')->__('Minimum Search query length is %s', $this->_getQuery()->getMinQueryLenght());
        }
        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return Mage::helper('catalogSearch')->getNoteMessages();
    }
}