<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//$targ_w = $targ_h = 150;
	$targ_w = $_POST['w'];
	$targ_h = $_POST['h'];
	$jpeg_quality = 90;

	$src = 'images/pool.jpg';
	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
	$targ_w,$targ_h,$_POST['w'],$_POST['h']);

	header('Content-type: image/jpeg');
	imagejpeg($dst_r,null,$jpeg_quality);

	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Basic Handler | Jcrop Demo</title>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<script src="cgi-bin/jquery.min.js"></script>
<script src="cgi-bin/jquery.Jcrop.js"></script>
<!--script type="text/javascript">

  jQuery(function($){

    var jcrop_api;

    $('#target').Jcrop({
      onChange:   showCoords,
      onSelect:   showCoords,
      onRelease:  clearCoords
    },function(){
      jcrop_api = this;
    });

    $('#coords').on('change','input',function(e){
      var x1 = $('#x1').val(),
          x2 = $('#x2').val(),
          y1 = $('#y1').val(),
          y2 = $('#y2').val();
      jcrop_api.setSelect([x1,y1,x2,y2]);
    });

  });

  // Simple event handler, called from onChange and onSelect
  // event handlers, as per the Jcrop invocation above
  function showCoords(c)
  {
    $('#x1').val(c.x);
    $('#y1').val(c.y);
    $('#x2').val(c.x2);
    $('#y2').val(c.y2);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function clearCoords()
  {
    $('#coords input').val('');
  };



</script-->
<link rel="stylesheet" href="cgi-bin/main.css" type="text/css" />
<link rel="stylesheet" href="cgi-bin/demos.css" type="text/css" />
<link rel="stylesheet" href="cgi-bin/jquery.Jcrop.css" type="text/css" />
<script type="text/javascript">

  $(function(){

    $('#cropbox').Jcrop({
      aspectRatio: 0,
      onSelect: updateCoords
    });

  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };

</script>
</head>
<body border="0" cellpadding="0" cellspacing="0">

<!--div class="container">
<div class="row">
<div class="span12">
<div class="jc-demo-box"-->

  <!-- This is the image we're attaching Jcrop to -->
  <!--img src="images/sago.jpg" id="target" alt="[Jcrop Example]" /-->
  <img src="images/pool.jpg" id="cropbox" />

  <!-- This is the form that our event handler fills -->
  <!--form id="coords"
    class="coords"
    onsubmit="return false;"
    action="cpmwizcrop.php">

    <div class="inline-labels">
    <label>X1 <input type="text" size="4" id="x1" name="x1" /></label>
    <label>Y1 <input type="text" size="4" id="y1" name="y1" /></label>
    <label>X2 <input type="text" size="4" id="x2" name="x2" /></label>
    <label>Y2 <input type="text" size="4" id="y2" name="y2" /></label>
    <label>W <input type="text" size="4" id="w" name="w" /></label>
    <label>H <input type="text" size="4" id="h" name="h" /></label>
    </div>
  </form-->
  
		<form action="cpmwizcrop.php" method="post" onsubmit="return checkCoords();">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="submit" value="Crop Image" class="btn btn-large btn-inverse" />
		</form>  

<div class="clearfix"></div>

<!--/div>
</div>
</div>
</div-->

</body>
</html>

