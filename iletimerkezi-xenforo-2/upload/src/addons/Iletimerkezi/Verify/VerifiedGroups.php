<?php

namespace Iletimerkezi\Verify;

use XF\Option\AbstractOption;
use XF\Option\UserGroup;

class VerifiedGroups extends AbstractOption
{
    public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
    {
        return UserGroup::renderSelect($option, $htmlParams);
    }
}