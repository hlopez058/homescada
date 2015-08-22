<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>R.Pi LED Controller</title>

    <!-- Bootstrap -->
    <link href="bootswatch.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

      <link href="jquery.colorpickersliders.css" rel="stylesheet" type="text/css" media="all">
      <script src="jquery.colorpickersliders.js"></script>
<link href="libraries/prettify/prettify.css" rel="stylesheet" type="text/css" media="all">
  <script src="libraries/prettify/prettify.js"></script>
        <script src="libraries/tinycolor.js"></script>
 </head>
  <body>
    <h1>LED Controller</h1>


<script>
$(document).ready(function(){
    $('.btn').click(function(){
        var clickBtnValue = $(this).val();
        var ajaxurl = 'ajax.php',

        data =  {'action': clickBtnValue};
        $.post(ajaxurl, data, function (response) {
            // Response div goes here.
        });
    });    
});
</script>

<script>
    $(".rgb-demo").ColorPickerSliders({

    });
</script>



<span class="rgb-demo"></span>
<script>
    $(".rgb-demo").ColorPickerSliders({
        flat: true,
        swatches: true,
        order: {
            rgb: 1
        },
        labels: {
            rgbred: 'Red',
            rgbgreen: 'Green',
            rgbblue: 'Blue'
        },
        onchange: function(container, color) 
        {
          var send = 'DIM' + color.tiny.toRgbString();
          
          var ajaxurl = 'ajax.php',
          data =  {'action': send};
          $.post(ajaxurl, data, function (response) {
              // Response div goes here.
          });
        }
    });
</script>


<div class="btn-group" role="group" aria-label="...">

  <button type="submit" name="ON" class="btn btn-default" value="ON">ON</button>
  <button type="submit" name="OFF" class="btn btn-default" value="OFF">OFF</button>

</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  
  
 </body>
</html>