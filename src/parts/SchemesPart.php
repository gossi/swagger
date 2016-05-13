<?php

namespace gossi\swagger\parts;

use phootwork\collection\Map;
use phootwork\collection\ArrayList;

trait SchemesPart
{
    private $schemes;

    private function parseSchemes(Map $data)
    {
        $this->schemes = $data->get('schemes', new ArrayList());
    }

    /**
     * Return schemes.
     *
     * @return ArrayList
     */
    public function getSchemes()
    {
        return $this->schemes;
    }
}
