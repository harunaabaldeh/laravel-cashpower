<?php

namespace App\DataTables;

use App\Rate;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RatesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param $query
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()->eloquent($query) ->addColumn('action', function ($rate){
                return '<a href="'.route('rates.edit',$rate).'" data-toggle="tooltip" '.
                    'data-original-title="Edit Detail"> <i class="fa fa-edit text-info m-r-10"></i> </a>';
            })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Rate $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Rate $model)
    {
        return $model->newQuery()->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('rates-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
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
            Column::make('id'),
            Column::make('Source Currency')->name('source_currency')->data("source_currency")
                ->addClass('text-center'),
            Column::make('Destination Currency')->name('destination_currency')
                ->data("destination_currency")->addClass('text-center'),
            Column::make('Rate')->name('rate')->data("rate")->addClass('text-center'),
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
        return 'Rates_' . date('YmdHis');
    }
}
