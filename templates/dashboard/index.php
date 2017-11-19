<!DOCTYPE html>
<html slick-uniqueid="3" xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" dir="ltr" lang="ru-ru">
<head>
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="keywords" content="">
  <meta name="rights" content="<?php echo ST_NAME; ?>">
  <meta name="author" content="Alexander Smirnov">
  <meta name="description" content="">
  <title><?php echo $application->getPageTitle() ?></title>
  <link href="templates/dashboard/image/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
  <link rel="stylesheet" href="templates/dashboard/css/bootstrap.css" type="text/css">
  <link rel="stylesheet" href="templates/dashboard/css/dashboard.css" type="text/css">
  <link rel="stylesheet" href="templates/dashboard/css/index.css" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="templates/dashboard/js/bootstrap.js" type="text/javascript"></script>
  <script src="templates/dashboard/js/bootstrap-wysiwyg.js" type="text/javascript"></script>
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
	
  <?php if(/*$application->getUserAccessLevel() == 10*/true) : ?>

  <!-- Nav -->
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">						
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php"><?php echo ST_NAME; ?></a>
      </div>
      <div class="collapse navbar-collapse">
        <?php echo $application->getMenu('main'); ?>
        <?php //echo $application->getComponent('usermenu'); ?>
      </div>
    </div>
  </div> <!-- Nav -->    

  <div class="container-fluid">
    <div class="row">

      <!-- left menu -->
      <div class="col-sm-3 col-md-2 sidebar">
        <?php echo $application->getMenu('admin'); ?>
        <hr>
        <?php echo $application->getMenu('adminadvert'); ?>
      </div> <!-- left menu -->

      <!-- content -->
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <?php echo $application->getPageContent(); ?>	
        <?php echo $application->getComponent('formCallback'); ?>
        <!--<?php //if ((include "plugins/tinymce/index.php") == 'OK') : ?>	
        <form method="post">
                        <textarea>Проверка проверка</textarea>
        </form>-->
        <?php //endif; ?>
      </div> <!-- content -->

    </div>
  </div>
  <?php else: ?>	
    <p><center><strong>Необходимо выполнить вход под учетной записью администратора</center></strong></p>
    <div class="container" style="max-width: 330px;">
      <?php echo $application->getMsgHTML() ?>
      <?php //echo $application->getComponent('user', array('user'), true); ?>
    </div>
  <?php endif; ?>
</body>
</html>