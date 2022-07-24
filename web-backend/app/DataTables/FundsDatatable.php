<?php

namespace App\DataTables;

use App\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class FundsDatatable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTables = datatables()
            ->eloquent($query)
            ->addColumn('type',function ($fund){

                if (strtolower(trim($fund->type)) == "credit"){
                    return "<span  class=\"badge badge-pill badge-primary p-1 \" data-toggle=\"tooltip\"".
                        " data-original-title=\"Operation Type\">".$fund->type."</span>";
                }else{
                    return "<span  class=\"badge badge-pill badge-secondary p-1 \" data-toggle=\"tooltip\"".
                        " data-original-title=\"Operation Type\">".$fund->type."</span>";
                    
                }
            })->rawColumns(['type']);

        return  $dataTables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Beneficiary $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Beneficiary $model)
    {

        $user = Auth::user();


        $searchTerm = null;
        $searchQuery = $this->request()->query();

        if (!empty($searchQuery) && is_array($searchQuery) && array_key_exists('search',$searchQuery)){
            if (!empty($searchQuery['search']) && array_key_exists('value',$searchQuery['search']) &&
                !empty($searchQuery['search'])){
                $searchTerm = trim($searchQuery['search']['value']);
            }
        }

        Log::info("[FundsDatatable][query]\t... \t",$this->attributes);
        $model = \App\Fund::whereUserId($user->id)->latest();

        if (!empty($searchTerm)){
            $model = $model->where('amount','LIKE','%'.$searchTerm.'%')
                ->OrWhere('balance_before','LIKE','%'.$searchTerm.'%')
                ->OrWhere('balance_after','LIKE','%'.$searchTerm.'%')
                ->OrWhere('description','LIKE','%'.$searchTerm.'%')
            ;
        }



        return  $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('export'),
                Button::make('reload')
            )->parameters(['drawCallback' => 'function() { drawCallBackHandler(); }',]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return  [
            Column::make('type')->addClass('text-center')->name('type')->data('type'),
            Column::make('amount')->addClass('text-center')->name('amount')->data('amount'),
            Column::make('balance_before')->addClass('text-center')->name('balance_before')
                ->data('balance_before'),
            Column::make('balance_after')->addClass('text-center')->name("balance_after")
                ->data("balance_after"),
            Column::make('description')->addClass('text-center')->name('description')
                ->data('description'),
            Column::make('created_at')->data('created_at'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'star-pay-account-statement-' . date('YmdHis');
    }
}
