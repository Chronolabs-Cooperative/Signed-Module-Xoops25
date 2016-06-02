<?php
	include_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'common.php';;
	header('Context-type: image/gif');
	readsignedArrays::getFile(_PATH_PROCESSES . DIRECTORY_SEPARATOR . constant('_SIGNED_CONFIG_LANGUAGE') . DIRECTORY_SEPARATOR . _SIGNED_WATERMARK_GIF);
	exit(0);
?>