<?php

namespace App\Constant;

use App\Constant\Common\Enum;

class SortUserProfile extends Enum
{
    public static SortUserProfile $ClientName;
    public static SortUserProfile $ClientEmail;
    public static SortUserProfile $UniqueIdentityNumber;
    public static SortUserProfile $Notes;
}

SortUserProfile::$ClientName = new SortUserProfile ('ClientName', 'ClientName');
SortUserProfile::$ClientEmail = new SortUserProfile ('ClientEmail', 'ClientEmail');
SortUserProfile::$UniqueIdentityNumber = new SortUserProfile ('UniqueIdentityNumber', 'UniqueIdentityNumber');
SortUserProfile::$Notes = new SortUserProfile ('Notes', 'Notes');

SortUserProfile::init();
