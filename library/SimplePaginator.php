<?php

class SimplePaginator {

    private $intCurrent;
    private $intLimit;
    private $intTotalData;
    private $arrArgs;

    public function paginate($intCurrentPage, $intTotalData, $intLimit = 10, $arrArgs = NULL) {
        $this->intLimit = $intLimit;
        $this->intTotalData = $intTotalData;

        if ($intCurrentPage < 1 or $intCurrentPage > $this->getTotalPage()) {
            $this->intCurrent = 1;
        } else {
            $this->intCurrent = $intCurrentPage;
        }

        if ($arrArgs === NULL) {
            $arrArgs = $_GET;
        }

        assert('is_array($arrArgs)');

        $this->arrArgs = $arrArgs;

        $this->arrArgs['limit'] = $intLimit;

        $this->arrArgs['page'] = $intCurrentPage;
    }

    private function getTotalPage() {
        return ceil($this->intTotalData / $this->intLimit);
    }

    private function getNext() {
        if ($this->intCurrent < $this->getTotalPage()) {
            return $this->intCurrent + 1;
        } else {
            return $this->intCurrent;
        }
    }

    private function getPrevious() {
        if ($this->intCurrent > 1) {
            return $this->intCurrent - 1;
        } else {
            return $this->intCurrent;
        }
    }

    private function getPages() {
        $bolFirst = false;
        $bolLast = false;
        $arrPage = array();
        if ($this->getTotalPage() == 0) {
            return false;
        } elseif ($this->getTotalPage() > 10) {
            for ($i = 1; $i <= $this->getTotalPage(); $i++) {
                if ($i == $this->intCurrent) {
                    $arrPage[] = array(
                        'link' => false,
                        'page' => $i
                    );
                } elseif ($i < $this->intCurrent - 3 && $i > 3 && $i < $this->intCurrent + 5) {
                    if (!$bolFirst) {
                        $arrPage[] = array(
                            'link' => false,
                            'page' => '...',
                        );
                    }
                    $bolFirst = true;
                } elseif ($i < $this->getTotalPage() - 3 && $i > $this->intCurrent + 5) {
                    if (!$bolLast) {
                        $arrPage[] = array(
                            'link' => false,
                            'page' => '...',
                        );
                    }
                    $bolLast = true;
                } else {
                    $arrPage[] = array(
                        'link' => true,
                        'page' => $i
                    );
                }
            }
        } else {
            for ($i = 1; $i <= $this->getTotalPage(); $i++) {
                if ($i == $this->intCurrent) {
                    $arrPage[] = array(
                        'link' => false,
                        'page' => $i
                    );
                } else {
                    $arrPage[] = array(
                        'link' => true,
                        'page' => $i
                    );
                }
            }
        }
        return $arrPage;
    }

    public function getDOMNode(DOMDocument $objDOM, $page_active = 1, $url) {

        $objRoot = $objDOM->createElement('div');
        $objRoot->setAttribute("class", "pagination");
        $objRoot->setAttribute("style", "display: none;");

        // prev
        $objRoot->appendChild($this->createPaginationLinkNode($objDOM, 1, '&lt;&lt;', 'prev', $page_active, $url));

        $pages = $this->getPages();
        if (!empty($pages)) {
            foreach ($this->getPages() as $name => $val) {
                $objRoot->appendChild($this->createPaginationLinkNode($objDOM, $val['page'], $val['page'], 'page', $page_active, $url));
            }
        }

        // last
        $objRoot->appendChild($this->createPaginationLinkNode($objDOM, $this->getTotalPage(), '&gt;&gt;', 'next', $page_active, $url));

        return $objRoot;
    }

    private function createPaginationLinkNode($objDOM, $intPage, $strLegend, $class, $page_active, $url) {

        $li = $objDOM->createElement('li');
        $li->setAttribute("class", $class);

        if (empty($page_active)) {
            if ($intPage == 1 && ($class != 'prev' && $class != 'next'))
                $li->setAttribute("class", 'active');
        }
        if (($intPage == $page_active && $class != 'prev') && ($intPage == $page_active && $class != 'next')) {
            $li->setAttribute('class', 'active');
        }

        $objLink = $objDOM->createElement('a', $strLegend);
        $objLink->setAttribute("href", $url . $intPage);

        $li->appendChild($objLink);

        return $li;
    }

}

?>
