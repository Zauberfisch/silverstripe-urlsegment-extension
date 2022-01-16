# SilverStripe URLSegment Extension

Utility/DataExtension for adding an URLSegment field to a DataObject. Also includes a FormField.  
  
Currently hardcoded to create the field `URLSegment` and use `Title` as a source. 

## Maintainer Contact

* Zauberfisch <code@zauberfisch.at>

## Requirements

* php >=7.4
* silverstripe/framework >=4

## Installation

* `composer require "zauberfisch/silverstripe-urlsegment-extension"`
* rebuild manifest (flush)

## Documentation

```php
<?php

/**
* @property string $Title
* @method \SilverStripe\CMS\Model\SiteTree TeamPage()
 */
class TeamMember extends \SilverStripe\ORM\DataObject {
    private static $db = [
        'Title' => \SilverStripe\ORM\FieldType\DBVarchar::class,
    ];
    private static $has_one = [
        'TeamPage' => \SilverStripe\CMS\Model\SiteTree::class,
    ];

    private static $extensions = [
        \zauberfisch\URLSegmentExtension\URLSegmentDataExtension::class,
    ];

    public function getCMSFields() {
        $urlPrefix = $this->TeamPage()->Link();
        $urlSuffix = "";
        $fields = parent::getCMSFields();
        $fields->removeByName('URLSegment');
        $fields->addFieldsToTab( 'Root.Main', [
            new \zauberfisch\URLSegmentExtension\URLSegmentFormField('URLSegment', $this->fieldLabel('URLSegment'), $urlPrefix, $urlSuffix),
        ]);
        return $fields;
    }
}
```
