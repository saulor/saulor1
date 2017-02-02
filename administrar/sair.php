<?php
require_once ('config.php');
require_once ('core/ClassesLoader.php');
ClassesLoader::Register();
Session::init();
Session::destroy($_SESSION[PREFIX . 'permissoes']);
unset($_SESSION[PREFIX . "permissoes"]);
unset($_SESSION[PREFIX . "cidades"]);
unset($_SESSION[PREFIX . "cursos"]);
unset($_SESSION[PREFIX . "loginId"]);
unset($_SESSION[PREFIX . "loginNome"]);
unset($_SESSION[PREFIX . "loginPermissao"]);
unset($_SESSION[PREFIX . "mensagemSe"]);
unset($_SESSION[PREFIX . "tempoLogado"]);
unset($_SESSION[PREFIX . "acessouComo"]);
header("Location: " . SITEURL . 'administrar');
?>