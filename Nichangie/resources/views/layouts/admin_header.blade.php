<div class="wrapper">
    <!-- Navbar-->
    <header class="main-header-top hidden-print">
        <a href="{{ (Auth::user()->usertype == 2) ? route('admin.home') : route('home')}}" class="logo"><strong>NACHANGIA</strong></a>
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#!" data-toggle="offcanvas" class="sidebar-toggle"></a>
            <!-- Navbar Right Menu-->
            <div class="navbar-custom-menu f-right">

                <ul class="top-nav">
                    <!--Notification Menu-->
                    <!-- window screen -->
                    <li class="pc-rheader-submenu">
                        <a href="#!" class="drop icon-circle" onclick="javascript:toggleFullScreen()">
                            <i class="icon-size-fullscreen"></i>
                        </a>

                    </li>
                    <!-- User Menu-->
                    <li class="dropdown">
                        <a href="#!" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle drop icon-circle drop-image">
                            <span><img class="img-circle " src="{{ asset('admin/assets/images/anonymous.jpg')}}" style="width:40px;" alt="User Image"></span>
                            <span>{{ Auth::user()->name; }} <b>{{ Auth::user()->lastname; }}</b> <i class=" icofont icofont-simple-down"></i></span>

                        </a>
                        <ul class="dropdown-menu settings-menu">
                            <li><a href="{{ route('profile') }}"><i class="icon-user"></i> {{ __('Profile')}}</a></li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="icon-logout"></i> {{ __('Logout')}}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Side-Nav-->
    <aside class="main-sidebar hidden-print ">
        <section class="sidebar" id="sidebar-scroll">
            <!-- Sidebar Menu-->
            <ul class="sidebar-menu">
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ (Auth::user()->usertype == 2) ? route('admin.home') : route('donee.home')}}">
                        <i class="icon-speedometer"></i><span> Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->user_type == 2)
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('user') }}">
                        <i class="icon-people"></i><span> Users</span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('story') }}">
                        <i class="icon-hourglass"></i><span> Campaigns</span>
                    </a>
                </li>
                <li class="treeview"><a class="waves-effect waves-dark" href="#!"><i class="icon-credit-card"></i><span> Transactions</span><i class="icon-arrow-down"></i></a>
                    <ul class="treeview-menu">
                        <li><a class="waves-effect waves-dark" href="{{ route('transaction')}}"><i class="icon-arrow-right"></i> All Transactions</a></li>
                        <li><a class="waves-effect waves-dark" href="{{ route('transaction.withdraw.request')}}"><i class="icon-arrow-right"></i> Withdraw Requests</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('donation.all') }}">
                        <i class="icon-heart"></i><span> Donations</span>
                    </a>
                </li>
                <li class="treeview"><a class="waves-effect waves-dark" href="#!"><i class="icon-speech"></i><span> Testimonials</span><i class="icon-arrow-down"></i></a>
                    <ul class="treeview-menu">
                        <li><a class="waves-effect waves-dark" href="{{ route('testimonial')}}"><i class="icon-arrow-right"></i> All Testimonials</a></li>
                        <li><a class="waves-effect waves-dark" href="{{ route('testimonial.show')}}"><i class="icon-arrow-right"></i> Add Testimonial</a></li>
                    </ul>
                </li>
                @else
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('me.campaign')}}">
                        <i class="icon-hourglass"></i><span> Campaigns</span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('transaction')}}">
                        <i class="icon-credit-card"></i><span> Transactions</span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="waves-effect waves-dark" href="{{ route('donation') }}">
                        <i class="icon-heart"></i><span> Donations</span>
                    </a>
                </li>
                @endif
            </ul>
        </section>
    </aside>