@extends('layouts.app')
 
@section('content')
<div class="card mt-5">
    <div class="card-header">List of raspberry Pis</div>
    <div class="card-body"> 
    <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('pis.create') }}"> Create New Pi</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Name</th>
            <th>MAC Address</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($pis as $pi)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $pi->pi_id }}</td>
            <td>{{ $pi->name }}</td>
            <td>{{ $pi->mac_address }}</td>
            <td>
                <form action="{{ route('pis.destroy',$pi->id) }}" method="POST">
   
                    <a class="btn btn-success mr-3" href="{{ route('pis.show',$pi->id) }}">Connect</a>
    
                    <a class="btn btn-primary" href="{{ route('pis.edit',$pi->id) }}">Edit</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $pis->links() !!}
    </div>
</div>
@endsection