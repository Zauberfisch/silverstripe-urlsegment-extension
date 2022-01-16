<?php

declare(strict_types=1);

namespace zauberfisch\URLSegmentExtension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\View\Parsers\URLSegmentFilter;

class URLSegmentDataExtension extends DataExtension {
	private static $db = [
		'URLSegment' => DBVarchar::class . "(255)",
	];
	private static $indexes = [
		'URLSegment' => true,
	];

	public function onBeforeWrite() {
		$this->owner->URLSegment = $this->generateURLSegment($this->owner->URLSegment ?: $this->owner->Title);
		parent::onBeforeWrite();
	}

	public function generateURLSegment(string $value): string {
		$filter = URLSegmentFilter::create();
		$urlSegment = $filter->filter($value);
		$this->owner->extend('updateURLSegment', $urlSegment, $value);
		if (!$urlSegment || $urlSegment == '-' || $urlSegment == '-1') {
			$urlSegment = md5(uniqid((string)$this->owner->ID));
		}
		$list = $this->owner->get();
		if ($this->owner->isInDB()) {
			$list = $list->exclude('ID', $this->owner->ID);
		}
		$validUrlSegment = $urlSegment;
		$count = 2;
		while ($list->filter('URLSegment', $validUrlSegment)->exists()) {
			$validUrlSegment = $filter->filter($urlSegment . '-' . $count++);
		}
		return $validUrlSegment;
	}
}
