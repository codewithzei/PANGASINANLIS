<?php
session_start();
session_unset();
session_destroy();
header("Location: /PangasinanLIS/pages/auth/login");
exit();
?>