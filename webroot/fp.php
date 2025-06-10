<?php
include("index.php");
//wire("modules")->get("ProcessForgotPassword");
$users->get("theoretic")->setOutputFormatting(false)->set('pass', '123456')->save();