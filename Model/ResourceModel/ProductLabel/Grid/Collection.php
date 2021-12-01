<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ProductLabel
 * @author    Houda EL RHOZLANE <houda.elrhozlane@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ProductLabel\Model\ResourceModel\ProductLabel\Grid;

use \Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Product Label Grid Collection
 *
 * @category  Smile
 * @package   Smile\ProductLabel
 * @author    Houda EL RHOZLANE <houda.elrhozlane@smile.fr>
 */
class Collection extends \Smile\ProductLabel\Model\ResourceModel\ProductLabel\Collection implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface[]
     */
    private $aggregations;

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritDoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setModel('Magento\Framework\View\Element\UiComponent\DataProvider\Document');
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->joinInner(
            ['ea' => $this->getTable('eav_attribute')],
            'ea.attribute_id = main_table.attribute_id',
            ['frontend_label']
        )->joinLeft(
            ['cpei' => $this->getTable('catalog_product_entity_int')],
            'cpei.attribute_id = ea.attribute_id',
            ['value' => 'cpei.value']
        )->group('main_table.product_label_id');
        $storeCondition = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        if ($this->getFilter('store')) {
            $storeId = current($this->getStoreIds());

            $storeCondition = $this->getConnection()->getIfNullSql(
                "cpei.store_id",
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        }

        $this->getSelect()->where('cpei.store_id = ? OR cpei.store_id IS NULL', $storeCondition);
		
        return $this;
    }
}
