<div class="sidebar" data-color="{!! Auth::user()->isAdminUser ? "yellow" : (Auth::user()->isAgentUser ? "orange" : "blue") !!}">
  <!--
    Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
-->
  <div class="logo">
    <a href="{{route('home')}}" class="simple-text logo-mini">
      {{ __('_ST') }}
    </a>
    <a href="{{route('home')}}" class="simple-text logo-normal">
        # {{\Auth::user()->star_account_number}}
    </a>
  </div>
  <div class="sidebar-wrapper" id="sidebar-wrapper">
    <ul class="nav">
      <li class="@if ($activePage == 'home') active @endif">
        <a href="{{ route('home') }}">

            <i class="fas fa-tachometer-alt"></i>
          <p>{{ __('Dashboard') }}</p>
        </a>
      </li>

        @if(!Auth::user()->isAdminUser)
      <li class="@if ($activePage == 'accounts.top-up') active @endif">
        <a href="{{ route('accounts.top-up',auth()->user()->uuid) }}">
            <i class="fas fa-piggy-bank"></i>
          <p>{{ __('Account Topup') }}</p>
        </a>
      </li>

        @endif

        @if(!\Auth::user()->isAdminUser)
      <li class="@if ($activePage == 'transactions.airtime.create') active @endif">
        <a href="{{ route('transactions.airtime.create') }}">
          <i class="fas fa-mobile"></i>
          <p>{{ __('Airtime Top Up') }}</p>
        </a>
      </li>

        @endif
        @if(Auth::user()->isAdminUser)
            <li class = "@if ($activePage == 'users.index') active @endif">
                <a href="{{ route('users.index') }}">
                    <i class="fas fa-users"></i>
                    <p>{{ __('Accounts') }}</p>
                </a>
            </li>

            <li class = "@if ($activePage == 'charges.index') active @endif">
                <a href="{{ route('charges.index') }}">
                    <i class="fas fa-coins"></i>
                    <p>{{ __('Charges') }}</p>
                </a>
            </li>
        @endif

      <li class = "@if ($activePage == 'transactions.index') active @endif">
        <a href="{{ route('transactions.index') }}">
            <i class="fas fa-hand-holding-usd"></i>
          <p>{{ __('Transactions') }}</p>
        </a>
      </li>
      <li class = " @if ($activePage == 'beneficiaries') active @endif">
        <a href="{{ route('beneficiaries.index') }}">
            <i class="fas fa-network-wired"></i>
          <p>{{ __('Beneficiaries') }}</p>
        </a>
      </li>



        @if(\Auth::user()->isAdminUser)
        <li>
            <a data-toggle="collapse" href="#accountTopUp">
                <i class="fas fa-exchange-alt"></i>
                <p>
                    {{ __("Rates") }}
                    <b class="caret"></b>
                </p>
            </a>

            <div class="collapse" id="accountTopUp">
                <ul class="nav">
                    <li class="@if ($activePage == 'rate-settings.index') active @endif">
                        <a href="{{ route('rate-settings.index') }}">
                            <i class="fas fa-cogs"></i>
                            <p> {{ __("Rate Configurations") }} </p>
                        </a>
                    </li>

                    <li class="@if ($activePage == 'rates.index') active @endif">
                        <a href="{{ route('rates.index') }}">
                            <i class="now-ui-icons design_bullet-list-67"></i>
                            <p> {{ __("System Rates") }} </p>
                        </a>
                    </li>

                </ul>
            </div>
        </li>

        @endif

        @if(!\Auth::user()->isAdminUser)
        <li>
            <a data-toggle="collapse" href="#sendMoney">
                <i class="fas fa-money-check-alt"></i>
                <p>
                    {{ __("Send Money") }}
                    <b class="caret"></b>
                </p>
            </a>
            <div class="collapse @if ($activePage == 'transactions') active @endif" id="sendMoney">
                <ul class="nav">
                    <li class="">
                        <a href="{{ route('transactions.create.by-service-type',['star-pay']) }}">
{{--                            <i class="now-ui-icons users_single-02"></i>--}}
                            <i class="fas fa-suitcase-rolling"></i>
                            <p> {{ __("Star Pay Account") }} </p>
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ route('transactions.create.by-service-type',['Bank']) }}">
{{--                            <i class="now-ui-icons design_bullet-list-67"></i>--}}
                            <i class="fas fa-university"></i>
                            <p> {{ __("Bank Transfer") }} </p>
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ route('transactions.create.by-service-type',['Wallet']) }}">
{{--                            <i class="now-ui-icons design_bullet-list-67"></i>--}}
                            <i class="fas fa-wallet"></i>
                            <p> {{ __("Mobile Money") }} </p>
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ route('transactions.create.by-service-type',['Pickup']) }}">
{{--                            <i class="now-ui-icons design_bullet-list-67"></i>--}}
                            <i class="fas fa-location-arrow"></i>
                            <p> {{ __("Cash Pick Up") }} </p>
                        </a>
                    </li>

                </ul>
            </div>
        </li>
        @endif

    </ul>
  </div>
</div>
