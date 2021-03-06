<?php

namespace NobrainerWeb\Solr\Index;

use DNADesign\Elemental\Models\ElementalArea;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\FullTextSearch\Search\Variants\SearchVariantVersioned;
use SilverStripe\Versioned\Versioned;

class SolrIndex extends \SilverStripe\FullTextSearch\Solr\SolrIndex
{
    /**
     * @config bool
     */
    private static $add_default_fields = true;

    /**
     * Add your own fields here to be indexed
     *
     * @config array
     */
    private static $custom_fields = [];

    /**
     * Add your own classes here to be indexed
     *
     * @config array
     */
    private static $classesToIndex = [
        SiteTree::class
    ];

    public function init()
    {
        $this->addClasses();

        $this->addDefaultFields();

        $this->addCustomFields();

        /** @see ElementalArea::getElementsForSearch */
        if (class_exists(ElementalArea::class)) {
            $this->addFulltextField('ElementsForSearch');
        }

        // dont index draft
        $this->excludeVariantState([SearchVariantVersioned::class => Versioned::DRAFT]);

        // TODO does not seem to work
        // respect ShowInSearch 
        //$this->addFilterField('ShowInSearch');
    }

    protected function addDefaultFields()
    {
        if (!self::config()->add_default_fields) {
            return;
        }

        $this->addFulltextField('Title');
        $this->addFulltextField('MenuTitle');
        $this->addFulltextField('Summary');
    }

    protected function addClasses()
    {
        $classes = self::config()->classesToIndex;
        if (empty($classes)) {
            return;
        }

        foreach ($classes as $className) {
            $this->addClass($className);
        }
    }

    protected function addCustomFields()
    {
        $customFields = self::config()->custom_fields;
        if (empty($customFields)) {
            return;
        }

        foreach ($customFields as $field) {
            $this->addFulltextField($field);
        }
    }
}