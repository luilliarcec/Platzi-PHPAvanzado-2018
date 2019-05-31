<?php

namespace App\Models;

class BaseElement
{
    private $title;
    private $description;
    private $visible = true;
    private $months;

    public function __construct($title = '', $description = '')
    {
        $this->setTitle($title);
        $this->description = $description;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title == '' ? 'N/A' : $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible): void
    {
        $this->visible = $visible;
    }

    public function getMonths()
    {
        return $this->months;
    }

    public function setMonths($months): void
    {
        $this->months = $months;
    }

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