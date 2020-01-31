@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Area</h1>
@stop

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                      <form class="" action="#" method="get">
                        <input type="text" name="search" value="">
                        <input class="btn-sm btn-warning" type="submit" value="Submit">
                      </form>
                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th><th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($cities as $item)
                                    <tr>
                                        <td><a href="{{ url('areas/areasRating/'.$item['id']) }}">{{ $item['name'] }}</a></td>
                                        <td>{{ $item['count'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
