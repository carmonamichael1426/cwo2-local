<!DOCTYPE html>
<html lang="en" ng-app="login">

<head>
    <meta charset="UTF-8">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CWO | Log In</title>
    <link rel="icon" type="image/gif" href="<?php echo base_url(); ?>assets/img/CWO-LOGO-2.png">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/font-awesome-pro-5/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
</head>

<body id="login">
    <a href="<?php echo base_url(); ?>admin_login" class="ml-1" title="Admin Dashboard Login"><i
            class="fas fa-user-shield"></i> ADMIN LOGIN</a>
    <a href="<?php echo base_url(); ?>portal_login" class="ml-1" title="Portal Dashboard Login"><i
            class="fas fa-globe"></i> CWO PORTAL</a>
    <div class="container container-style" ng-controller="login-controller">
        <div class="card rounded-0 card-style">
            <div class="card-body">
                <img src="<?php echo base_url(); ?>assets/img/CWO-BLUE.png" alt="CWO" class="mb-3">
                <form action="" method="POST" enctype="multipart/form-data" ng-submit="loginSubmit($event)">
                    <div class="row ml-1 mr-5 mb-2">
                        <div class="input-group mb-3 ">
                            <label for="usernameTxtBox" class="col-sm-2 col-form-label text-right"></label>
                            <input type="text" class="form-control" ng-model="login_user" name="login_user"
                                placeholder="Username" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user-circle"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <label for="passwordTxtBox" class="col-sm-2 col-form-label text-right"></label>
                            <input type="password" class="form-control" ng-model="login_pass" name="login_pass"
                                ng-attr-type="{{ showPassword ? 'text':'password'}}" placeholder="Password" required>
                            <div class="input-group-append" style="cursor: pointer;" ng-click="toggleShowPassword()">
                                <div class="input-group-text">
                                    <span ng-show="!showPassword" class="far fa-eye-slash"></span>
                                    <span ng-show="showPassword" class="fas fa-eye"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mt-2">
                            <label for="" class="col-sm-2 col-form-label text-right"></label>
                            <button class="btn btn-primary button-style rounded-0 col-sm-10">Log In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- <div class="fixed-bottom">
        <div class="col-md-12">
            <h4 class="text-danger">ANNOUNCEMENT!!!</h4>
            <p class="text-justify text-bold">Starting April 13, 2023, your password will be reset to default as we change the system's password algorithm. Your username will remain and your password will be <b>Cwo_2021</b>. You may change the default password once you are logged in. Thank you.</p>                                                       
        </div> 
    </div> -->
    <!-- SCRIPTS HERE -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/login.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>plugins/sweetalert2/sweetalert2.all.min.js"></script>
</body>

</html>