<?php
//session_start();
if (!isset($_SESSION['apriori_toko_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "mining.php";


//object database class
$db_object = new database();

$pesan_error = $pesan_success = "";
if(isset($_GET['pesan_error'])){
    $pesan_error = $_GET['pesan_error'];
}
if(isset($_GET['pesan_success'])){
    $pesan_success = $_GET['pesan_success'];
}

if(isset($_POST['cari'])) {

    $tanggal_awal = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];

    $sql = "SELECT
    *
    FROM
     process_log WHERE start_date >= '$tanggal_awal' AND end_date <= '$tanggal_akhir'";
$query=$db_object->db_query($sql);
$jumlah=$db_object->db_num_rows($query);


}else {

    $sql = "SELECT
        *
        FROM
         process_log ";
$query=$db_object->db_query($sql);
$jumlah=$db_object->db_num_rows($query);
}
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1>
                    Hasil
                </h1>
                <form action="" method="post">
                    <div class="row" style="margin-top: 20px;">
                        <h4 style="margin-left: 10px;">Filter data berdasarkan tanggal</h4>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-lg-1 col-xl-1 col-md-3 col-12">
                            <label for="">tanggal awal</label>
                        </div>
                        <div class="col-lg-3 col-xl-3 col-md-3 col-12">
                            <input type="date" name="tanggal_awal" required id="tanggal_awal">
                        </div>
                        <div class="col-lg-1 col-xl-1 col-md-3 col-12">
                            <label for="">tanggal akhir</label>
                        </div>
                        <div class="col-lg-3 col-xl-3 col-md-3 col-12">
                            <input type="date" name="tanggal_akhir" required id="tanggal_akhir">
                        </div>
                        <div class="col-lg-1 col-xl-1 col-md-1 col-12">
                            <button type="submit" name="cari" class="btn btn-success">cari</button>
                        </div>
                        <div class="col-lg-1 col-xl-1 col-md-1 col-12">
                            <a href="index.php?menu=hasil" class="btn btn-warning">tampilkan semua</a>
                        </div>

                    </div>
                </form>
            </div><!-- /.page-header -->


            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box">
                        <div class="widget-body">
                            <div class="widget-main">
                                <!--            <form method="post" action="">
                <div class="form-group">
                    <input name="submit" type="submit" value="Proses" class="btn btn-success">
                </div>
            </form>-->

                                <?php
            if (!empty($pesan_error)) {
                display_error($pesan_error);
            }
            if (!empty($pesan_success)) {
                display_success($pesan_success);
            }


            //echo "Jumlah data: ".$jumlah."<br>";
            if($jumlah==0){
                    echo "Data kosong...";
            }
            else{
            ?>
                                <table class='table table-bordered table-striped  table-hover'>
                                    <tr>
                                        <th>No</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Min Support</th>
                                        <th>Min Confidence</th>
                                        <th></th>
                                        <th>Pdf</th>
                                    </tr>
                                    <?php
                    $no=1;
                    while($row=$db_object->db_fetch_array($query)){
//                        if($no==1){
//                            echo "Min support: ".$row['min_support'];
//                            echo "<br>";
//                            echo "Min confidence: ".$row['min_confidence'];
//                        }
//                        $kom1 = explode(" , ",$row['kombinasi1']);
//                        $jika = implode(" Dan ", $kom1);
//                        $kom2 = explode(" , ",$row['kombinasi2']);
//                        $maka = implode(" Dan ", $kom2);
                            echo "<tr>";
                            echo "<td>".$no."</td>";
                            echo "<td>".format_date2($row['start_date'])."</td>";
                            echo "<td>".format_date2($row['end_date'])."</td>";
                            echo "<td>".$row['min_support']."</td>";
                            echo "<td>".$row['min_confidence']."</td>";
                            $view = "<a href='index.php?menu=view_rule&id_process=".$row['id']."'>View rule</a>";
                            echo "<td>".$view."</td>";
                            echo "<td>";     ?>
                                    <a href="export/CLP.php?id_process=<?= $row['id'] ?>&&start_date=<?= $row['start_date'] ?>&&end_date=<?= $row['end_date'] ?>"
                                        class="btn btn-app btn-light
                                        btn-xs" target="blank">
                                        <i class='ace-icon fa fa-print bigger-160'></i>
                                        Print
                                    </a> <?php
                            echo "<a href='hapus_hasil.php?id=".$row['id']."' "
                                . "class='btn btn-app btn-light btn-xs' target='blank'>
                                <i class='fa-solid fa-trash-can'></i>
                                hapus
                            </a>";
                            echo "</td>";
//                            echo "<td>Jika ".$jika.", Maka ".$maka."</td>";
//                            echo "<td>".price_format($row['confidence'])."</td>";
                        echo "</tr>";
                        $no++;
                    }
                    ?>
                                </table>
                                <?php
            }
            ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>