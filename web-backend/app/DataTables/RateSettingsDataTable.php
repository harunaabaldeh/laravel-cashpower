<?php

namespace App\DataTables;

use App\RateSetting;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RateSettingsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param $query
     * @return DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()->eloquent($query)
            ->addColumn('action', function ($rateSetting){
            $editAction  = '<a href="'.route('rate-settings.edit',$rateSetting). '" data-toggle="tooltip"'.
                ' data-original-title="Edit Detail"> <i class="fa fa-edit text-info m-r-10"></i> </a>';

            $deleteAction =  '<a form-alert-message="Kindly Confirm the removal of this Rate Configurations" '.
                'form-id="rateSettingDelete'.$rateSetting->id.'" class="deleteModel" href="'
                .route('rate-settings.destroy',[$rateSetting->id]).'" data-toggle="tooltip" '.
                'data-original-title="Remove Rate Configuration"> <i class="fa fa-trash text-danger m-r-10"></i> </a>';

            $deleteAction .= \Form::open(['action' => ['RatesSettingsController@destroy',$rateSetting->id],
                'method' => 'DELETE', 'id' => "rateSettingDelete$rateSetting->id"]);
            $deleteAction .= \Form::close();

            return $editAction.$deleteAction;
        })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param RateSetting $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RateSetting $model)
    {
        return $model->newQuery()->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('ratesettings-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [

            Column::make('id')->addClass("text-center"),
            Column::make('Source Currency')->name('source_currency')->data("source_currency")
                ->addClass('text-center'),
            Column::make('Destination Currency')->name('destination_currency')
                ->data("destination_currency")->addClass('text-center'),
            Column::make('Fixed Markup')->name('markup_fixed')->data("markup_fixed")
                ->addClass('text-center'),
            Column::make('Percentage Markup')->name('markup_percentage')->data("markup_percentage")
                ->addClass('text-center'),
            Column::make('created_at')->addClass("text-center"),
            Column::make('updated_at')->addClass('text-center')->title("Last Updated")
                ->addClass("text-center"),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'RateSettings_' . date('YmdHis');
    }
}
