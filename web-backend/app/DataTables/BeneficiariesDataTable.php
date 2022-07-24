<?php

namespace App\DataTables;

use App\Applicant;
use App\Beneficiary;
use App\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BeneficiariesDataTable extends DataTable
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
            ->eloquent($query)->addColumn('fullname',function ($beneficiary){
                return $beneficiary->firstname." ".$beneficiary->lastname." ".$beneficiary->othernames;
            })->addColumn('account_type',function ($beneficiary){

                if ($beneficiary->account_type == "PickUp"){
                    return "<span  class=\"badge badge-pill badge-primary p-1 \" data-toggle=\"tooltip\" data-original-title=\"".$beneficiary->account_type."\">".$beneficiary->account_type."</span>";
                }

                if ($beneficiary->account_type == "Bank"){
                    return "<span   class=\"badge badge-pill badge-danger p-1 center-block\" data-toggle=\"tooltip\" data-original-title=\"".$beneficiary->account_type."\">".$beneficiary->account_type."</span>";
                }

                if ($beneficiary->account_type == "Wallet"){
                    return "<span class=\"badge badge-pill badge-warning p-1 \" data-toggle=\"tooltip\" data-original-title=\"".$beneficiary->account_type."\">".$beneficiary->account_type."</span>";
                }

                return "<span class=\"badge badge-pill badge-secondary p-1 \" data-toggle=\"tooltip\" data-original-title=\"".$beneficiary->account_type."\">".$beneficiary->account_type."</span>";

            }) ->addColumn('account_number',function ($beneficiary){

                if ($beneficiary->account_type == "Bank"){
                    $account_number = $beneficiary->account_number;
                }else{
                    $account_number = $beneficiary->msisdn;
                }
                return $account_number;
            })
            ->addColumn('action', function ($beneficiary){

                $editAction  = '<a href="'.route('beneficiaries.edit',$beneficiary).'" data-toggle="tooltip" data-original-title="Edit Detail"> <i class="fa fa-edit text-info m-r-10"></i> </a>';
                $showAction =  '<a href="'.route('beneficiaries.show',$beneficiary->id).'" data-toggle="tooltip" data-original-title="Edit Details""><i class="fa fa-eye text-warning m-r-10"></i></a>';
                $deleteAction =  '<a form-alert-message="Kindly Confirm the removal of this Beneficiary" form-id="beneficiariesDelete'.$beneficiary->id.'" class="deleteModel" href="'.route('beneficiaries.destroy',[$beneficiary->id]).'" data-toggle="tooltip" data-original-title="Remove Beneficiary"> <i class="fa fa-trash text-danger m-r-10"></i> </a>';
                $deleteAction .= \Form::open(['action' => ['BeneficiariesController@destroy',$beneficiary->id], 'method' => 'DELETE', 'id' => "beneficiariesDelete$beneficiary->id"]);
                $deleteAction .= \Form::close();

                return $editAction.$showAction.$deleteAction;
            })->rawColumns(['action','account_type']);

        if (Auth::user()->isAdminUser){
            $dataTables->addColumn('Benefactor',function ($beneficiary){
                return '<a href="'.route('beneficiaries.edit',$beneficiary).'" data-toggle="tooltip" data-original-title="Edit Detail"> <i class="fa fa-edit text-info m-r-10"></i> </a>';
            });
        }
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

//        Log::info("[BeneficiariesDataTable][query]\t... \t",$this->request()->in);

        if ($user->isAdminUser){
            $model = Beneficiary::latest();
        }else{
            $model = Beneficiary::whereUserId($user->id)->latest();
        }

        if (!empty($searchTerm)){
            $model = $model->where('firstname','LIKE','%'.$searchTerm.'%')
            ->OrWhere('lastname','LIKE','%'.$searchTerm.'%')
            ->OrWhere('nickname','LIKE','%'.$searchTerm.'%')
            ->OrWhere('othernames','LIKE','%'.$searchTerm.'%')
            ->OrWhere('account_number','LIKE','%'.$searchTerm.'%')
            ;
        }


        return  $model;
//        return $model->newQuery()->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('beneficiaries-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
//                        Button::make('print'),
//                        Button::make('reset'),
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
        $columns = [
            Column::make('Service Type')->addClass('text-center')->name('account_type')
                ->data('account_type'),
            Column::make('Name')->addClass('text-center')->name("fullname")->data("fullname"),
            Column::make('Nick Name')->addClass('text-center')->name('nickname')
                ->data('nickname'),
            Column::make('Account')->addClass('text-center')->name('account_number')
                ->data('account_number'),
            Column::make('Bank / MNO')->addClass('text-center')->name('bank_name')
                ->data('bank_name'),
            Column::make('created_at')->data('created_at'),
            Column::make('updated_at')->title("Last Updated")->name('updated_at')
                ->data('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];

        return  $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Beneficiaries_' . date('YmdHis');
    }
}
