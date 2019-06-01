<?php


namespace App\Models;


use App\Traits\HasDefaultImage;
use Illuminate\Database\Eloquent\Model;
//require_once('BaseElement.php');
//require_once('Printable.php');

class Project extends Model
{
    use HasDefaultImage;
    protected $table = 'projects';
}