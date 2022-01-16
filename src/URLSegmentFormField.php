<?php

declare(strict_types=1);

namespace zauberfisch\URLSegmentExtension;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

class URLSegmentFormField extends TextField {
	protected string $urlPrefix;
	protected string $urlSuffix;
	private static $allowed_actions = [
		'suggest',
	];

	public function __construct(string $name, ?string $title = null, string $urlPrefix = "", string $urlSuffix = "") {
		$this->urlPrefix = $urlPrefix;
		$this->urlSuffix = $urlSuffix;
		parent::__construct($name, $title);
	}

	public function Value(): string {
		return rawurldecode($this->value);
	}

	public function getAttributes(): array {
		return array_merge(
			parent::getAttributes(),
			[
				'data-prefix' => $this->urlPrefix,
				'data-suffix' => $this->urlSuffix,
				'data-default-url' => "",
			]
		);
	}

	public function suggest(HTTPRequest $request): string {
		if (!$request->getVar('value')) {
			$this->httpError(405, _t('SilverStripe\\CMS\\Forms\\SiteTreeURLSegmentField.EMPTY', 'Please enter a URL segment or click cancel'));
		}
		$record = $this->getForm()->getRecord();
		Controller::curr()->getResponse()->addHeader('Content-Type', 'application/json');
		return json_encode(['value' => $record->generateURLSegment($request->getVar('value'))]);
	}

	public function Type(): string {
		return 'text urlsegment';
	}

	public function getURL(): string {
		return Controller::join_links($this->urlPrefix, $this->Value(), $this->urlSuffix);
	}

	public function Field($properties = []) {
		Requirements::add_i18n_javascript('silverstripe/cms: client/lang', false, true);
		return parent::Field($properties);
	}
}
