<?php

use BaseTree\Models\Model;
use Illuminate\Database\Eloquent\Collection;

if ( ! function_exists('create')) {
    /**
     * @param string $class
     * @param integer|null $quantity
     * @param array $attributes
     * @return Model|Collection
     */
    function create($class, $quantity = null, $attributes = [])
    {
        return factory($class, $quantity)->create($attributes);
    }
}

if ( ! function_exists('make')) {
    /**
     * @param string $class
     * @param integer|null $quantity
     * @param array $attributes
     * @return Model|Collection
     */
    function make($class, $quantity = null, $attributes = [])
    {
        return factory($class, $quantity)->make($attributes);
    }
}