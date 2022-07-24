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

class UsersDataTable extends DataTable
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
            ->addColumn('account_type',function ($user){

                if ($user->isAdminUser){
                    return "<span  class=\"badge badge-pill badge-primary p-1 \" data-toggle=\"tooltip\"".
                        " data-original-title=\"Admin User\">Admin</span>";
                }else{
                    return "<span class=\"badge badge-pill badge-secondary p-1 \" data-toggle=\"tooltip\" ".
                        "data-original-title=\"User\">User</span>";
                }
            })
            ->addColumn('email',function ($user){
                return "<span   data-toggle=\"tooltip\"".
                    " data-original-title=\"".$user->email."\">".\Str::limit($user->email,10)."</span>";

            })
            ->addColumn('account_status',function ($user){

                if ($user->accountStatus == "active"){
                    return "<span  class=\"badge badge-pill badge-primary p-1 \" data-toggle=\"tooltip\">Active</span>";
                }else{
                    return "<span   class=\"badge badge-pill badge-danger p-1 center-block\" data-toggle=\"tooltip\" >"
                        .$user->accountStatus."</span>";
                }

            })
            ->addColumn('fullname',function ($user){
                return $user->firstname." ".$user->lastname." ".$user->othernames;
            })->addColumn('msisdn',function ($user){
                return $user->msisdn;
            }) ->addColumn('star_account_number',function ($user){
                return $user->star_account_number;
            })->addColumn('balance',function ($user){

                return $user->country->currency_code." ".floatval($user->balance);
            })
            ->addColumn('action', function ($user){

                $accountStatusAction = '';
                $accountUpgradeAction = '';

                $showAction =  '<a href="'.route('users.show',$user->id).'" data-toggle="tooltip" '.
                    'data-original-title="Show Details""><i class="fa fa-eye text-warning m-r-10"></i></a>';

                $fundUserAction =  '<a href="'.route('users.fund.get',$user->id).'" data-toggle="tooltip" '.
                    'data-original-title="Credit E-Value""><i class="fa fa-comment-dollar text-secondary m-r-10"></i></a>';
//                <i class="fas fa-comment-dollar"></i>
                if (!$user->isAgentUser){
                    $accountUpgradeAction  .= '<a href="'.route('users.account-upgrade.agent',$user).'" '.
                        'data-toggle="tooltip" data-original-title="Upgrade To Agent"> '.
                        '<i class="fa fa-concierge-bell text-info m-r-10"></i> </a>';
                }


                if (!$user->isAdminUser){
                    $accountUpgradeAction .=  '<a form-alert-message="Upgrade To Admin" '.
                        'form-id="usersDelete'.$user->id.'" class="deleteModel" '.
                        'href="'.route('users.account-upgrade.admin',[$user->id]).'" data-toggle="tooltip" '.
                        'data-original-title="Upgrade to Admin"> <i class="fa fa-user-lock text-secondary m-r-10"></i>'
                        .' </a>';
                }



                if ($user->accountStatus == "de-activated"){
                    $accountStatusAction =  '<a form-alert-message="Kindly Confirm the Re-Activation of this User" '.
                        'form-id="Reactivate'.$user->id.'" class="deleteModel" '.
                        'href="'.route('users.account-status.re-activate',[$user->id]).'" data-toggle="tooltip" '.
                        'data-original-title="Reactivate Account"> <i class="fa fa-user-plus text-success m-r-10"></i>'
                        .'</a>';
                    $accountStatusAction .= \Form::open(['action' => ['UserController@reActivateUser',$user->id],
                        'method' => 'PATCH', 'id' => "Reactivate$user->id"]);
                    $accountStatusAction .= \Form::close();

                }else{
                    $accountStatusAction =  '<a form-alert-message="Kindly Confirm the De-Activation of this User" '.
                        'form-id="DeActivateDelete'.$user->id.'" class="deleteModel" '.
                        'href="'.route('users.account-status.de-activate',[$user->id]).'" data-toggle="tooltip" '.
                        'data-original-title="Deactivate Account"> <i class="fa fa-user-times text-danger m-r-10"></i>'
                        .' </a>';
                    $accountStatusAction .= \Form::open(['action' => ['UserController@deActivateUser',$user->id],
                        'method' => 'PATCH', 'id' => "DeActivateDelete$user->id"]);
                    $accountStatusAction .= \Form::close();

                }

                return $fundUserAction.$showAction.$accountUpgradeAction.$accountStatusAction;
            })->rawColumns(['action','account_type','account_status','email']);

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

//        $user = Auth::user();
        $searchTerm = null;
        $searchQuery = $this->request()->query();

        if (!empty($searchQuery) && is_array($searchQuery) && array_key_exists('search',$searchQuery)){
            if (!empty($searchQuery['search']) && array_key_exists('value',$searchQuery['search']) &&
                !empty($searchQuery['search'])){
                $searchTerm = trim($searchQuery['search']['value']);
            }
        }

        Log::info("[UsersDataTable][query]\t... \t",$this->attributes);
        $model = \App\User::latest();

        if (!empty($searchTerm)){
            $model = $model->where('firstname','LIKE','%'.$searchTerm.'%')
                ->OrWhere('lastname','LIKE','%'.$searchTerm.'%')
                ->OrWhere('othernames','LIKE','%'.$searchTerm.'%')
                ->OrWhere('star_account_number','LIKE','%'.$searchTerm.'%')
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
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
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
            Column::make('Status')->addClass('text-center')->name('account_status')
                ->data('account_status'),
            Column::make('Type')->addClass('text-center')->name('account_type')
                ->data('account_type'),
            Column::make('MSISDN')->addClass('text-center')->name('msisdn')
            ->data('msisdn'),
            Column::make('Name')->addClass('text-center')->name("fullname")->data("fullname"),
            Column::make('Email')->addClass('text-center')->name('email')->data('email'),
            Column::make('Account Number')->addClass('text-center')->name('star_account_number')
                ->data('star_account_number'),
            Column::make('Balance')->addClass('text-center')->name('balance')->data('balance'),
            Column::make('ID Type')->addClass('text-center')->name('idType')
                ->data('idType'),
            Column::make('created_at')->data('created_at'),
//            Column::make('updated_at')->title("Last Updated")->name('updated_at')->data('updated_at'),
            Column::computed('action')->exportable(false)->printable(false)->width(60)
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
        return 'accounts_' . date('YmdHis');
    }
}
