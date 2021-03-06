@extends('backend.layout')

@section('css')
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker3.min.css" />
    <link href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        #dataTableBuilder_wrapper {
            height: auto !important;
        }
    </style>
@stop

@section('contents')
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $store->name }}</h1>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="form-horizontal">
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Period: </label>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-warning active">
                                <input type="radio" name="period" id="day" autocomplete="off" checked value="day"> Day
                            </label>
                            <label class="btn btn-warning">
                                <input type="radio" name="period" id="week" autocomplete="off" value="week"> Week
                            </label>
                            <label class="btn btn-warning">
                                <input type="radio" name="period" id="month" autocomplete="off" value="month"> Month
                            </label>
                        </div>
                        </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="daterange" name="daterange" value="{{ date('Y-m-d', strtotime('yesterday')) }}"/>
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-forward fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span id="total_in"></span></div>
                                    <div>Total (In)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-gray">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-backward fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span id="total_out"></span></div>
                                    <div>Total (Out)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<h3>Visits <span id="date_from"></span></h3>
			<div id="visits"></div>
		</div>
	</div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            {!! $html->table() !!}
        </div>
    </div>

@stop

@section('javascript')
    <script>
        var graph;

        var callApi = function() {
            return $.get('/api/query',{
                period: $("input:radio[name=period]:checked").val(),
                device_id: '{{ $device->id }}',
                from: $("#daterange").val(),
                to: ''
            }, function(data) {
                graph = Morris.Bar({
                    // ID of the element in which to draw the chart.
                    element: 'visits',
                    // Chart data records -- each entry in this array corresponds to a point on
                    // the chart.
                    data: data,
                    // The name of the data record attribute that contains x-values.
                    xkey: 'name',
                    // A list of names of data record attributes that contain y-values.
                    ykeys: ['count_in', 'count_out'],
                    // Labels for the ykeys -- will be displayed when you hover over the
                    // chart.
                    labels: ['In', 'Out']
                });
            });
        };

        var updateData = function() {
            return $.get('/api/query',{
                period: $("input:radio[name=period]:checked").val(),
                device_id: '{{ $device->id }}',
                from: $("#daterange").val(),
                to: ''
            }, function(data) {
                graph.setData(data)
            });
        };

        var callTotalsApi = function() {
            return $.get('/api/query',{
                period: $("input:radio[name=period]:checked").val(),
                device_id: '{{ $device->id }}',
                from: $("#daterange").val(),
                to: '',
                totals: true
            }, function(data) {
                $('#total_in').text(data.in);
                $('#total_out').text(data.out);
                $('#date_from').text();
            });
        };

        callApi();
        callTotalsApi();
    </script>
    <script>
        $('input:radio[name=period]').on('change', function () {
            updateData();
            callTotalsApi();
        });

        $('#daterange').on('change', function () {
            updateData();
            callTotalsApi();
        });
    </script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        $('#daterange').datepicker({
            format: 'yyyy-mm-dd',
            todayBtn: true,
            autoclose: true
        });
    </script>
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    {!! $html->scripts() !!}
@stop
