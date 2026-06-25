<?php

namespace Modules\Library\Enums;

enum BookCopyStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Loaned = 'loaned';
    case Lost = 'lost';
    case Damaged = 'damaged';
}
