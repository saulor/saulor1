<?php
	require_once ('administrar/config.php');
	require_once ('administrar/core/ClassesLoader.php');
	ClassesLoader::Register();
	Session::init();
	require_once ('SiteDAO.php');
	require_once ('SiteController.php');
	require_once ('administrar/core/Routes.php');
?>