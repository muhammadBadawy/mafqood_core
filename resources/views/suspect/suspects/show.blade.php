@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Show Suspect</h1>
@stop

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">

                        <a href="{{ url('/suspect/suspects') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/suspect/suspects/' . $suspect->id . '/edit') }}" title="Edit Suspect"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('suspect/suspects' . '/' . $suspect->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Suspect" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $suspect->id }}</td>
                                    </tr>
                                    <tr><th> Name </th><td> {{ $suspect->name }} </td></tr><tr><th> Appearance Times </th><td> {{ $suspect->appearance_times }} </td></tr>
                                </tbody>
                            </table>
                            <div class="row">
                              @foreach($suspect->stamps as $stamp)
                              <div class="col-md-4">
                                <a href="{{ asset(Storage::url('people/' . $stamp->image)) }}">
                                  <img style="height:200px;width:200px;image-orientation: from-image;" src="{{ asset(Storage::url('people/' . $stamp->image)) }}" alt="">
                                </a>
                              </div>
                              @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
