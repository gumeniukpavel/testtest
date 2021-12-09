<?php

namespace App\Constant;

use App\Constant\Common\Enum;

class OrderType extends Enum
{
    public static OrderType $Desc;
    public static OrderType $Asc;
}

OrderType::$Desc = new OrderType('DESC', 'DESC');
OrderType::$Asc = new OrderType('ASC', 'ASC');

OrderType::init();
