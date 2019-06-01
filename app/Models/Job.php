<?php


namespace App\Models;


use App\Traits\HasDefaultImage;
use Illuminate\Database\Eloquent\Model;
//require_once('BaseElement.php');
//require_once('Printable.php');

class Job extends Model
{
    use HasDefaultImage;
    protected $table = 'jobs';
//    public $timestamps = false; //Quita la obligación a created_at y updated_at

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