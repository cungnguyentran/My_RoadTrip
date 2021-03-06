<?php
require("db.php");
session_start();

if(isset($_SESSION['auth'])){

}else {
    header('Location:signin.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RoadTripping</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script src="script.js"></script>
    <script src="todolist.js"></script>
    <script src="events_finder.js"></script>
    <script src="database.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgbouMURMuy_zBO2i2WZX_UqBppNMQPvY&libraries=places&callback=initMap" defer></script>
    <script>
        var data;
        var data2;
        function test(){
            $.ajax({
                url: 'http://localhost/Roadtrip/display_data_from_database.php',
                type: "POST",
                dataType: 'json',
                data: ({
                    id:<?php echo $_SESSION['user_id']?>
                }),
                success: function(result){
                    data = result;
                    console.log('cung',data);
//                    $('#origin-input').val(result[0].origin);
//                    $('#destination-input').val(result[0].destination);
                    var data_object = result[0];
                    var data_object_length = 0;
                    var origin = data_object.origin;
                    var destination =data_object.destination;

                    for(var j in data_object){
                        if(data_object.hasOwnProperty(j)){
                            data_object_length++;
                        }
                    }


                    var favorite  = $("<p>Places you chose along the route:  </p>");
                    if($("#myLastTripModal .modal-body").text().length == 22){

                        for(var k in data_object){
                            if(data_object[k] == 1 ){
                                for(var i=0; i < k.length ; i++){
                                    if(k[i] == "_"){
                                       var word =  k.substring(i+1)+", ";
                                    }
                                }
                                $(favorite).append(word);
                            }
                        }

                        var original_place  = $("<p>Your last location was </p>");
                        $(original_place).append(origin);

                        var destination_place  = $("<p>Your last destination was </p>");
                        $(destination_place).append(destination);

                        $("#myLastTripModal .modal-body").append(original_place, destination_place,favorite);
                    }else {
                        return false;
                    }



                }
            });
        }
    </script>
</head>
<body>
<div class="header text-center img-responsive">
    <a href=""><img src="images/road-trip-sign-clip-art-road-trip-svg-scrapbook-title-biFdsd-clipart.png" alt="road_trip_logo"></a>
    <div class="pull-right">
        <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown -->
            <li class="dropdown">
                <button class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <h5><span class="glyphicon glyphicon-user"><span class="caret"></span></span>
                    <?php echo $_SESSION['name']; ?></h5>
                </button>
                <ul class="dropdown-menu">
                    <li data-toggle="modal" data-target="#myModal"><a href="#">Profile</a></li>

                    <li><a href="http://localhost/Roadtrip/signout.php">Logout</a></li>
                    <!-- Modal -->

<!--                    end Modal-->
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
<!--        Modal edit profiles-->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Profile</h4>
                    </div>
                    <div class="modal-body">

                        <?php
                        if(isset($_POST['edit_profile'])){
                            $user_id = $_SESSION['user_id'];
                            $username = $_POST['username'];
                            $email = $_POST['email'];


                            $username = mysqli_real_escape_string($connection,$username);
                            $email = mysqli_real_escape_string($connection,$email);


                            $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = $user_id";
                            $result = mysqli_query($connection,$query);
                        }
                        if(!$connection){
                            mysqli_error($connection);
                        }else{
                            $user_id = $_SESSION['user_id'];
                            $read_query = "SELECT * FROM users WHERE id = $user_id";
                            $result = mysqli_query($connection, $read_query);
                            while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <div class="panel-body">
                            <form accept-charset="UTF-8" role="form" method="Post" action="" >
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" name="username" value= "<?php echo $row['username']?>" type="text">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" name="email" value="<?php echo $row['email']?>" type="email">
                                    </div>

                                    <input class="btn btn-lg btn-info btn-block" type="submit" name="edit_profile" value="Update Account">
                                </fieldset>
                            </form>
                        </div>
                        <?
                            }
                        }

                        ?>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
<!--        end modal-->
    </div>
</div>

<div class="col-sm-2 left_sidebar">

    <!--<h2 class="text-center">Menu</h2>-->
    <div class="left_sidebar_container text-center">
        <!-- first drop down -- Accommodations -->
        <div class="dropdown">
            <h6>ACCOMMODATIONS</h6>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-bed"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <p>Accommodations:</p>
                <li><a><input value="hotels" type="checkbox" name="acc_hotels"/>Hotels</a></li>
                <li><a><input value="motel" type="checkbox" name = "acc_motels"/>Motels</a></li>
                <li><a><input value="campground + rv_park" type="checkbox" name="acc_camping"/>Camping/RV Parks</a></li>
            </ul>
        </div>

        <!--second drop down Attractions-->
        <div class="dropdown">
            <h6>ATTRACTIONS</h6>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-camera"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <p>Attractions</p>
                <li><a><input value="amusement park" type="checkbox" name="att_amusement"/>Amusement Parks</a></li>
                <li><a><input value="museums" type="checkbox" name="att_museums"/>Museums</a></li>
                <li><a><input  value="zoo / aquarium" type="checkbox" name="att_zoo"/>Zoo/Aquarium</a></li>
            </ul>
        </div>

        <!-- Third drop down Outdoors and Recreation -->
        <div class="dropdown">
            <h6>OURDOORS AND RECREATION</h6>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-tree-conifer"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <p>Outdoors and Recreation:</p>
                <li><a><input value="beach" type="checkbox" name="out_beaches"/>Beaches</a></li>
                <li><a><input value="trails" type="checkbox" name="out_trails"/>Trails/Hikes</a></li>
                <li><a><input value="nationalParks" type="checkbox" name="out_parks"/>National Parks</a></li>
            </ul>
        </div>

        <!-- forth drop down Gas Stations and Service Stations -->
        <div class="dropdown">
            <h6>GAS AND SERVICE STATIONS</h6>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-scale"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <p>Gas and Service Stations</p>
                <li><a><input value="gas" type="checkbox" name="gas_gas"/>Gas Stations Only</a></li>
                <li><a><input value="car repair" type="checkbox" name="gas_service"/>Service Stations Only</a></li>

                <li><a><input  value="gas / car repair" type="checkbox"/>Gas and Service Stations</a></li>
            </ul>
        </div>

        <!-- fifth drop down food -->
        <div class="dropdown">
            <h6>FOOD</h6>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-cutlery"></span>
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <p>Food and Drink</p>
                <li><a><input value="restaurants" type="checkbox" name="food_restaurant"/>Restaurants</a></li>
                <li><a><input value="diners" type="checkbox" name="food_diners"/>Diners</a></li>
                <li><a><input value="fast food" type="checkbox" name="food_fastfood"/>Fast Food</a></li>
                <li><a><input value="health Food + health" type="checkbox" name="food_vegetarian"/>Vegetarian and Health Food</a></li>
                <li><a><input value="bars" type="checkbox" name="food_bars"/>Bars and Drinks</a></li>
                <li><a><input value="wineries/Breweries" type="checkbox" name="food_wineries"/>Wineries, Breweries and
                        Distilleries</a></li>
            </ul>
        </div>
        <div class="row">
            <button class="btn btn-info" id= "displayData2" type="button" data-toggle="modal" onclick="set_val_destination()">
                <span class="glyphicon glyphicon-globe" ></span>
                <span class="text">Find Events</span>
            </button>
<!--                todolist-->
            <button id="actionSubmit" type="button" class="btn btn-primary col-sm-12" data-dismiss="modal">Show Results
            </button>
            <button data-toggle="modal" data-target="#todo_list" class="btn btn-primary col-sm-12">Todo List
            </button>

        </div>
<!--        my last trip modal -->


        <div class="modal fade" id="myLastTripModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">My Last Trip Info. </h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

<!--        my last trip modal end-->
        <!-- Modal todolist -->
        <div class="modal fade" id="todo_list" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Todo List</h4>
                    </div>
                    <div class="modal-body" id="todolist_body">
                            <input type="text" id="myInput" placeholder="List Items...">
                            <button onclick="newElement()" class="addBtn btn btn-info">Add</button>

                        <ul id="myUL">
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="button_left_holder">

        <button id="save_places_to_database" type="button" class="btn btn-success col-sm-12" data-dismiss="modal">Save Trip
        </button>
        <button class="btn btn-info col-sm-12 my_last_trip"  data-toggle="modal" data-target="#myLastTripModal" onclick="test()">My Last Trip
        </button>
    </div>

    <!--event modal-->
    <div id="myModal2" class="modal fade" role="dialog" >
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="modal_content">
                <div class="modal-header" >
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">what would you like for event?</h4>
                </div>
                <div class="modal-body" id="modal_body">
                    <form>
                        <div class="input-group">
                            <span class="input-group-addon">City</span>
                            <input id="cityEvent" type="text" class="form-control" name="cityEvent" placeholder="Additional Info">
                        </div>
                        <input type="radio" name="choose" value="Comedy">Comedy<br>
                        <input type="radio" name="choose" value="Concerts and Tour Dates">Concerts and Tour Dates<br>
                        <input type="radio" name="choose" value="Conferences and Trade Shows">Conferences and Trade Shows<br>
                        <input type="radio" name="choose" value="Festivals">Festivals<br>
                        <input type="radio" name="choose" value="Food and Wine">Food and Wine<br>
                        <input type="radio" name="choose" value="Kids and Family">Kids and Family<br>
                        <input type="radio" name="choose" value="Nightlife and Singles">Nightlife and Singles<br>
                        <input type="radio" name="choose" value="Performing Arts">Performing Arts<br>
                        <input type="radio" name="choose" value="Sports">Sports<br>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="submit_event"  type="button" class="btn btn-default" data-dismiss="modal" onclick="/*getInformation()*/">Show Results</button>
                </div>
            </div>
        </div>
    </div>
    <!--end event modal-->

</div>
<!--input for map-->
<div id="inputsContainer">
    <input id="origin-input" class="col-sm-5" type="text"
           placeholder="Enter a location">
    <input id="destination-input" class="col-sm-5" type="text"
           placeholder="Enter a destination">
</div>

<div id="mode-selector" class="controls">
    <input name="type" id="changemode-driving">
    <label for="changemode-driving"></label>
</div>
<!--end input for map-->

<div class="main_container col-sm-8">
    <div id="map" class="">
    </div>

</div>

<div class="col-sm-2 right_sidebar">
    <div class="button_holder">
        <!--<button class="col-sm-6 btn btn-info text-center" id="getDirectionsButton" onclick="openNav3()">Directions</button>-->
        <button class="col-sm-6 btn btn-info text-center" id="getDirectionsButton" onclick="openNav3()">
            <span class="glyphicon glyphicon-road" ></span>
        </button>
        <button class="col-sm-6 btn btn-info text-center" onclick="getWeather()">
            <span class="glyphicon glyphicon-cloud" ></span>
        </button>
    </div>

    <!--direction-->
    <div class="direction_detail">
        <!--<div id="mySidenav3"></div>-->
        <div id="mySidenav3" class="sidenav3">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav3()">&times;</a>
            <!--<label id="directionsLabel"><input type="checkbox" id="traffic" onclick="showTraffic()"/>Show/Hide Traffic</label>-->
            <div id="directionsLabel" class="text-center">
                <label style="font-size: 2em">
                    <input id="traffic" onclick="showTraffic()" type="checkbox">
                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                    SHOW TRAFFIC
                </label>
            </div>
        </div>
    </div>
    <!--end direction-->

    <!--weather-->
    <div id="weatherDisplayContainer" class="col-sm-12 text-center">

        <div class="panelAccordian">
            <h6 class="accordion">Current Weather</h6>
            <div id="weatherImage"></div>
            <div id="weatherLocation"></div>
            <div id="weatherAlerts"></div>
            <div id="weatherTemp"></div>
            <div id="weatherHumidity"></div>
            <div id="weatherWind"></div>
        </div>

        <div class="panelAccordian">
            <h6 class="accordion" id="weatherDOW1"></h6>
            <div id="weatherIcon1"></div>
            <div id="weatherHigh1"></div>
            <div id="weatherLow1"></div>
            <div id="weatherWind1"></div>
        </div>
        <div class="panelAccordian">
            <h6 class="accordion" id="weatherDOW2"></h6>
            <div id="weatherIcon2"></div>
            <div id="weatherHigh2"></div>
            <div id="weatherLow2"></div>
            <div id="weatherWind2"></div>
        </div>
        <div class="panelAccordian">
            <h6 class="accordion" id="weatherDOW3"></h6>
            <div id="weatherIcon3"></div>
            <div id="weatherHigh3"></div>
            <div id="weatherLow3"></div>
            <div id="weatherWind3"></div>
        </div>
    </div>
    <!--end weather-->
</div>
</body>
</html>