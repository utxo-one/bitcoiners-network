<?php

namespace App\Enums;

enum FollowRequestStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
}