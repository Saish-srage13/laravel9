<?php

namespace App\Indices;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Illuminate\Support\Str;

abstract class BaseIndex implements ElasticsearchHelperInterface
{
    protected string $indexName;

    public function __construct()
    {
        $class = (new \ReflectionClass($this))->getShortName();
        $this->indexName = Str::snake($class);
    }

    /**
     * Scpoe for improvement -
     * 
     * This class can have an implmentation that can standardize the params for creating search related query strings  
     */
}