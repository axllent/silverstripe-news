<?php
/**
 * SilverStripe News Module
 * =========================
 *
 * Module for a news page containing individual news articles (like a blog).
 *
 * License: MIT-style license http://opensource.org/licenses/MIT
 * Authors: Techno Joy development team (www.technojoy.co.nz)
 */

class NewsPage extends Page {

	static $articles_per_page = 10;

	static $icon = 'silverstripe-news/images/news.png';

	static $description = 'News page with articles';

	static $db = array();

	public static $has_many = array(
		'NewsArticles' => 'NewsArticle'
	);

	public function getCmsFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('Content');
		$gridField = new GridField('NewsItems', false, $this->NewsArticles(),
			new GridFieldConfig_RecordEditor(self::$articles_per_page)
		);
		$fields->addFieldToTab('Root.Main', $gridField);
		return $fields;
	}

	public static function getArticlesPerPage() {
		return self::$articles_per_page;
	}

}

class NewsPage_Controller extends Page_Controller {

	public static $allowed_actions = array('ViewArticle', 'rss');

	public static $url_handlers = array(
		'article//$ArticleID/$UrlName' => 'ViewArticle',
	);

	public function init() {
		RSSFeed::linkToFeed($this->Link() . "rss");
		parent::init();
	}

	public function index($request) {
		$this->PaginatedArticles = new PaginatedList($this->NewsArticles()->filter(array('Date:LessThan' => $this->cur_time())), $request);
		$this->PaginatedArticles->setPageLength($this->getArticlesPerPage());
		return $this;
	}

	public function ViewArticle($request){
		$id = $request->param('ArticleID');
		$UrlName = $request->param('UrlName');
		$this->Article = $this->NewsArticles()->filter(array(
			'ID' => $id,
			'Date:LessThan' => $this->cur_time()
		))->First();
		/* Article not found */
		if (!$this->Article) {
			return $this->httpError(404);
		}
		/* Article on different page? */
		if ($this->Article->ParentID != $this->ID || $UrlName != $this->generateURLSegment($this->Article->Title))
			return $this->redirect($this->Article->Link(), 301);

		/* Override MetaData
		 * Only works if template calls the $Title individually and not through $MetaTags
		 * Title not overwritten else $Breadcrumbs does not work
		 */
		$this->dataRecord->MetaTitle = '';
		$this->dataRecord->MetaDescription = '';
		$this->dataRecord->MetaKeywords = '';

		return $this->renderWith(array('Article_view','Page'));
	}

	/*
	 * Override BreadCrumbs to add Article to list
	 */
	public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
		$page = $this->Owner;
		$pages = array();
		if ($this->Article) $pages[] = $this->Article;
		while(
			$page
 			&& (!$maxDepth || count($pages) < $maxDepth)
 			&& (!$stopAtPageType || $page->ClassName != $stopAtPageType)
 		) {
			if($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
				$pages[] = $page;
			}
			$page = $page->Parent;
		}
		$template = new SSViewer('BreadcrumbsTemplate');
		return $template->process($this->customise(new ArrayData(array(
			'Pages' => new ArrayList(array_reverse($pages))
		))));
	}

	/*
	 * Generate current date to ensure posts in the future are not shown
	 */
	public function cur_time() {
		return date('Y-m-d H:i:s');
	}

	/*
	 * Override Title to cater for DataObject
	 */
	public function Title() {
		return $this->Article ? $this->Article->Title : $this->Title;
	}

	/*
	 * Generate a RSS feed of news page
	 */
	public function rss() {
		$siteConfig = SiteConfig::current_site_config();
		$rss = new RSSFeed(
			$this->NewsArticles()->filter(array('Date:LessThan' => $this->cur_time())),
			$this->Link(),
			$siteConfig->Title
		);
		return $rss->outputToBrowser();
	}

}
