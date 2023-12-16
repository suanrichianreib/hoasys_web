<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title><?php echo $title . ' | HOASYS'; ?>
    </title>
    <meta name="description" content="Initialized via remote ajax json data">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <link href="<?php echo base_url(); ?>assets/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/src/custom/css/fonts/poppins/poppins.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo base_url(); ?>assets/src/custom/css/fonts/roboto/roboto.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo base_url(); ?>assets/src/custom/css/fonts/pacifico/pacifico.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo base_url(); ?>assets/src/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/src/demo/default/base/style.bundle.css" rel="stylesheet"
        type="text/css">
    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo base_url(); ?>assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo base_url(); ?>assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?php echo base_url(); ?>assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo base_url(); ?>assets/images/favicon/site.webmanifest">
    <script src="<?php echo base_url(); ?>assets/js/app.js" type="text/javascript"></script>
    <!-- <script>
		< meta name = "msapplication-TileColor"
		content = "#da532c" > < meta name = "theme-color"
		content = "#ffffff" > < script src =
			"<?php echo base_url(); ?>assets/js/app.js" type =
	"text/javascript" > </script> -->
    <script>
    var sys_env = "<?php echo ENVIRONMENT; ?>";
    </script>
    <script src="<?php echo base_url(); ?>assets/src/custom/plugins/moment/moment-timezone.js">
    </script>
    <script src="<?php echo base_url(); ?>assets/src/custom/plugins/moment/moment-timezone-with-data.js">
    </script>
    <style>
    html {
        background: black;
    }

    body {
        /* background-image: url(<?php echo base_url("/assets/images/default/moving6.jpg");
                                        ?>); */
        /* background-position: center;
        background-size: cover;
        background-repeat: no-repeat; */
        background-color:white !important;
        margin: 0;
    }

    .m-grid__item.m-grid__item--fluid.m-login__wrapper {
        margin: auto !important;
        flex: none !important;
        padding: 0 !important;
    }

    .padding-Box {
        padding-left: 50px;
        padding-right: 50px;
        padding-top: 45px;
        padding-bottom: 40px;
    }

    .signinDes {
        background: rgba(193, 193, 193, 0.36);
        width: 40em !important;
        background-size: 100% 100%;
        border-radius: 1em;
    }

    #myVideo {
        position: fixed;
        object-fit: cover;
        height: 100vh;
        width: 100vw;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    .content {
        z-index: 1;
    }
    </style>
</head>

<body
    class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    <!-- <video autoplay muted loop id="myVideo">
        <source src="<?php echo base_url('assets/vid/Login_Loop2.mp4'); ?>" type="video/mp4">
    </video> -->
    <div class="m-grid m-grid--hor m-grid--root m-page content">

        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2"
            id="m_login">
            <div class="mt-5" style="text-align: center;z-index: 99;">
                <img class="mx-auto d-block" src="<?php echo base_url("/assets/images/hoasys.png") ?>"
                    style="width:25%" />
            </div>
            <div class="m-grid__item m-grid__item--fluid	m-login__wrapper" style="margin-top: -4rem!important;">

                <div class="m-login__container signinDes" style="margin-top:5em;">
                    <div class="m-login__signin">
                        <div id="showError" style="display:none">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="font-size:small;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                </button>
                                <strong>Oh snap!</strong> Username and Password mismatched!
                            </div>
                        </div>
                        <form class="m-login__form m-form padding-Box" style="margin: 0 auto;" action="#">
                            <div class="form-group m-form__group">
                                <input class="form-control m-input" type="text" placeholder="Username" name="email"
                                    id="email" autocomplete="off">
                            </div>
                            <div class="form-group m-form__group">
                                <input class="form-control m-input" type="password" placeholder="Password"
                                    name="password" id="password" autocomplete="off">
                            </div>
                            <div class="m-login__form-action" style="margin-top: 4% !important;">
                                <button id="m_login_signin_submit"
                                    class="btn btn-primary  m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">Log
                                    In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url(); ?>assets/src/vendors/base/vendors.bundle.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/src/demo/default/base/scripts.bundle.js" type="text/javascript">
    </script>
    <!-- <script src="<?php echo base_url(); ?>assets/snippets/pages/user/login.js"
	type="text/javascript"></script> -->

</body>

</html>
<script type="text/javascript">
$('#m_login_signin_submit').click((e) => {
    e.preventDefault();
    let email = $('#email').val();
    let password = $('#password').val();
    $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>login/login_authentication",
        dataType: "JSON",
        data: {
            email,
            password
        },
        success: function(data) {
            if (data === 'Success') {
                window.location.href =
                    "<?= base_url() ?>/login/check_login_access";
                $("#showError").css("display", "none");
            } else {
                $("#showError").css("display", "block");
            }
        }
    })
})
</script>