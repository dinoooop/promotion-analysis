@extends('admin.layouts.dashboard')


@section('title', 'Items')

@section('main')

<div class="right_col" role="main">



    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

            <div class="x_title">
                <h2>CSV Imported Sessions</h2>
                <div class="clearfix"></div>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <a href="{{route('multiples.create')}}">Import CSV [+]</a>
                </div>
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
            <p>There are no csv import available</p>
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