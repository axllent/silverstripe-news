<?php

class NewsArticle extends DataObject {

	public static $default_sort = "\"Date\" DESC, \"ID\" DESC";

	public static $db = array(
		'Title' => 'Varchar(200)',
		'Date' => 'Datetime',
		'Content' => 'HTMLText'
	);

	public static $has_one = array(
		'Parent' => 'SiteTree',
		'Thumbnail' => 'Image'
	);

	public static $summary_fields = array(
		'Date',
		'Title'
	);

	public function getCmsFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('ParentID');
		$fields->removeByName('Content');
		$fields->removeByName('Date');
		$fields->removeByName('Thumbnail');
		$fields->removeByName('Title');

		// Set today's date if empty
		if (!$this->Date)
			$this->Date = date('Y-m-d H:i:s');

		$fields->addFieldsToTab('Root.Main', array(
			new TextField('Title', 'Article Title'),
			new LiteralField('DateInfo', '<p>Dates set in the future will not be visible until that day/time.</p>'),
			$date = new DatetimeField('Date', 'Article Date / Time (24h)'),
			$ul = new UploadField('Thumbnail', 'Article Image'),
			$editor = new HtmlEditorField('Content','Article Content')
		));

		$date->getDateField()->setConfig('showcalendar', true);
		$date->getTimeField()->setConfig('timeformat', 'HH:mm');

		// Use NewsArticles subfolder
		$ul->setFolderName('NewsArticles');
		$ul->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));

		$editor->setRows(25);

		return $fields;
	}

	/* Permissions */
	public function canView($member = null) {
		return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
	}
	public function canEdit($member = null) {
		return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
	}
	public function canCreate($member = null) {
		return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
	}
	public function canDelete($member = null) {
		return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
	}

	/* Create the item link */
	public function Link() {
		return $this->Parent()->Link() . 'article/' . $this->ID . '/' . $this->Parent()->generateURLSegment($this->Title) . '/';
	}

	public function validate() {
		$valid = parent::validate();
		if (trim($this->Title) == '')
			$valid->error("Please give your article a title");
		return $valid;
	}

}