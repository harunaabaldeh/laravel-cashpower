<?php

namespace App\DataTables;

use App\Charge;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ChargesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param $query
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()->eloquent($query)
            ->addColumn('source_country',function ($charge){
                return empty($charge->source_country) ? "None" : $charge->source_country;
            })
            ->addColumn('destination_country',function ($charge){
                return empty($charge->destination_country) ?  "None" : $charge->destination_country;
            })
            ->addColumn('action', function ($charge){

                $editAction  = '<a href="'.route('charges.edit',$charge).'" data-toggle="tooltip" '.
                    'data-original-title="Edit Detail"> <i class="fa fa-edit text-info m-r-10"></i> </a>';

                $deleteAction =  '<a form-alert-message="Kindly Confirm the removal of this Charge Configurations" '.
                    'form-id="chargeDelete'.$charge->id.'" class="deleteModel" href="'
                    .route('charges.destroy',[$charge->id]).'" data-toggle="tooltip" '.
                    'data-original-title="Remove Charge Configuration"> '.
                    '<i class="fa fa-trash text-danger m-r-10"></i> </a>';

                $deleteAction .= \Form::open(['action' => ['ChargesController@destroy',$charge->id],
                    'method' => 'DELETE', 'id' => "chargeDelete$charge->id"]);
                $deleteAction .= \Form::close();

                return  $editAction.$deleteAction;
            })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Rate $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Charge $model)
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
            ->setTableId('charges-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
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
            Column::make('Account')->name('account_type')->data("account_type")
                ->addClass('text-center'),
            Column::make('Service')->name('service_name')->data("service_name")
                ->addClass('text-center'),
            Column::make('Source')->name('source_country')->data("source_country")
                ->addClass('text-center'),
            Column::make('Destination')->name('destination_country')
                ->data("destination_country")->addClass('text-center'),
            Column::make('Fixed')->name('fixed_charge')->data("fixed_charge")
                ->addClass('text-center'),
            Column::make('Percentage')->name('percentage_charge')->data("percentage_charge")
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
        return 'Charges_' . date('YmdHis');
    }
}
