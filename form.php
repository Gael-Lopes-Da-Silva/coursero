<?php

include_once "_define.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

include "_header.php";

?>



<?php

include "_footer.php";
