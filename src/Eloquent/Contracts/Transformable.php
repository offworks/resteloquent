<?php
namespace Restery\Eloquent\Contracts;

interface Transformable
{
    /**
     * Get fractal transformer class
     *
     * @return mixed
     */
    public function getTransformer();
}