<?php

require_once __DIR__ . '/../config/classes/Session.php';

Session::logout();

header('Location: http://localhost/controle-contas');