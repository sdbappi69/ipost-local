<?php

namespace App;

class Observer
{
    public $userID;

    public function __construct() {
        $this->userID = (auth()->user()) ? auth()->user()->id : null;
    }

    public function saving($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }

    public function saved($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }


    public function updating($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }

    public function updated($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }


    public function creating($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }

    public function created($model)
    {
        $model->created_by = $this->userID;
        $model->updated_by = $this->userID;
    }

}
