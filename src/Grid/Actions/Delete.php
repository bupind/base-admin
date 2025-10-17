<?php

namespace Base\Admin\Grid\Actions;

use Base\Admin\Actions\Response;
use Base\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Delete extends RowAction
{
    public $icon = 'icon-trash';

    public function __construct()
    {
        parent::__construct();
        $this->setActionClass('btn-danger btn-sm');
    }

    public function name()
    {
        return __('backend.delete');
    }

    public function addScript()
    {
        $this->attributes = [
            'onclick'  => 'backend.resource.delete(event,this)',
            'data-url' => "{$this->getResource()}/{$this->getKey()}",
        ];
    }

    /*
    // could use dialog as well instead of addScript
    public function dialog()
    {
        $options  = [
            "type" => "warning",
            "showCancelButton"=> true,
            "confirmButtonColor"=> "#DD6B55",
            "confirmButtonText"=> __('confirm'),
            "showLoaderOnConfirm"=> true,
            "cancelButtonText"=>  __('cancel'),
        ];
        $this->confirm('Are you sure delete?', '', $options);
    }
    */
    /**
     * @return Response
     */
    public function handle(Model $model)
    {
        $trans = [
            'failed'    => trans('backend.delete_failed'),
            'succeeded' => trans('backend.delete_succeeded'),
        ];
        try {
            DB::transaction(function() use ($model) {
                $model->delete();
            });
        } catch(\Exception $exception) {
            return $this->response()->error("{$trans['failed']} : {$exception->getMessage()}");
        }
        return $this->response()->success($trans['succeeded'])->refresh();
    }
}
