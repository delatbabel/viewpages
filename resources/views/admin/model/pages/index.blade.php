@extends('admin.layouts.main')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>
                {{$sTitle or 'Customize View'}}
                <small>{{ $config->getOption('title') }}</small>
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
                <?php $tmpRouteName = Route::getCurrentRoute()->getName(); ?>
                @include('admin.model.table')
            </div>
        </div>
    </div>
@endsection
