<?php
//session_start();
if (!isset($_SESSION['apriori_toko_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "mining.php";

if(isset($_GET['cari'])) {
    $nama_pimpinan = $_GET['nama_pimpinan'];
}
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1>
                    Hasil
                </h1>
            </div><!-- /.page-header -->
            <?php
//object database class
$db_object = new database();

$pesan_error = $pesan_success = "";
if(isset($_GET['pesan_error'])){
    $pesan_error = $_GET['pesan_error'];
}
if(isset($_GET['pesan_success'])){
    $pesan_success = $_GET['pesan_success'];
}

$year_sql = "SELECT DISTINCT YEAR(start_date) as year FROM process_log ORDER BY year DESC";
$year_query = $db_object->db_query($year_sql);
            
$years = array();
while ($year_row = $db_object->db_fetch_array($year_query)) {
    $years[] = intval($year_row['year']);
}

// Filter data based on the selected year
$year_filter = isset($_GET['year']) ? intval($_GET['year']) : null;

$sql = "SELECT * FROM process_log";
if ($year_filter) {
    $sql .= " WHERE YEAR(start_date) = " . $year_filter;
}

$query=$db_object->db_query($sql);
$jumlah=$db_object->db_num_rows($query);
?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box">
                        <div class="widget-body">
                            <div class="widget-main">
                                <!-- Tambahkan form untuk filter -->
                                <form method="get" action="index.php">
                                    <input type="hidden" name="menu" value="hasil">
                                    <!-- Tambahkan input tersembunyi untuk menetapkan nilai menu -->
                                    <div class="form-group">
                                        <div class="col-lg-1 col-xl-1 col-md-3 col-12">
                                            <label for="yearFilter">Pilih Tahun:</label>
                                        </div>
                                        <div class="col-lg-2 col-xl-2 col-md-2 col-12">

                                            <select name="year" id="yearFilter" class="form-control">
                                                <option value="">Semua</option>
                                                <?php
                    foreach ($years as $year) {
                        $selected = $year_filter === $year ? 'selected' : '';
                        echo "<option value='" . $year . "' " . $selected . ">" . $year . "</option>";
                    }
                    ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-1 col-xl-1 col-md-3 col-12">
                                            <label for="">nama Pimpinan</label>
                                        </div>
                                        <div class="col-lg-2 col-xl-2 col-md-2 col-12">
                                            <input type="text" name="nama_pimpinan" required id="nama_pimpinan">
                                        </div>
                                        <input type="submit" name="cari" class="btn btn-primary" value="Filter">
                                        <a href="index.php?menu=hasil" class="btn btn-secondary">Reset</a>
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
                                                    <?php if(isset($_GET['cari'])) : ?>
                                                    <a href="export/CLP.php?id_process=<?= $row['id'] ?>&&start_date=<?= $row['start_date'] ?>&&end_date=<?= $row['end_date'] ?>&&nama_pimpinan=<?= $nama_pimpinan ?>"
                                                        class="btn btn-app btn-light
                                        btn-xs" target="blank">
                                                        <i class='ace-icon fa fa-print bigger-160'></i>
                                                        Print
                                                    </a>
                                                    <?php endif ?>
                                                    <?php
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