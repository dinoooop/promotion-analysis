@extends('admin.layouts.dashboard')

@section('title', 'Manage multiple promotions')

@section('main')

<div class="right_col" role="main">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h3 class="pull-left">Manage multiple promotions</h3>
                <a href="{{url('admin/multiples/create')}}" class="btn btn-info pull-right">Click here to add multiple promotions.</a>
                <div class="clearfix"></div>
            </div>
            
            

            @if ($records->count())
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Imported Date</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($records as $record)
                    <?php $record = App\Multiple::display_prepare($record) ?>
                    <tr>
                        <td><?php echo $record->title; ?></td>
                        <td>{{ $record->type }}</td>
                        <td>{{ $record->created_at }}</td>
                        
                        <td>
                            <a class="btn btn-danger row-delete" href="{{route('multiples.destroy', array($record->id))}}" data-modal_id="{{$record->id}}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>

            @else
            <p>There are no multiple promotions available</p>
            @endif
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">{{ $records->links() }}</div>
        </div>
    </div>



</div>
@stop