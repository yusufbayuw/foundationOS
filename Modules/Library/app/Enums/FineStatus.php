<?php

namespace Modules\Library\Enums;

enum FineStatus: string
{
    case None = 'none';
    case Unpaid = 'unpaid';
    case Paid = 'paid';
}
