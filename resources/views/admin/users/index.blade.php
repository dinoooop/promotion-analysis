@extends('admin.layouts.dashboard')


@section('title', 'Users')

@section('main')

<div class="right_col" role="main">
    <p>{{ link_to_route('users.create', 'Create User [+]')}}</p>

    @if ($records->count())
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($records as $record)
            <tr>
                <td>{{ link_to_route('users.edit', $record->username, array($record->id)) }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->email }}</td>
                <td>{{ User::get_role_name($record->role) }}</td>
                <td class="action">    
                    <a href="{{route('users.edit', array($record->id))}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    @if(User::hasrole('user_delete', $record->id))
                    <a class="btn btn-danger row-delete" href="{{route('users.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
                    @endif
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

    @else
    <p>There are no users</p>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links() }}</div>
        </div>
    </div>
    
    

</div>
@stop