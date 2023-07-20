<?php
$conn = mysqli_connect("localhost", "root", "", "apriori_toko_parfum");

$id = $_GET['id'];

    $delete = mysqli_query($conn, "DELETE FROM process_log WHERE id='$id'");
    if(mysqli_affected_rows($conn)) {
    echo "<script>
    alert('berhasil');
    document.location.href = 'index.php?menu=hasil';
    </script>";
    }

    // if(!$conn) {
    
    // }

?>