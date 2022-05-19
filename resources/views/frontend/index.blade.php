@extends('layouts.frontend')
@section('title')
    welcom page
@endsection
@section('content')
@include('layouts/inct/frontendslider')

<div class="py-5">
    <div class="container">
        <div class="row">
                @foreach ($products as $product)
                <div class="col-md-3 mt-3">
                    <div class="card">
                        <img src="{{asset('assets/uploads/product/'.$product->img)}}" alt="not found">
                        <div class="card-body">
                            <h1>{{$product->name}}</h1>
                            <small>{{$product->selling_price}}</small>

                        </div>
                    </div>
                </div>
                @endforeach

                </div>

        </div>

    </div>

</div>

@endsection
@section('scripts')
<script>
    $('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
})



</script>

@endsection
