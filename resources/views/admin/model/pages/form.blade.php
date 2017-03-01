@extends('admin.layouts.main')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>
                {{$sTitle or 'Customize View'}}
                <small>Form</small>
            </h2>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active"><strong>Here</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>
                            @if($itemId)
                                {{trans('administrator::administrator.edit')}}
                            @else
                                {{trans('administrator::administrator.createnew')}}
                            @endif
                        </h5>
                    </div>
                    <div class="ibox-content">
                        {!! Form::model($model, [
                                'class'   => 'form-horizontal',
                                'enctype' => 'multipart/form-data',
                                'route'   => ['admin_save_item',$config->getOption('name'),$itemId],
                            ]) !!}
                        <div class="row">
                            @foreach($arrayFields as $key => $arrCol)
                                @if($arrCol['visible'] && $arrCol['editable'])
                                    <?php $tmpClass = in_array($key,
                                        ['content', 'websites']) ? 'col-md-12' : 'col-md-6'; ?>
                                    <div class="{{$tmpClass}}">
                                        <div class="form-group {{$errors->has($arrCol['field_name']) ? 'has-error' : null}}">
                                            <?php $tmpClass = in_array($key,
                                                ['content', 'websites']) ? 'col-md-2' : 'col-md-4'; ?>
                                            <label class="{{$tmpClass}} control-label"
                                                   for="{{$arrCol['field_name']}}">
                                                {!! $arrCol['title'] !!}:
                                            </label>
                                                <?php $tmpClass = in_array($key,
                                                    ['content', 'websites']) ? 'col-md-10' : 'col-md-8'; ?>
                                            <div class="{{$tmpClass}}">
                                                @include('admin.model.field',[
                                                   'type'         => $arrCol['type'],
                                                   'name'         => $arrCol['field_name'],
                                                   'id'           => $arrCol['field_name'],
                                                   'value'        => $model->{$arrCol['field_name']},
                                                   'arrCol'       => $arrCol,
                                                   'defaultClass' => 'form-control',
                                                   'flagFilter'   => false,
                                                ])
                                                @if ($errors->has($arrCol['field_name']))
                                                    <p style="color:red;">
                                                        {!!$errors->first($arrCol['field_name'])!!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a href="{{route('admin_index', $config->getOption('name'))}}" class="btn btn-default ">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">Save & Close</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
