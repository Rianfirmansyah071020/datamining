<?php
include_once "../database.php";
include_once "../fungsi.php";
include_once "fpdf16/fpdf.php";
$id_process = $_REQUEST['id_process'];
$nama_pimpinan = $_REQUEST['nama_pimpinan'];
//object database class
$db_object = new database();

$sql_que = "SELECT
            conf.*, log.start_date, log.end_date
            FROM
             confidence conf, process_log `log`
            WHERE conf.id_process = '$id_process' "
            . " AND conf.id_process = log.id "
            . " AND conf.lolos=1 "
            . " ORDER BY conf.from_itemset DESC";

$db_query = $db_object->db_query($sql_que) or die("Query gagal");
//Variabel untuk iterasi
$i = 0;
//Mengambil nilai dari query database
while ($data = $db_object->db_fetch_array($db_query)) {
    $cell[$i][0] = price_format($data['confidence']);
    $cell[$i][1] = "Jika konsumen membeli ".$data['kombinasi1']
                    .", maka konsumen juga akan membeli ".$data['kombinasi2'];

    // Adding start date and end date to the $cell array
    $cell[$i]['start_date'] = $data['start_date'];
    $cell[$i]['end_date'] = $data['end_date'];

    $i++;
}

//memulai pengaturan output PDF
class PDF extends FPDF {

    //untuk pengaturan header halaman
    function Header() {
        //Pengaturan Font Header
        $this->SetFont('Times', 'B', 14); //jenis font : Times New Romans, Bold, ukuran 14
        //untuk warna background Header
        $this->SetFillColor(255, 255, 255);
        //untuk warna text
        $this->SetTextColor(0, 0, 0);
        //Menampilkan tulisan di halaman
        $this->Cell(0, 1, ' Bengkel Mustofa Motor', '', 0, 'C'); //TBLR (untuk garis)=> B = Bottom,
        $this->Ln();
        $this->Cell(0, 1, 'Jl.padang-painan, Kenagarian Nanggalo, Kec. Koto XI Tarusan, Kab.Pesisir Selatan, 25654', '', 0, 'C');
        $this->Ln(2);
    }

    //untuk pengaturan footer halaman
    private $isLastPage = false;

    function Footer()
    {
        // Posisi bagian bawah halaman
        $this->SetY(-5);
    
        // Pengaturan font footer
        $this->SetFont('Arial', 'I', 12);
    
        // Cek apakah halaman ini merupakan halaman terakhir
        if ($this->isLastPage) {
            // Menampilkan tanggal
            $this->Cell(28, 2, 'Nanggalo, ' . date('d/m/Y'), 0, 1, 'R');
    
            // Menampilkan teks "Pimpinan"
            $this->Cell(27, 2, 'Pimpinan', 0, 0, 'R');

            // Menentukan jarak atas sebelum mencetak teks "(Risman MH)"
            $this->SetY($this->GetY() + 0.5); // Atur jarak atas di sini, misalnya 0.5
    
            // Menampilkan nama "Risman MH"
            $this->Cell(27, 2, $_REQUEST['nama_pimpinan'], 0, 1, 'R');
        }
    }
    
    function Output($dest = '', $name = '', $isUTF8 = false)
    {
        // Menandai halaman terakhir
        $this->isLastPage = true;
    
        // Memanggil fungsi Output dari kelas induk
        parent::Output($dest, $name, $isUTF8);
    }
    

}

//pengaturan ukuran kertas P = Portrait
$pdf = new PDF('L', 'cm', 'A4');
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 1, 'Laporan Hasil Penjualan', '', 0, 'C');
$pdf->Ln();
$j = 0; // Inisialisasi variabel $j sebelum perulangan
if ($i > 0) { // Memastikan ada data dalam array $cell sebelum mencetak
    $start_date = $cell[$j]['start_date'];
    $end_date = $cell[$j]['end_date'];
    $date_range = 'Periode : ' . $_REQUEST['start_date'] . ' - ' . $_REQUEST['end_date'] ;
    $pdf->Cell(0, 1, $date_range, '', 0, 'C');
}
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(1, 1, 'No', 'LRTB', 0, 'C');
$pdf->Cell(24, 1, 'Rule', 'LRTB', 0, 'C');
$pdf->Cell(3, 1, 'Confidence', 'LRTB', 0, 'C');
$pdf->Ln();
$pdf->SetFont('Times', "", 10);
for ($j = 0; $j < $i; $j++) {
    //menampilkan data dari hasil query database
    $pdf->Cell(1, 1, $j + 1, 'LBTR', 0, 'C');
    $pdf->Cell(24, 1, $cell[$j][1], 'LBTR', 0, 'L');
    $pdf->Cell(3, 1, $cell[$j][0], 'LBTR', 0, 'C');
    $pdf->Ln();
}
//menampilkan output berupa halaman PDF
$pdf->Output();
?>