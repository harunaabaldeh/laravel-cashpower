<?php

namespace App\DataTables;

use App\Beneficiary;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables()
            ->eloquent($query)->addColumn('status',function ($transaction){

//                ["Pending","Error","Cancelled","Success"]
                if ($transaction->status == "Pending"){
                    return "<span  class=\"badge badge-pill badge-primary p-1 \" data-toggle=\"tooltip\" "
                        ."data-original-title=\"".$transaction->status_message."\">".$transaction->status."</span>";
                }

                if ($transaction->status == "Error"){
                    return "<span   class=\"badge badge-pill badge-danger p-1 center-block\" "
                        ."data-toggle=\"tooltip\" data-original-title=\""
                        .$transaction->status_message."\">".$transaction->status."</span>";
                }

                if ($transaction->status == "Cancelled"){
                    return "<span class=\"badge badge-pill badge-warning p-1 \" data-toggle=\"tooltip\"".
                        " data-original-title=\"".$transaction->status_message."\">".$transaction->status."</span>";
                }


                if ($transaction->status == "Success"){
                    return "<span class=\"badge badge-pill badge-success p-1 \" data-toggle=\"tooltip\" " .
                        "data-original-title=\"".$transaction->status_message."\">".$transaction->status."</span>";
                }

                return "<span class=\"badge badge-pill badge-secondary p-1 \" data-toggle=\"tooltip\" "
                    ."data-original-title=\"".$transaction->status_message."\">".$transaction->status."</span>";
            })
            ->addColumn('sendAmount',function ($transaction){
                return $transaction->source_currency." ".$transaction->source_amount;
            })->addColumn('receiveAmount',function ($transaction){
                return $transaction->destination_currency." ".$transaction->destination_amount;
            })->addColumn('receiveAmount',function ($transaction){
                return $transaction->destination_currency." ".$transaction->destination_amount;
            })->addColumn('beneficiary',function ($transaction){

                if ($transaction->type == "star-pay"){

                    //TODO .. fix web beneficiary assignment for star-pay transfers.
                    $beneficiary = \App\Beneficiary::find($transaction->beneficiary_id);
                    return !empty($beneficiary) ? "<a data-toggle=\"tooltip\" ".
                        "data-original-title=\"View Beneficiary Details\" href='#'>".
                        $beneficiary->fullName."</a>" : "None";
                }


                return !empty($transaction->beneficiary) ? "<a data-toggle=\"tooltip\"".
                    " data-original-title=\"View Beneficiary Details\" href='".
                    route("beneficiaries.show",$transaction->beneficiary)."'>".
                    $transaction->beneficiary->nickname."</a>" : "None";
            })
            ->rawColumns(['beneficiary','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $model =  $model->newQuery();

        if (!Auth::user()->isAdminUser){
            $model = $model->where('user_id',Auth::user()->id);
        }

        $model = $model->latest();

        $searchTerm = null;
        $searchQuery = $this->request()->query();

        if (!empty($searchQuery) && is_array($searchQuery) && array_key_exists('search',$searchQuery)){
            if (!empty($searchQuery['search']) && array_key_exists('value',$searchQuery['search']) &&
                !empty($searchQuery['search'])){
                $searchTerm = trim($searchQuery['search']['value']);
            }
        }

        Log::info("[TransactionsDataTable][query]\t... \t".$searchTerm);

        if (!empty($searchTerm))
        {
            $model = \App\Transaction::
                where('type','LIKE','%'.$searchTerm.'%')
                ->OrWhere('destination_currency','LIKE','%'.$searchTerm.'%')
                ->OrWhere('destination_amount','LIKE','%'.$searchTerm.'%')
                ->OrWhere('source_amount','LIKE','%'.$searchTerm.'%')
                ->OrWhere('status','LIKE','%'.$searchTerm.'%')
                ->OrWhere('status_message','LIKE','%'.$searchTerm.'%')
               ->latest()
            ;
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('transactions-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
//                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
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
        return [
            Column::make('id'),
            Column::make('type')->addClass('text-center'),
            Column::make('status')->addClass('text-center'),
            Column::make('Send Amount')->name('sendAmount')->data("sendAmount")
                ->addClass('text-center'),
            Column::make('Receive Amount')->name('receiveAmount')->data("receiveAmount")
                ->addClass('text-center'),
            Column::make('Beneficiary')->name("beneficiary")->data("beneficiary")
                ->addClass('text-center'),
            Column::make('created_at')->addClass('text-center'),
            Column::make('updated_at')->addClass('text-center')->title("Last Updated"),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Transactions_' . date('YmdHis');
    }
}
