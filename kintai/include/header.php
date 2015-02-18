<?php

//include_once("./util/constant.php");
//
//$_SESSION["HOME_PATH"] = $HOME_PATH;
//$_SESSION["CSS_PATH"] = $CSS_PATH;

?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width",initial-scale="1.0",maximum-scale="1.0",minimum-scale="1.0">
<!--    user-scalable=no"-->
<meta name="robots" content="none">


<title><?php echo title ?></title>

<?php if($deviceType == "SP" || $deviceType == "APP") { ?>
<!--    <link rel="StyleSheet" type="text/css" href="--><?php //echo cssPath."css/style_sp.css" ?><!--" >-->
    <link rel="StyleSheet" type="text/css" href=
        "<?php echo $_SESSION["HOME_PATH"].$_SESSION["CSS_SP_PATH"] ?>" >
<?php }else{ ?>
<!--    <link rel="StyleSheet" type="text/css" href="--><?php //echo cssPath."css/style.css" ?><!--">-->
    <link rel="StyleSheet" type="text/css" href=
        "<?php echo $_SESSION["HOME_PATH"].$_SESSION["CSS_PATH"] ?>">
<?php }?>
