<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>glambook</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

    <style type="text/css">
      .navbar > .container .navbar-brand {
        color: #5782DB;
        font-weight: 700;
      }

      /* tool bar*/
      .toolbar {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #333;
      }
      .toolbar .collection-id {
        display: inline-block;
        margin: 0;
        padding: 7px 0 0 0;
        color: #eee;
      }
      .toolbar .tags {
        padding-top: 4px;
      }
      .toolbar .label.stats {
        margin-left: 1em;
      }
      .toolbar .label.stats.first {
        margin-left: 2em;
      }

      .tab-pane {
        margin-top: 15px;
      }

      /* main column */
      .main .card {
        background-color: #f0f0f0;
      }
      .main .meta {
        padding: .5em 1em;
      }

      /* tool column */

      .comment {
        margin-right: 40px;
        border: 2px solid blue;

        padding: 0.5em;
        background-color: #f0f0f0;
        border-radius: 1px;
         cursor: pointer; 
         cursor: hand; 
      }
      
      .bad {
        margin-right: 0px;
        margin-left: 40px;
        opacity: 0.4;
        border: none;

      }
      .description {
        margin-top: 20px;
      }

      .nav-tabs {
        background-color: #666;
        border: none;
      }
      .nav-tabs li {
        margin: 0;
      }
      .nav-tabs li.active {
        background-color: #ccc;
      }
      .nav-tabs li a,
      .nav-tabs li.active a {
        margin: 0;
        border: none;
        background-color: transparent;
        color: #333;
      }
      .nav-tabs li a {
        color: #eee;
      }
      .nav-tabs li a:hover,
      .nav-tabs li.active a:hover {
        border: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        background-color: #333;
        color: #eee;
      }

      /* footer */

      footer p {
        margin-top: 2em;
        padding: 2em 0;
        border-top: 1px solid #f0f0f0;
      }

    </style>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>
  <body>
    <header>
      <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">glambook harvester</a>
          </div>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="list.html">Harvest</a></li>
          </ul>
        </div>
      </nav>
    </header>