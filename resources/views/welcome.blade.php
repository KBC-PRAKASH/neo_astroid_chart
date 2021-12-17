<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"></script>
    <!-- Styles -->
    <style>
    html,
    body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        font-weight: 200;
        height: 100vh;
        margin: 0;
    }

    .full-height {
        height: 100vh;
    }

    .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }

    .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
    }

    .content {
        text-align: center;
    }

    .title {
        font-size: 84px;
    }

    .links>a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    .m-b-md {
        margin-bottom: 30px;
    }
    </style>
</head>

<body>
<div class="modal" id="loader" style="z-index: 9999999999;">
    <div class="modal-dialog" style="width:70px;top:40%;margin:0 auto;">
        <div class="modal-content"
            style="border-radius: 10px;background: transparent;border: none;box-shadow: none !important;">
            <div align="center" class="modal-body"
                style="border-radius: 10px;background: transparent;border: none;box-shadow: none !important;">
                <img alt="" src="{{ asset('loader.gif')}}" width="60">
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <h2 class="text-center mt-3">Asteroid - Neo Stats</h2>
        <hr />
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <form id="inputsForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input class="datepicker" name="from_date" id="from_date" data-date-format="mm/dd/yyyy">
                                <div class="error" id="from_date_err" style="color:red;font-size:14px;"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input class="datepicker" name="to_date" id="to_date" data-date-format="mm/dd/yyyy">
                                <div class="error" id="to_date_err" style="color:red;font-size:14px;"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary mt-4 _submitBtn">Submit</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <hr />
        <div class="row _show_a_section">
            <h6>Fastest Asteroid in km/h (Respective Asteroid ID & its speed) <span id="_show_fastest_astroid_speed" style="color:blue;"></span></h6>
        </div>
        <div class="row _show_a_section">
            <h6>Closest Asteroid (Respective Asteroid ID & its distance)<span id="_show_closest_astroid_distance" style="color:blue;"></span></h6>
        </div>
        <div class="row _show_a_section">
            <h6>Average Size of the Asteroids in kilometers<span id="_show_average_size_of_astroid" style="color:blue;"></span></h6>
        </div>
        <div class="row  updateCanvas">
            <canvas id="myChart" width="400" height="400"></canvas>
        </div>
    </div>

</body>
<script type="text/javascript">
$(document).ready(function() {
    $('._show_a_section').hide();

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        endDate: '-3d'
    });

    $(document).on('change', '#from_date', function() {
        $('#to_date').datepicker('setStartDate', $(this).val());
    });

    $('._submitBtn').click(() => {
        let $from_date = $('#from_date').val();
        let $to_date = $('#to_date').val();
        let formValid = true;

        if ($from_date == '') {
            $('#from_date_err').html('please select from date');
            formValid = false;
        } else {
            $('#from_date_err').html('');
        }

        if ($to_date == '') {
            $('#to_date_err').html('please select from date');
            formValid = false;
        } else {
            $('#to_date_err').html('');
        }

        if (formValid) {
            $.ajax({
                url: '{{URL("/")}}/get-data-from-api',
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'from_date': $from_date,
                    'to_date': $to_date
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#loader').modal('show');
                    $('#myChart').remove();
                    $('.updateCanvas').append('<canvas id="myChart" width="400" height="400"></canvas>');
                },
                success: function(resp) {
                    if(resp.status == 200){
                        $('#_show_fastest_astroid_speed').html(' Speed : '+resp.data.get_fastest_astroid_speed.speed_km+' || Asteroid ID : '+resp.data.get_fastest_astroid_speed.astroid_id);
                        $('#_show_closest_astroid_distance').html(' distance : '+resp.data.get_closest_astroid_distance.distance_km+' || Asteroid ID : '+resp.data.get_closest_astroid_distance.astroid_id);
                        $('#_show_average_size_of_astroid').html(' Average Size of the Astroid is : '+resp.data.average_size_of_astroid_km);
                        _generateChartForNeoStart(resp.data.result.near_earth_objects);
                    }else{
                        $('#from_date_err').html(resp.data.error_message);
                    }
                    
                   
                },
                complete: function() {
                    $('#loader').modal('hide');
                }
            });
        }

    });

    const _generateChartForNeoStart = (data, extra, errors) => {

        let mylabels = [];
        let chartData = [];
        let fastest_asteroid_km = [];
        let coolest_astroid_distance = [];
        let total_size_of_astroid = 0;
        let total_astroid_count = 0;    
        $.each(data, function(key, val) {
            mylabels.push(key.split('-').reverse().join('-'));
            chartData.push(val.length);

            // //speed
            // $.each(val, function(rvKey, rvVal){
            //     fastest_asteroid_km.push(rvVal.close_approach_data[0].relative_velocity.kilometers_per_hour);
            // });
            
            // //distance 
            // $.each(val, function(rvKey, rvVal){
            //     coolest_astroid_distance.push(rvVal.close_approach_data[0].miss_distance.kilometers);
            // });

            // // Average size 
            // $.each(val, function(rvKey, rvVal){
            //     total_size_of_astroid += rvVal.estimated_diameter.kilometers.estimated_diameter_max;
            //     total_astroid_count++;
            // });

        });

        // $('#_show_average_size_of_astroid').html(' Average Size of the Astroid is : '+total_size_of_astroid / total_astroid_count);

        // let _fastest_asteroid_km = Math.max.apply(Math,fastest_asteroid_km);
        // calculate_fastest_astroid_speed(data, _fastest_asteroid_km);

        // let _closest_astroid_distance = Math.min.apply(Math,coolest_astroid_distance);

        // _calculate_closest_astroid_distance(data, _closest_astroid_distance);

        const ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                
                labels: mylabels,
                datasets: [{
                    label: 'Total number of asteroids ',
                    data: chartData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        myChart.destroy();

        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                
                labels: mylabels,
                datasets: [{
                    label: 'Total number of asteroids ',
                    data: chartData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        $('._show_a_section').show();
    }

    const calculate_fastest_astroid_speed = ($data, fastest_asteroid_km) => {
        $.each($data, function(key, val) {
            //speed
            $.each(val, function(rvKey, rvVal){
                if(rvVal.close_approach_data[0].relative_velocity.kilometers_per_hour == fastest_asteroid_km){
                    $('#_show_fastest_astroid_speed').html(' Speed : '+fastest_asteroid_km+' || Asteroid ID : '+rvVal.id);
                }
            });
        });
    }
    const _calculate_closest_astroid_distance = ($data, closest_asteroid_distance) => {
        $.each($data, function(key, val) {
            $.each(val, function(rvKey, rvVal){
                if(rvVal.close_approach_data[0].miss_distance.kilometers == closest_asteroid_distance){
                    $('#_show_closest_astroid_distance').html(' distance : '+closest_asteroid_distance+' || Asteroid ID : '+rvVal.id);
                }
            });

            
        });
    }
});
</script>

</html>