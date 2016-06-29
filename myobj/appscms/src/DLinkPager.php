<?php
namespace MYOBJ\appscms\src;

class DLinkPager extends \CLinkPager {
	public $template = '';
	public $html_params = array();


	public function run() {
		echo $this->createContent($this->template);
	}

	protected function createPageButton($html_elem,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected) {
			$class .= ' ' . ($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
		}
		$str = str_replace('URL_ID', $this->createPageUrl($page), $html_elem);
		$str = str_replace('ID_ELEM', $page+1, $str);
		return str_replace('{{CLASS}}', $class, $str);
	}

	protected function createContent($content) {

		if(($pageCount=$this->getPageCount())<=1) {
			return false;
		}

		list($beginPage,$endPage)=$this->getPageRange();
		$currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()

		// first page
		$button = '';
		if (isset($this->html_params['first']) !== false) {
			$button = $this->createPageButton($this->html_params['first'],0,$this->firstPageCssClass,$currentPage<=0,false);
		}
		$content = str_replace('{{IN_FIRST}}', $button, $content);

		// prev page
		$button = '';
		if (isset($this->html_params['prev']) !== false) {
			if(($page=$currentPage-1)<0) {
				$page=0;
			}
			$button = $this->createPageButton($this->html_params['prev'],$page,$this->previousPageCssClass,$currentPage<=0,false);
		}
		$content = str_replace('{{IN_PREV}}', $button, $content);

		// internal pages
		$button = '';
		if(isset($this->html_params['elements']) !== false) {
			for ($i = $beginPage; $i <= $endPage; ++$i) {
				$button .= $this->createPageButton($this->html_params['elements'], $i, $this->internalPageCssClass, false, $i == $currentPage);
			}
		}
		$content = str_replace('{{IN_ELEMENTS}}', $button, $content);

		// next page
		$button = '';
		if (isset($this->html_params['next']) !== false) {
			if(($page=$currentPage+1)>=$pageCount-1) {
				$page = $pageCount - 1;
			}
			$button = $this->createPageButton($this->html_params['next'],$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);
		}
		$content = str_replace('{{IN_NEXT}}', $button, $content);

		// yet page
		$button = '';
		if (isset($this->html_params['yet']) !== false && $pageCount > $currentPage+1) {
			if(($page=$currentPage+1)>=$pageCount-1) {
				$page = $pageCount - 1;
			}
			$button = $this->createPageButton($this->html_params['yet'],$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);
		}
		$content = str_replace('{{IN_YET}}', $button, $content);

		// last page
		$button = '';
		if (isset($this->html_params['last']) !== false) {
			$button = $this->createPageButton($this->html_params['last'],$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);
		}
		$content = str_replace('{{IN_LAST}}', $button, $content);

		return $content;
	}
}
