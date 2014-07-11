<?php
//include 'models/patient_model.php';
//$patient_model = new patient_model();
//$patients = $patient_model->list_all(20);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>GRPHPSandbox</title>

        <!-- Bootstrap core CSS -->
        <link href="public/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="public/bootstrap/css/jumbotron-narrow.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="public/styles.css" rel="stylesheet">

    </head>

    <body>

        <div class="container">
            <div class="header">
                <ul class="nav nav-pills pull-right">
                    <li class="active"><a href="index.php">Manage advocate</a></li>
                    <li><a href="refer_friend_program.php">Refer a friend program</a></li>
                </ul>
                <h3 class="text-muted">GRPHPSandbox</h3>
            </div>

            <div class="jumbotron clearfix">
                <div class="header"><p>Make your search using this criteria</p></div>
                <form class="form-horizontal" role="form">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="inputName" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inputName" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputLastname" class="col-sm-4 control-label">Last name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inputLastname" placeholder="Last name">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="inputEmail" class="col-sm-4 control-label">Email</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button id="search_user" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row marketing">
                <div style="text-align: right; margin-bottom: 10px;">
                    <button id="btn_new_advocate" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span> New advocate</button>
                </div>
                <div id="new_advocate_container" class="jumbotron clearfix" style="display: none;">
                    <div class="header">
                        <p>New Advocate</p>
                        <button type="button" class="close" id="btn_close_advocate">&times;</button>
                    </div>
                    <form class="form-horizontal" role="form">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="inputName" class="col-sm-4 control-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="inputName" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputLastname" class="col-sm-4 control-label">Last name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="inputLastname" placeholder="Last name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail" class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="inputPayout" class="col-sm-4 control-label">Payout threshold</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="inputPayout" placeholder="Payout threshold">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputCurrency" class="col-sm-4 control-label">Currency</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="inputCurrency" placeholder="Currency">
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <input class="btn btn-primary" type="submit" value="Submit" id="btn1_new_advocate">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Name</th>
                            <th>Last name</th>
                            <th>Account</th>
                            <th>Campaign</th>
                            <th>Creation date</th>
                            <th>Actions</th>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>Last name</td>
                            <td>Account</td>
                            <td>Campaign</td>
                            <td>Creation date</td>
                            <td>Actions</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="footer">
                <ul class="nav nav-pills pull-left">
                    <li><a href="index.php">Manage advocate</a></li>
                    <li><a href="refer_friend_program.php">Refer a friend program</a></li>
                </ul>
                <div style="clear: both; text-align: center;">
                    <p>Copyright ©2014 GRPHPSandbox. All rights reserved.</p>
                </div>
            </div>

        </div> <!-- /container -->

        <!-- scripts at the bottom! -->
        <script src="public/jquery-2.0.3.min.js"></script>

        <!-- this script file is for global js -->
        <script src="public/script.js"></script>

        <!-- add bootstrap js -->
        <script src="public/bootstrap/js/bootstrap.min.js"></script>

    </body>
</html>

