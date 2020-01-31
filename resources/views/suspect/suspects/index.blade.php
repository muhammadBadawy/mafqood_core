@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Suspect</h1>
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
                                        <th>#</th><th>Name</th><th>Appearance Times</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($suspects as $item)
                                    <tr>
                                        <td>{{ $loop->iteration or $item->id }}</td>
                                        <td>{{ $item->name }}</td><td>{{ $item->appearance_times }}</td>
                                        <td>
                                            <a href="{{ url('/suspect/suspects/' . $item->id) }}" title="View Suspect"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/suspect/suspects/' . $item->id . '/edit') }}" title="Edit Suspect"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/suspect/suspects' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Suspect" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $suspects->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection