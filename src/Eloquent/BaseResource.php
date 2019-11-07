<?php

namespace Restery\Eloquent;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

abstract class BaseResource extends TransformerAbstract
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function store(array $data)
    {
        return get_class($this->model)::create($data);
    }

    public function show(Model $model)
    {
        return $model;
    }

    public function update(Model $model, array $data)
    {
        $model->update($data);
    }

    public function destroy(Model $model)
    {
        $model->delete();
    }

    public function transform(Model $model)
    {
        return $model->toArray();
    }
}