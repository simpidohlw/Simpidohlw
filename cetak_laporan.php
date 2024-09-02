<?php
require('fpdf/fpdf.php');
require('config/koneksi.php');

$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

// Temukan pegawai dengan nilai tertinggi di tahun yang dipilih
$query_max = "SELECT id_pegawai, MAX(nilai) AS nilai_tertinggi 
              FROM penilaian 
              WHERE YEAR(tanggal) = '$tahun'
              GROUP BY id_pegawai 
              ORDER BY nilai_tertinggi DESC 
              LIMIT 1";
$result_max = $conn->query($query_max);
$pegawai_terbaik = $result_max->fetch_assoc();
$id_pegawai_terbaik = $pegawai_terbaik['id_pegawai'];

$query = "SELECT peg.id_pegawai, peg.nama, peg.nip, peg.jenis_kelamin, peg.no_handphone, 
                 peg.lama_bekerja, peg.pendidikan, peg.jabatan, p.nilai, p.tanggal, h.hasil, 
                 IF(peg.id_pegawai = '$id_pegawai_terbaik', 'Ya', '-') as penerima_reward
          FROM pegawai peg
          LEFT JOIN penilaian p ON peg.id_pegawai = p.id_pegawai AND YEAR(p.tanggal) = '$tahun'
          LEFT JOIN hasil_penilaian h ON p.id_penilaian = h.id_penilaian";
$result = $conn->query($query);

if ($format == 'pdf') {
    class PDF extends FPDF
    {
        function Header()
        {
            global $tahun;
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Laporan Tahunan - Tahun ' . $tahun, 0, 1, 'C');
            $this->Ln(10);
            $this->SetFont('Arial', 'B', 8);
           
            $this->Cell(25, 10, 'Nama', 1);
            $this->Cell(25, 10, 'NIP', 1);
            $this->Cell(15, 10, 'Jenis Kel.', 1);
            $this->Cell(25, 10, 'No. HP', 1);
            $this->Cell(20, 10, 'Lama Bekerja', 1);
            $this->Cell(20, 10, 'Pendidikan', 1);
            $this->Cell(20, 10, 'Jabatan', 1);
            $this->Cell(15, 10, 'Nilai', 1);
            $this->Cell(25, 10, 'Tanggal', 1);
            $this->Cell(20, 10, 'Hasil', 1);
            $this->Cell(25, 10, 'Reward', 1);
            $this->Ln();
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 8);

    while ($row = $result->fetch_assoc()) {
       
        $pdf->Cell(25, 10, $row['nama'], 1);
        $pdf->Cell(25, 10, $row['nip'], 1);
        $pdf->Cell(15, 10, $row['jenis_kelamin'], 1);
        $pdf->Cell(25, 10, $row['no_handphone'], 1);
        $pdf->Cell(20, 10, $row['lama_bekerja'], 1);
        $pdf->Cell(20, 10, $row['pendidikan'], 1);
        $pdf->Cell(20, 10, $row['jabatan'], 1);
        $pdf->Cell(15, 10, $row['nilai'], 1);
        $pdf->Cell(25, 10, $row['tanggal'], 1);
        $pdf->Cell(20, 10, $row['hasil'], 1);
        $pdf->Cell(25, 10, $row['penerima_reward'], 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'Laporan_Tahunan_' . $tahun . '.pdf');
} elseif ($format == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Tahunan_$tahun.xls");
    echo '<table border="3 solid black">';
    echo '<tr>';
   
    echo '<th>Nama</th>';
    echo '<th>NIP</th>';
    echo '<th>Jenis Kelamin</th>';
    echo '<th>No. Handphone</th>';
    echo '<th>Lama Bekerja</th>';
    echo '<th>Pendidikan</th>';
    echo '<th>Jabatan</th>';
    echo '<th>Nilai</th>';
    echo '<th>Tanggal</th>';
    echo '<th>Hasil</th>';
    echo '<th>Reward</th>';
    echo '</tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        
        echo '<td>' . $row['nama'] . '</td>';
        echo '<td>' . $row['nip'] . '</td>';
        echo '<td>' . $row['jenis_kelamin'] . '</td>';
        echo '<td>' . $row['no_handphone'] . '</td>';
        echo '<td>' . $row['lama_bekerja'] . '</td>';
        echo '<td>' . $row['pendidikan'] . '</td>';
        echo '<td>' . $row['jabatan'] . '</td>';
        echo '<td>' . $row['nilai'] . '</td>';
        echo '<td>' . $row['tanggal'] . '</td>';
        echo '<td>' . $row['hasil'] . '</td>';
        echo '<td>' . $row['penerima_reward'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
?>
