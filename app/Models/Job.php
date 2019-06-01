<?php


namespace App\Models;


use App\Traits\HasDefaultImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Job extends Model
{
    use HasDefaultImage;
    use SoftDeletes;

    protected $table = 'jobs';

    function getDurationAsString()
    {
        $years = floor($this->months / 12);
        $extraMonths = $this->months % 12;

        if ($years < 1) {
            return "$extraMonths months";
        } elseif ($extraMonths < 1) {
            return "$years years";
        } else {
            return "$years years $extraMonths months";
        }
    }
}