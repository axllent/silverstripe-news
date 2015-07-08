<?php
/**
 * SilverStripe News Module
 * =========================
 *
 * News article DataObject
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */

class NewsArticle extends DataObject {

	public static $rss_thumb_width = 250;

	public static $default_sort = array(
		'"Date"' => 'DESC',
		'"ID"' => 'DESC'
	);

	private static $db = array(
		'Title' => 'Varchar(200)',
		'Date' => 'Datetime',
		'Content' => 'HTMLText'
	);

	private static $has_one = array(
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

		// Set today's date if empty
		if (!$this->Date) {
			$this->Date = date('Y-m-d H:i:s');
		}

		$fields->dataFieldByName('Title')->setTitle('Article Title');

		$fields->addFieldToTab('Root.Main',
			LiteralField::create('DateInfo', '<p>Dates set in the future will not be visible until that day/time.</p>'),
			'Date'
		);

		$fields->addFieldToTab('Root.Main',
			$datetime = DatetimeField::create('Date', 'Article Date / Time (24h)'),
			'Content'
		);
		$datetime->getDateField()->setConfig('showcalendar', 1);
		$datetime->getTimeField()->setConfig('timeformat', 'HH:mm');

		$fields->addFieldToTab('Root.Main',
			$ul = UploadField::create('Thumbnail', 'Article Image'),
			'Content'
		);
		$ul->setFolderName('NewsArticles');
		$ul->setAllowedFileCategories('image');

		return $fields;
	}

	/**
	 * Generate RSS Content and allow extending
	 */
	public function RssContent() {
		$thumbnail = false;
		$t = $this->Thumbnail();
		if ($t->ID) {
			$thumbnail = '<p><a href="' . $t->URL . '"><img src="' . $t->setWidth(self::$rss_thumb_width)->URL .
				'" alt="'. htmlspecialchars($t->Title) . '" /></a></p>';
		}
		$html = DBField::create_field(
			'HTMLText',
			$thumbnail . $this->Content
		);
		$this->extend('updateRssContent', $html);
		return $html;
	}

	/* Create the item link */
	public function Link() {
		return $this->Parent()->Link() . 'article/' . $this->Parent()->generateURLSegment($this->Title) . '-' . $this->ID . '/';
	}

	public function validate() {
		$valid = parent::validate();
		if (trim($this->Title) == '')
			$valid->error('Please give your article a title');
		return $valid;
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

}