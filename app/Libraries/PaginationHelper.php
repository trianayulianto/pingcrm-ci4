<?php

namespace App\Libraries;

use CodeIgniter\Pager\Pager;

class PaginationHelper
{
    /**
     * @param Pager $pager
     * @param string $group
     * @return array
     */
    public static function getLinks(Pager $pager, $group = 'default')
    {
        $pageCount = $pager->getPageCount($group);

        $links = [];
        if ($pageCount > 1) {
            $links[] = [
                'url' => $pager->getPreviousPageURI($group),
                'label' => 'Previous',
                'active' => false
            ];

            for ($i = 1; $i <= $pageCount; $i++) {
                $links[] = [
                    'url' => $pager->getPageURI($i, $group),
                    'label' => $i,
                    'active' => $pager->getCurrentPage($group) == $i
                ];
            }

            $links[] = [
                'url' => $pager->getNextPageURI($group),
                'label' => 'Next',
                'active' => false
            ];
        }

        return $links;
    }
}
