<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        @if(auth()->user()->role?->name === 'SuperAdmin')
            <li class="nav-item">
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.branches.index') }}" class="nav-link {{ request()->routeIs('superadmin.branches.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-code-branch"></i>
                    <p>Branches</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.users.index') }}" class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Users</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.loan-types.index') }}" class="nav-link {{ request()->routeIs('superadmin.loan-types.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-hand-holding-usd"></i>
                    <p>Loan Types</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.fd-setups.index') }}" class="nav-link {{ request()->routeIs('superadmin.fd-setups.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-piggy-bank"></i>
                    <p>FD Setups</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.account-types.index') }}" class="nav-link {{ request()->routeIs('superadmin.account-types.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-wallet"></i>
                    <p>Account Types</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.company-setup.show') }}" class="nav-link {{ request()->routeIs('superadmin.company-setup.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Company Setup</p>
                </a>
            </li>
        @endif

        @if(auth()->user()->role?->name === 'Manager')
            <li class="nav-item">
                <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manager.customers.index') }}" class="nav-link {{ request()->routeIs('manager.customers.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Customers</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manager.bank-accounts.index') }}" class="nav-link {{ request()->routeIs('manager.bank-accounts.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-university"></i>
                    <p>Bank Accounts</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manager.fd-accounts.index') }}" class="nav-link {{ request()->routeIs('manager.fd-accounts.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-piggy-bank"></i>
                    <p>FD Accounts</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manager.loans.index') }}" class="nav-link {{ request()->routeIs('manager.loans.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-hand-holding-usd"></i>
                    <p>Loans</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manager.loan-applications.index') }}" class="nav-link {{ request()->routeIs('manager.loan-applications.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Loan Applications</p>
                </a>
            </li>
        @endif

        @if(auth()->user()->role?->name === 'Clerk')
            <li class="nav-item">
                <a href="{{ route('clerk.dashboard') }}" class="nav-link {{ request()->routeIs('clerk.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clerk.customers.index') }}" class="nav-link {{ request()->routeIs('clerk.customers.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-plus"></i>
                    <p>Customers</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clerk.loan-applications.index') }}" class="nav-link {{ request()->routeIs('clerk.loan-applications.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Loan Applications</p>
                </a>
            </li>
        @endif

        @if(auth()->user()->role?->name === 'Cashier')
            <li class="nav-item">
                <a href="{{ route('cashier.dashboard') }}" class="nav-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cashier.transactions.index') }}" class="nav-link {{ request()->routeIs('cashier.transactions.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                    <p>Transactions</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cashier.loan-repayments.index') }}" class="nav-link {{ request()->routeIs('cashier.loan-repayments.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>Loan Repayments</p>
                </a>
            </li>
        @endif

        @if(auth()->user()->role?->name === 'Accountant')
            <li class="nav-item">
                <a href="{{ route('accountant.dashboard') }}" class="nav-link {{ request()->routeIs('accountant.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item has-treeview {{ request()->routeIs('accountant.reports.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('accountant.reports.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Reports <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('accountant.reports.loan-outstanding') }}" class="nav-link {{ request()->routeIs('accountant.reports.loan-outstanding') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Loan Outstanding</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('accountant.reports.transaction-statement') }}" class="nav-link {{ request()->routeIs('accountant.reports.transaction-statement') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Transaction Statement</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('accountant.reports.loan-demand') }}" class="nav-link {{ request()->routeIs('accountant.reports.loan-demand') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Loan Demand</p>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

    </ul>
</nav>
