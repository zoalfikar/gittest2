<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

      <ul class="navbar-nav" >
        <li class="nav-item ">
          <a  class="nav-link  " href="{{url('/')}}">Home </a>
        </li>
        <li class="nav-item ">
          <a class="nav-link"  href="{{url('/orders')}}">my orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('/showCategories')}}">categories</a>
        </li>
        <li class="nav-item ">
            <a class="nav-link " href="{{url('/cart')}}">
                <span class="badge badge-pill bg-primary cat-items-count"></span>cart
            </a>
        </li>
        <li class="nav-item ">
            <a class="nav-link " href="{{url('/wishlist')}}">
                <span class="badge badge-pill bg-success wishlist-items-count"></span>wishlist
            </a>
        </li>
    </ul>
    <div class="search-bar">
        <form action="{{url('/get-product')}}" method="POST">
            @csrf
            <div class="input-group">
                <input  type="search" name="search" id="search_product" class="form-control" placeholder="search products" aria-label="Username" aria-describedby="basic-addon1">
                <button type="submit" class="input-group-text" ><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
       <ul class="navbar-nav ms-auto" >
           <div class="collapse navbar-collapse" id="navbarNav">
        @guest

        <li class="nav-item">
            <a class="nav-link " href="{{url('login')}}">login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="{{url('register')}}">register</a>
        </li>

        @else

        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="#">{{Auth::user()->name}}</a>
        </li>

        @endguest

      </ul>
    </div>
  </nav>
