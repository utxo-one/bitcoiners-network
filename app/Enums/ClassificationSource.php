<?php

namespace App\Enums;

enum ClassificationSource: string
{
    case CRAWLER = 'crawler';
    case LIGHTNING = 'lightning';
    case VOTE = 'vote';
}