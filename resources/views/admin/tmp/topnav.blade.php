<div class="top_nav">

    <div class="nav_menu">
        <nav class="" role="navigation">
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    @if(Auth::check())
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    @endif
                    <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                        <li><a href="{{ url('admin/profile') }}">  Profile</a></li>
                        <li><a href="{{ URL::action('UsersController@logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

</div>