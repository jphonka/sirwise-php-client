<?php

namespace SirWise\Api;

/**
 * Api interface.
 *
 * @author Sami Starast <sami.starast@voltiosoft.com>
 */
interface ApiInterface
{
    public function getPerPage();
    public function setPerPage($perPage);
}
