
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">

        <div class="navbar nav_title" style="border: 0;">
            <a href="{{url('/')}}" target="_blank" class="site_title"><i class="fa fa-pie-chart"></i> <span style="font-size: 12px">PROMOTION<strong>ANALYSIS</strong></span></a>
        </div>
        <div class="clearfix"></div>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

            <div class="menu_section">
                
                <ul class="nav side-menu">
                    
                    <li><a href="{{ url('admin/profile') }}" ><i class="fa fa-home"></i> Profile</a></li>
                    @if(User::hasrole('sup_admin_cap'))
                    <li><a href="{{ url('admin/users') }}" ><i class="fa fa-users"></i> Users</a></li>
                    @endif
                    
                </ul>
            </div>
            

        </div>
        <!-- /sidebar menu -->

       
       
    </div>
</div>