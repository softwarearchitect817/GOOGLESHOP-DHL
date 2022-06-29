<div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="#">{{ env('APP_NAME') }}</a>

      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="#">{{ Str::limit(env('APP_NAME'), $limit = 1) }}</a>
      </div>
      <ul class="sidebar-menu">
        @if(Auth::user()->role_id==1)
        @can('dashboard')
        <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.dashboard') }}">
           <i class="flaticon-dashboard"></i> <span>{{ __('Dashboard') }}</span>
          </a>
        </li>
        @endcan

        @can('order.list')
       <li class="{{ Request::is('admin/order*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.order.index') }}">
           <i class="flaticon-note"></i> <span>{{ __('Orders') }}</span>
          </a>
        </li>
        @endcan

        @php
        $plan=false;            
        @endphp
        @can('plan.create')
        @php
           $plan=true; 
        @endphp
        @endcan 
        @can('plan.list')
        @php
        $plan=true;            
        @endphp
        @endcan
        @can('plan.list')
        @php
        $plan=true;            
        @endphp
        @endcan
        @if($plan == true)
        <li class="dropdown {{ Request::is('admin/plan*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-pricing"></i> <span>{{ __('Plans') }}</span></a>
          <ul class="dropdown-menu">
            @can('plan.create')
            <li><a class="nav-link {{ Request::is('admin/plan/create') ? 'active' : '' }}" href="{{ route('admin.plan.create') }}">{{ __('Create') }}</a></li>
            @endcan
            @can('plan.list')
            <li><a class="nav-link {{ Request::is('admin/plan') ? 'active' : '' }}" href="{{ route('admin.plan.index') }}">{{ __('All Plans') }}</a></li>
            @endcan
            @can('plan.list')
            <li><a class="nav-link {{ Request::is('admin/plan/credit') ? 'active' : '' }}" href="{{ route('admin.plan.credit') }}">{{ __('Email Plans') }}</a></li>
            @endcan
          </ul>
        </li>

        @endif
        @can('report.view')
        <li class="{{ Request::is('admin/report*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.report') }}">
            <i class="flaticon-dashboard-1"></i> <span>{{ __('Reports') }}</span>
          </a>
        </li>
        @endcan

        @can('customer.create','customer.list','customer.request','customer.list')
        <li class="dropdown {{ Request::is('admin/customer*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-customer"></i> <span>Customers</span></a>
          <ul class="dropdown-menu">
            @can('customer.create')
            <li><a class="nav-link" href="{{ route('admin.customer.create') }}">{{ __('Create Customer') }}</a></li>
            @endcan
            @can('customer.list')
            <li><a class="nav-link" href="{{ route('admin.customer.index') }}">{{ __('All Customers') }}</a></li>
            @endcan 
            @can('customer.request')
            <li><a class="nav-link" href="{{ route('admin.customer.index','type=3') }}">{{ __('Customer Request') }}</a></li>
            @endcan 
            @can('customer.list')
            <li><a class="nav-link" href="{{ route('admin.customer.index','type=2') }}">{{ __('Suspended Customers') }}</a></li>
            @endcan
          </ul>
        </li>
        @endcan

        @can('domain.create','domain.list')
         <li class="dropdown {{ Request::is('admin/domain*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-www"></i> <span>{{ __('Domains') }}</span></a>
          <ul class="dropdown-menu">
            @can('domain.create')
            <li><a class="nav-link {{ Request::is('admin/domain/create') ? 'active' : '' }}" href="{{ route('admin.domain.create') }}">{{ __('Create Domain') }}</a></li>
            @endcan
            @can('domain.list')
            <li><a class="nav-link {{ Request::is('admin/domain') ? 'active' : '' }}" href="{{ route('admin.domain.index') }}">{{ __('All Domains') }}</a></li>
            @if(getenv("AUTO_APPROVED_DOMAIN") !== false)

             <li><a class="nav-link {{ Request::is('admin/domain') ? 'active' : '' }}" href="{{ route('admin.customdomain.index') }}">{{ __('Custom Domains Requests') }}</a></li>
             @endif
            @endcan
          </ul>
        </li>
        @endcan

        @can('cron_job')
         <li class="{{ Request::is('admin/cron') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.cron.index') }}">
            <i class="flaticon-task"></i> <span>{{ __('Cron Jobs') }}</span>
          </a>
        </li>
        @endcan
        @can('payment_gateway.config')
         <li class="{{ Request::is('admin/payment-geteway*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.payment-geteway.index') }}" >
            <i class="flaticon-credit-card"></i> <span>{{ __('Payment Gateways') }}</span>
          </a>
        </li>
        @endcan
        @can('template.list')
        <li class="{{ Request::is('admin/template') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.template.index') }}">
            <i class="flaticon-template"></i> <span>{{ __('Templates') }}</span>
          </a>
        </li>
        @endcan
        @can('page.create','page.list')
        <li class="dropdown {{ Request::is('admin/page*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-document"></i> <span>{{ __('Pages') }}</span></a>
          <ul class="dropdown-menu">
            @can('page.create')
            <li><a class="nav-link" href="{{ route('admin.page.create') }}">{{ __('Create Pages') }}</a></li>
            @endcan
            @can('page.list')
            <li><a class="nav-link" href="{{ route('admin.page.index') }}">{{ __('All Pages') }}</a></li>
            @endcan
          </ul>
        </li>
        @endcan
        
        @can('language_edit')
        <li class="dropdown {{ Request::is('admin/language*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-translation"></i> <span>{{ __('Language') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('admin.language.create') }}">{{ __('Create language') }}</a></li>
            <li><a class="nav-link" href="{{ route('admin.language.index') }}">{{ __('Manage language') }}</a></li>
          </ul>
        </li>
        @endcan
       @can('site.settings')
        <li class="dropdown {{ Request::is('admin/appearance*') ? 'active' : '' }}  {{ Request::is('admin/gallery*') ? 'active' : '' }} {{ Request::is('admin/menu*') ? 'active' : '' }} {{ Request::is('admin/seo*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-settings"></i> <span>{{ __('Appearance') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('admin.appearance.show','header') }}">{{ __('Frontend Settings') }}</a></li>
            <li><a class="nav-link" href="{{ route('admin.gallery.index') }}">{{ __('Gallery') }}</a></li>
            <li><a class="nav-link" href="{{ route('admin.menu.index') }}">{{ __('Menu') }}</a></li>
            <li><a class="nav-link" href="{{ route('admin.seo.index') }}">{{ __('SEO') }}</a></li>
          </ul>
        </li>
        @endcan
        @can('marketing.tools')
         <li class="{{ Request::is('admin/marketing') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.marketing.index') }}">
           <i class="flaticon-megaphone"></i> <span>{{ __('Marketing Tools') }}</span>
          </a>
        </li>
        @endcan

        @can('site.settings','environment.settings')
         <li class="dropdown {{ Request::is('admin/site-settings*') ? 'active' : '' }} {{ Request::is('admin/system-environment*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-settings"></i> <span>{{ __('Settings') }}</span></a>
          <ul class="dropdown-menu">
            @can('site.settings')
            <li><a class="nav-link" href="{{ route('admin.site.settings') }}">{{ __('Site Settings') }}</a></li>
            @endcan
            @can('environment.settings')
            <li><a class="nav-link" href="{{ route('admin.site.environment') }}">{{ __('System Environment') }}</a></li>
            @endcan
          </ul>
        </li>
        @endcan

       
        @can('admin.list','role.list')
         <li class="dropdown {{ Request::is('admin/role*') ? 'active' : '' }} {{ Request::is('admin/users*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-member"></i> <span>{{ __('Admins & Roles') }}</span></a>
          <ul class="dropdown-menu">
            @can('role.list')
            <li><a class="nav-link" href="{{ route('admin.role.index') }}">{{ __('Roles') }}</a></li>
            @endcan
            @can('admin.list')
            <li><a class="nav-link" href="{{ route('admin.users.index') }}">{{ __('Admins') }}</a></li>
            @endcan
          </ul>
        </li>
        @endcan

        @endif

        @if(Auth::user()->role_id==3 || Auth::user()->role_id==4)
        
        @php
        $plan_limit=user_limit();
        

        @endphp
        <li class="{{ Request::is('seller/dashboard*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.dashboard') }}">
            <i class="flaticon-dashboard"></i> <span>{{ __('Dashboard') }}</span>
          </a>
        </li>

        <li class="dropdown {{ Request::is('seller/order*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-note"></i> <span>{{ __('Orders') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ url('/seller/orders/all') }}">{{ __('All Orders') }}</a></li>
            <li><a class="nav-link" href="{{ url('/seller/orders/canceled') }}">{{ __('Canceled') }}</a></li>

          </ul>
        </li>

        <li class="dropdown {{ Request::is('seller/product*') ? 'active' : '' }} {{ Request::is('seller/inventory*') ? 'active' : '' }} {{ Request::is('seller/category*') ? 'active' : '' }} {{ Request::is('seller/attribute*') ? 'active' : '' }} {{ Request::is('seller/brand*') ? 'active' : '' }} {{ Request::is('seller/coupon*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-box"></i> <span>{{ __('Products') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('seller.product.index') }}">{{ __('All Products') }}</a></li>
            <li><a class="nav-link" @if(filter_var($plan_limit['inventory']) == true) href="{{ route('seller.inventory.index') }}" @endif>{{ __('Inventory') }} @if(filter_var($plan_limit['inventory']) != true) <i class="fa fa-lock text-danger"></i> @endif</a></li>
            <li><a class="nav-link" href="{{ route('seller.category.index') }}">{{ __('Categories') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.attribute.index') }}">{{ __('Attributes') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.brand.index') }}">{{ __('Brands') }}</a></li>
              <li><a class="nav-link" href="{{ route('seller.coupon.index') }}">{{ __('Coupons') }}</a></li>
          </ul>
        </li>
        @if(env('MULTILEVEL_CUSTOMER_REGISTER') == true)
        <li class="{{ Request::is('seller/customer*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.customer.index') }}">
            <i class="flaticon-customer"></i> <span>{{ __('Customers') }}</span>
          </a>
        </li>
        @endif

        <li class="{{ Request::is('seller/transection*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.transection.index') }}">
            <i class="flaticon-credit-card"></i> <span>{{ __('Transactions') }}</span>
          </a>
        </li>

        <li class="{{ Request::is('seller/report*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.report.index') }}">
            <i class="flaticon-dashboard-1"></i> <span>{{ __('Reports') }}</span>
          </a>
        </li>
        <li class="{{ Request::is('seller/review*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.review.index') }}">
            <i class="flaticon-dashboard-1"></i> <span>{{ __('Review & Ratings') }}</span>
          </a>
        </li>


      

        <li class="dropdown {{ Request::is('seller/location*') ? 'active' : '' }} {{ Request::is('seller/shipping*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-delivery"></i> <span>{{ __('Shipping') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('seller.location.index') }}">{{ __('Countries') }}</a></li>
           

            <li>
              
               <a href="shipping" class=" dropdown-toggle" data-toggle="dropdown"> <span>{{ __('Shipping Methods') }}</span></a>
          <ul class="dropdown-menu" id="#shipping" >
            <li><a class="nav-link" href="{{ route('seller.shipping.index') }}">{{ __('Set up your own rates') }}</a></li>
           

            <li><a style="line-height: 10px;" class="nav-link" href="shipping_app">{{ __('Use app to calculate rates') }}</a></li>
          </ul>
              


  {{--        <a class="nav-link" href="{{ route('seller.shipping.index') }}">{{ __('Shipping Methods') }}</a> --}}

            </li>

          </ul>
        </li>

        <li class="dropdown {{ Request::is('seller/ads*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-megaphone"></i> <span>{{ __('Offer & Ads') }}</span></a>
          <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('seller.ads.index') }}">{{ __('Bump Ads') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.ads.show','banner') }}">{{ __('Banner Ads') }}</a></li>
          </ul>
        </li>
       
                
                      <li class="dropdown {{ Request::is('seller/blog*') ? 'active' : '' }}">
                          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                                  class="flaticon-note"></i> <span>{{ __('Blog') }}</span></a>
                          <ul class="dropdown-menu">
                              <li><a class="nav-link"
                                      href="{{ route('seller.blog-articles') }}">{{ __('Articles') }}</a></li>
                              <li><a class="nav-link"
                                      href="{{ route('seller.blog-view') }}">{{ __('Categories') }}</a></li>
                              <li><a class="nav-link"
                                      href="{{ route('seller.blog-articleComment') }}">{{ __('Comments') }}</a>
                              </li>
                          </ul>
                      </li>
               
        <li class="dropdown {{ Request::is('seller/setting*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-settings"></i> <span>{{ __('Settings') }}</span></a>
          <ul class="dropdown-menu">
            @php
                $p = \App\Models\Userplan::where('user_id', seller_id())
                          ->latest()
                          ->first();
                     
                $blog_plan = json_decode($p->plan->data);
                  @endphp
                
                          @if ($blog_plan->blog == true)
                          <li><a class="nav-link"
                                  href="{{ route('seller.blog-article.setting') }}">{{ __('Blog settings') }}</a>
                          </li>
                        @endif
            <li><a class="nav-link" href="{{ route('seller.settings.show','shop-settings') }}">{{ __('Shop Settings') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.settings.show','payment') }}">{{ __('Payment Options') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.settings.show','plan') }}">{{ __('Subscriptions') }}</a></li>
            @if(getenv("AUTO_APPROVED_DOMAIN") !== false)

            <li><a class="nav-link" href="{{ route('seller.domain.index') }}">{{ __('Domain Settings') }}</a></li>
            @endif
          </ul>
        </li>
          <li class="dropdown {{ Request::is('seller/marketing*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="flaticon-megaphone"></i> <span>{{ __('Marketing Tools') }}</span></a>
          <ul class="dropdown-menu">
                 <li><a class="nav-link" href="{{ route('seller.marketing.show','news-letter') }}">{{ __('News Letter') }}</a></li>
                  <li><a class="nav-link" href="{{ route('seller.marketing.show','system-email') }}">{{ __('System Template') }}</a></li>
                 
            <li><a class="nav-link" href="{{ route('seller.marketing.show','google-analytics') }}">{{ __('Google Analytics') }}</a></li>
             <li><a class="nav-link" href="{{ route('seller.marketing.show','tag-manager') }}">{{ __('Google Tag Manager') }}</a></li>
            <li><a class="nav-link" href="{{ route('seller.marketing.show','facebook-pixel') }}">{{ __('Facebook Pixel') }}</a></li>
             <li><a class="nav-link" href="{{ route('seller.marketing.show','whatsapp') }}">{{ __('Whatsapp Api') }}</a></li>
             <li><a class="nav-link" href="{{ route('seller.marketing.show','feed') }}">{{ __('Product Feed') }}</a></li>
          </ul>
        </li>
        <li class="{{ Request::is('seller/staff*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.staff.index') }}">
            <i class="flaticon-member"></i> <span>{{ __('Staff Accounts') }}</span>
          </a>
        </li>
        <li class="{{ Request::is('seller/abandoned_cart*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('seller.abandoned_cart.list') }}">
            <i class="flaticon-note"></i> <span>{{ __('Abandoned Cart') }}</span>
          </a>
        </li>
        <li class="{{ Request::is('seller/support*') ? 'active' : '' }}">
          <a class="nav-link" @if(filter_var($plan_limit['live_support']) == true) href="{{ route('seller.support') }}" @endif>
           @if(filter_var($plan_limit['live_support']) != true) <i class="fa fa-lock text-danger"></i> @else <i class="fa fa-user"></i>  @endif <span>{{ __('Technical Support') }} </span>
          </a>
        </li>
        

        
        <li class="menu-header">{{ __('SALES CHANNELS') }}</li>
        <li class="dropdown {{ Request::is('seller/setting*') ? 'active' : '' }}">
          <a href="#" class="nav-link has-dropdown"><i class="flaticon-shop"></i> <span>{{ __('Online store') }}</span></a>
          <ul class="dropdown-menu">
            <li><a href="{{ route('seller.theme.index') }}">{{ __('Themes') }}</a></li> 
            <li><a href="{{ route('seller.menu.index') }}">{{ __('Menus') }}</a></li> 
            <li><a href="{{ route('seller.page.index') }}">{{ __('Pages') }}</a></li> 
            <li><a href="{{ route('seller.slider.index') }}">{{ __('Sliders') }}</a></li> 
            <li><a href="{{ route('seller.seo.index') }}">{{ __('Seo') }}</a></li> 

           

          </ul>
        </li>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
          <a href="{{ url('/') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="fas fa-external-link-alt"></i>{{ __('Your Website') }}
          </a>
        </div> 
        @endif      
      </aside>
    </div>