<?php

namespace App\Repositories;

trait Repository
{
    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $id
     * @param $input
     * @return mixed
     */
    public function update($id, $input)
    {
        $this->model = $this->getById($id);
        return $this->save($this->model, $input);
    }

    /**
     * @param $model
     * @param $input
     * @return mixed
     */
    public function save($model, $input)
    {
        $model->fill($input);
        $model->save();
        return $model;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return $this->model::find($id);
    }
}
