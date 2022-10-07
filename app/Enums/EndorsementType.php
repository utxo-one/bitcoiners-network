<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum EndorsementType: string
{
    use EnumToArray;
    
    case DEVELOPER = 'developer';
    case INFLUENCER = 'influencer';
    case ORGANIZATION = 'organization';
    case NODE_RUNNER = 'node-runner';
    case BIG_BRAINER = 'big-brainer';
    case SHITPOSTER = 'shitposter';
    case ANALYST = 'analyst';
    case CONTENT_CREATOR = 'content-creator';
    case ARTIST = 'artist';
    case MINER = 'miner';
}