@extends('layouts.dashboard')


@section('title', 'Posts')

@section('main')

<div class="right_col" role="main" ng-controller="IndexPosts">
    <p>{{ link_to_route('posts.create', 'Create new post [+]')}}</p>

    @if ($records->count())
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($records as $record)
            <tr>
                <td>{{ link_to_route('posts.edit', $record->title, array($record->id)) }}</td>
                <td>{{ $record->description }}</td>
                
                <td>
                    <i class="fa fa-trash row-delete" ng-click="delete_row($event)" data-modal_id="{{$record->id}}"></i>
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

    @else
    <p>There are no posts available</p>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links(); }}</div>
        </div>
    </div>
    
    

</div>
@stop