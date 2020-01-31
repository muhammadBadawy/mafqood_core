@extends('adminlte::page')

@section('title', 'Mafqood')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

<div class="row">
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="ion ion-ios-paper"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Reports</span>
        <span class="info-box-number">{{ $reports_number }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-eye-open"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Face Stamps</span>
        <span class="info-box-number">{{ $stamps_number }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->

  <!-- fix for small devices only -->
  <div class="clearfix visible-sm-block"></div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-warning-sign"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Suspects</span>
        <span class="info-box-number">{{ $suspects_number }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Admina</span>
        <span class="info-box-number">{{ $admins_number }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>

<div class="row">
<div class="col-md-12">
  <!-- AREA CHART -->
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Missing</h3>

      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body">
      <div class="chart">
        <canvas id="myChart" width="400" height="150" style="height: 250px; width: 510px;" width="510" height="250"></canvas>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->

  <!-- DONUT CHART -->
  <div class="box box-danger">
    <div class="box-header with-border">
      <h3 class="box-title">Donut Chart</h3>

      <div class="box-tools pull-right">
      </div>
    </div>
    <div class="box-body">
      <canvas id="myChart2" style="height: 265px; width: 530px;" width="530" height="265"></canvas>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->

</div>

</div>
<!-- /.row -->


        <!-- /.box-body -->
        <script src="{{ asset('vendor/adminlte/vendor/jquery/dist/Chart.bundle.js') }}"></script>
<script>
var ctx = document.getElementById('myChart').getContext('2d');

labels = [
            @foreach($reports as $report)
              "{{$report->date}}",
            @endforeach
          ];

data = [
  @foreach($reports as $report)
    {{$report->count}},
  @endforeach
];

var myLineChart = new Chart(ctx, {"type":"line",
"data":
        {"labels":labels,
        "datasets":[
                {"label":
                    "My First Dataset",
                    "data":data,
                    "fill":false,
                    "borderColor":"rgb(75, 192, 192)",
                    "lineTension":0.1
                  }
                ]
        },
        "options":{}
});


var ctx2 = document.getElementById('myChart2').getContext('2d');

doughnut_data = [
  @foreach($doughnut_data as $doughnut_datum)
    @if($doughnut_datum['count'] != 0)
      {{$doughnut_datum['count']}},
    @endif
  @endforeach
];

labels = [
  @foreach($doughnut_data as $doughnut_datum)
    @if($doughnut_datum['count']  != 0)
      "{{$doughnut_datum['name']}}",
    @endif
  @endforeach
];

backgroundColor = [
  @foreach($doughnut_data as $doughnut_datum)
    @if($doughnut_datum['count']  != 0)
      "rgb({{ ($doughnut_datum['count']*rand(10,100)) % 255 }}, {{ ($doughnut_datum['count']*rand(10,100)) % 255 }}, {{ ($doughnut_datum['count']*rand(10,100)) % 255 }})",
    @endif
  @endforeach
];

data = {
    datasets: [{
        data: doughnut_data,
        backgroundColor: backgroundColor,
        borderWidth: 1,
        borderColor: '#000',
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    'labels': labels
};

var myDoughnutChart = new Chart(ctx2, {
    type: 'doughnut',
    data: data,
    options: {}
});

</script>
@stop
