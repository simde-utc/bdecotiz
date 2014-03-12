<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $title; ?></title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <style>
        /* Sticky footer styles
        -------------------------------------------------- */
        html {
          position: relative;
          min-height: 100%;
        }
        body {
          /* Margin bottom by footer height */
          margin-bottom: 60px;
        }
        #footer {
          position: absolute;
          bottom: 0;
          width: 100%;
          /* Set the fixed height of the footer here */
          height: 60px;
          background-color: #f5f5f5;
        }


        /* Custom page CSS
        -------------------------------------------------- */
        /* Not required for template or sticky footer method. */

        .container {
          width: auto;
          max-width: 680px;
          padding: 0 15px;
        }
        .container .text-muted {
          margin: 20px 0;
        }
    </style>
  </head>

  <body>

    <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h1><?php echo $title; ?></h1>
      </div>
      <?php if($loggedin): ?>
        <p class="lead">Bonjour <?php echo $userInfo->prenom; ?> <?php echo $userInfo->nom; ?> !</p>
        <?php if($userInfo->is_cotisant): ?>
            <p>Félicitations tu es déjà <strong>cotisant</strong>.</p>
        <?php else: ?>
            <p>Tu n'es pas encore cotisant.</p>
            <a href="<?php echo $cotiseUrl; ?>" class="btn btn-success pull-right">Cotiser maintenant !</a><br /><br />
        <?php endif; ?>
        <a href="<?php echo $logoutUrl; ?>" class="btn btn-primary pull-right">Déconnexion</a>
      <? else: ?>
        <p class="lead"></p>
        <p>Cet outil te permet de cotiser au BDE-UTC en payant par internet. <br />
        Connecte toi pour regarder si tu es déjà cotisant.</p>
        <a href="<?php echo $loginUrl; ?>" class="btn btn-primary pull-right">Connexion</a>
      <?php endif; ?>
      <img src="img/bde.jpg" width="100%" />
    </div>

    <div id="footer">
      <div class="container">
        <p class="text-muted">Contact: bde@assos.utc.fr - Il y'a une vie après les cours.</p>
      </div>
    </div>
  </body>
</html>

