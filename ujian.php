<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status'])) { 
    header("location:login.php"); 
    exit; 
}

if (!isset($_GET['id']) || empty($_GET['id'])) { 
    header("location:index.php?pesan=mk_tidak_diset"); 
    exit; 
}

$id_mk = mysqli_real_escape_string($conn, $_GET['id']);
$query_mk = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id_mk = '$id_mk'");
$data_mk = mysqli_fetch_assoc($query_mk);

if (!$data_mk) { 
    header("location:index.php?pesan=mk_tidak_ditemukan"); 
    exit; 
}

$durasi_menit = (isset($data_mk['durasi']) && $data_mk['durasi'] > 0) ? (int)$data_mk['durasi'] : 30;
$durasi_detik = $durasi_menit * 60;
$soal_query = mysqli_query($conn, "SELECT * FROM soal WHERE id_mk = '$id_mk' ORDER BY id_soal ASC");
$total_soal = mysqli_num_rows($soal_query);
$soal_array = [];
while($row = mysqli_fetch_assoc($soal_query)) {
    $soal_array[] = $row;
}

if (isset($_POST['selesai']) || isset($_POST['is_timeout'])) {
    $benar = 0; 
    $id_user = $_SESSION['id_user']; 

    // Cek apakah sudah pernah ujian
    $cek_sudah_ujian = mysqli_query($conn, "SELECT id_nilai FROM nilai_ujian WHERE id_user = '$id_user' AND id_mk = '$id_mk'");
    if (mysqli_num_rows($cek_sudah_ujian) > 0) {
        header("location:index.php?pesan=sudah_ujian");
        exit;
    }

    $jawaban_user_simpan = $_POST['jawaban'] ?? [];

    if (!empty($jawaban_user_simpan)) {
        foreach ($jawaban_user_simpan as $id_soal => $jawaban_user) {
            $id_soal = mysqli_real_escape_string($conn, $id_soal);
            $jawaban_val = mysqli_real_escape_string($conn, $jawaban_user);

            mysqli_query($conn, "INSERT INTO jawaban_mhs (id_user, id_mk, id_soal, jawaban) 
                                 VALUES ('$id_user', '$id_mk', '$id_soal', '$jawaban_val')");

            $q_cek = mysqli_query($conn, "SELECT jawaban_benar, opsi_a FROM soal WHERE id_soal = '$id_soal'");
            $d_cek = mysqli_fetch_assoc($q_cek);

            if (!empty($d_cek['opsi_a'])) { // Jika soal PG
                if ($d_cek['jawaban_benar'] == $jawaban_user) {
                    $benar++;
                }
            }
        }
    }
    $skor = ($total_soal > 0) ? ($benar / $total_soal) * 100 : 0;
    $salah = $total_soal - $benar;
    $tanggal = date("Y-m-d H:i:s");
    $query_simpan = "INSERT INTO nilai_ujian (id_user, id_mk, skor, jumlah_benar, jumlah_salah, tanggal_ujian) 
                     VALUES ('$id_user', '$id_mk', '$skor', '$benar', '$salah', '$tanggal')";
    mysqli_query($conn, $query_simpan);
    header("location:review_ujian.php?id=$id_mk");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: <?php echo htmlspecialchars($data_mk['nama_mk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('selected-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <link rel="stylesheet" href="style_theme.css">
    <style>
        :root { --primary: #3498db; --success: #2ecc71; --danger: #e74c3c; --warning: #f1c40f; }
        body { 
            -webkit-user-select: none; /* Safari */
    -ms-user-select: none; /* IE 10+ */
    user-select: none; /* Standard syntax */
    -webkit-touch-callout: none; /* iOS Safari */
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--body-bg); 
            color: var(--text-color); 
            margin: 0; 
        }
        input, textarea {
    -webkit-user-select: text;
    -ms-user-select: text;
    user-select: text;
}
        .exam-header { background: var(--container-bg); padding: 15px 5%; position: sticky; top: 0; z-index: 1000; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); }
        .timer-box { background: var(--danger); color: white; padding: 10px 20px; border-radius: 12px; font-weight: 800; font-size: 1.1rem; }
        .container { padding: 40px 5%; max-width: 900px; margin: auto; }
        .question-step { display: none; }
        .question-step.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .question-card { background: var(--container-bg); border-radius: 24px; padding: 35px; border: 1px solid var(--border-color); margin-bottom: 25px; box-shadow: var(--card-shadow); position: relative;}
        .q-number { background: var(--primary); color: white; padding: 6px 18px; border-radius: 10px; font-weight: 700; margin-bottom: 20px; display: inline-block; }
        .option-label { display: flex; align-items: center; padding: 18px 22px; margin-bottom: 15px; border-radius: 14px; border: 1px solid var(--border-color); cursor: pointer; background: var(--input-bg); }
        .option-label:hover { border-color: var(--primary); transform: translateX(5px); }
        .nav-buttons { display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap; }
        .btn { flex: 1; padding: 16px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; text-transform: uppercase; min-width: 150px; }
        .btn-back { background: var(--input-bg); color: var(--text-color); border: 1px solid var(--border-color); }
        .btn-next, .btn-submit { background: var(--success); color: white; }
        .btn-clear { background: var(--warning); color: #000; font-size: 0.8rem; padding: 10px; margin-top: 10px; flex: none; width: fit-content; }
        
    </style>
</head>
<body>

<div class="exam-header">
    <div class="mk-info">
        <h2 style="margin:0; font-size: 1.2rem; font-weight:800; color:var(--primary);"><?php echo htmlspecialchars($data_mk['nama_mk']); ?></h2>
        <small id="progress-text" style="font-weight:700;">Soal 1 dari <?php echo $total_soal; ?></small>
    </div>
    <div id="timer" class="timer-box">00:00:00</div>
</div>

<div class="container">
    <?php if ($total_soal > 0): ?>
    <form action="" method="POST" id="examForm">
        <input type="hidden" name="is_timeout" id="timeout_flag" value="">
        
        <?php foreach($soal_array as $index => $s): ?>
        <div class="question-step <?php echo $index === 0 ? 'active' : ''; ?>" id="step-<?php echo $index; ?>" data-soal-id="<?php echo $s['id_soal']; ?>">
            <div class="question-card">
                <span class="q-number">Soal <?php echo $index + 1; ?></span>
                <p style="font-size: 1.15rem; line-height: 1.7; font-weight:600;"><?php echo nl2br(htmlspecialchars($s['pertanyaan'])); ?></p>
                
                <div class="options-group">
                    <?php if (!empty($s['opsi_a'])): ?>
                        <?php foreach(['a', 'b', 'c', 'd'] as $opt): ?>
                        <label class="option-label">
                            <input type="radio" name="jawaban[<?php echo $s['id_soal']; ?>]" value="<?php echo $opt; ?>" onclick="saveAnswer('<?php echo $s['id_soal']; ?>', '<?php echo $opt; ?>')">
                            <span style="margin-left: 15px;"><strong style="color:var(--primary);"><?php echo strtoupper($opt); ?>.</strong> <?php echo htmlspecialchars($s["opsi_$opt"]); ?></span>
                        </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <textarea 
    name="jawaban[<?php echo $s['id_soal']; ?>]" 
    class="essai-input" 
    placeholder="Tulis jawaban Anda di sini..." 
    style="
        width: 100%; 
        min-height: 200px; 
        padding: 20px; 
        border-radius: 15px; 
        border: 1px solid var(--border-color); 
        background: var(--input-bg); 
        color: var(--text-color); 
        font-family: inherit; 
        font-size: 1rem; 
        resize: vertical;
        display: block; /* Menghilangkan behavior inline */
        box-sizing: border-box; /* Memastikan padding tidak merusak lebar 100% */
        margin-top: 15px; /* Memberi jarak dari teks soal */
        outline: none; /* Menghapus outline default browser saat fokus */
    " 
    oninput="saveAnswer('<?php echo $s['id_soal']; ?>', this.value)"></textarea>
                    <?php endif; ?>
                </div>

                <!-- Tombol Hapus Jawaban -->
                <button type="button" class="btn btn-clear" onclick="clearAnswer('<?php echo $s['id_soal']; ?>', <?php echo $index; ?>)">
                    <i class="fa-solid fa-eraser"></i> Hapus Jawaban
                </button>
            </div>

            <div class="nav-buttons">
                <?php if ($index > 0): ?>
                    <button type="button" class="btn btn-back" onclick="changeStep(<?php echo $index - 1; ?>)"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                <?php endif; ?>

                <?php if ($index < $total_soal - 1): ?>
                    <button type="button" class="btn btn-next" onclick="changeStep(<?php echo $index + 1; ?>)">Lanjut <i class="fa-solid fa-arrow-right"></i></button>
                <?php else: ?>
                    <button type="button" class="btn btn-submit" onclick="validateAndSubmit()"><i class="fa-solid fa-paper-plane"></i> Selesaikan</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </form>
    <?php endif; ?>
</div>
    

<script>
    let tabViolationCount = 0;
    let isTabLocked = false;
    const ACCESS_CODE = "ADMIN123"; 
    
    let currentStep = 0;
    const totalSoal = <?php echo $total_soal; ?>;
    const mkId = "<?php echo $id_mk; ?>";
    const timerDisplay = document.getElementById('timer');

    function changeStep(step) {
        document.getElementById(`step-${currentStep}`).classList.remove('active');
        document.getElementById(`step-${step}`).classList.add('active');
        currentStep = step;
        document.getElementById('progress-text').innerText = `Soal ${currentStep + 1} dari ${totalSoal}`;
        
        localStorage.setItem('exam_current_step_' + mkId, step);
        window.scrollTo(0, 0);
    }

    function saveAnswer(soalId, pilihan) {
        let savedAnswers = JSON.parse(localStorage.getItem('exam_answers_' + mkId)) || {};
        if (pilihan.trim() === "") {
            delete savedAnswers[soalId];
        } else {
            savedAnswers[soalId] = pilihan;
        }
        localStorage.setItem('exam_answers_' + mkId, JSON.stringify(savedAnswers));
    }
    function clearAnswer(soalId, index) {
        const radios = document.querySelectorAll(`input[name="jawaban[${soalId}]"]`);
        radios.forEach(r => r.checked = false);
        const textarea = document.querySelector(`textarea[name="jawaban[${soalId}]"]`);
        if (textarea) textarea.value = "";
        saveAnswer(soalId, "");
        Swal.fire({
            icon: 'info',
            title: 'Jawaban Dihapus',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        });
    }

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !isTabLocked) {
            lockExam();
        }
    });
    function lockExam() {
        isTabLocked = true;
        let attempts = 0;

        Swal.fire({
            title: 'Pelanggaran Terdeteksi!',
            text: 'Anda meninggalkan halaman ujian. Masukkan Kode Akses dari Pengawas:',
            input: 'text',
            inputAttributes: { autocapitalize: 'off' },
            showCancelButton: false,
            confirmButtonText: 'Buka Kunci',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showLoaderOnConfirm: true,
            preConfirm: (code) => {
                let formData = new FormData();
                formData.append('kode', code);
                formData.append('id_mk', mkId);

                return fetch('cek_kode.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        attempts++;
                        if (attempts >= 3) {
                            forceSubmitExam("Terlalu banyak kesalahan kode akses.");
                            throw new Error('Limit reached');
                        }
                        throw new Error(`Kode Salah! Sisa percobaan: ${3 - attempts}`);
                    }
                    return true;
                })
                .catch(error => {
                    if (error.message !== 'Limit reached') {
                        Swal.showValidationMessage(error.message);
                    }
                });
            }
        }).then((result) => {
            if (result && result.isConfirmed) {
                isTabLocked = false;
                Swal.fire({ icon: 'success', title: 'Akses Diberikan', timer: 1000, showConfirmButton: false });
            }
        });
    }

    function forceSubmitExam(reason) {
        window.onbeforeunload = null;
        localStorage.removeItem('exam_end_time_' + mkId);
        localStorage.removeItem('exam_answers_' + mkId);
        localStorage.removeItem('exam_current_step_' + mkId);
        
        Swal.fire({
            title: 'Ujian Dihentikan',
            text: reason,
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            document.getElementById('timeout_flag').value = "true";
            document.getElementById('examForm').submit();
        });
    }

    function validateAndSubmit() {
        const form = document.getElementById('examForm');
        let savedAnswers = JSON.parse(localStorage.getItem('exam_answers_' + mkId)) || {};
        let unansweredIndex = -1;

        const steps = document.querySelectorAll('.question-step');
        for (let i = 0; i < steps.length; i++) {
            let sId = steps[i].getAttribute('data-soal-id');
            if (!savedAnswers[sId] || savedAnswers[sId].trim() === "") {
                unansweredIndex = i;
                break;
            }
        }

        if (unansweredIndex !== -1) {
            Swal.fire({
                title: 'Belum Selesai!',
                text: `Soal nomor ${unansweredIndex + 1} belum dijawab. Harap isi semua soal.`,
                icon: 'warning',
                confirmButtonText: 'Ke Soal Tersebut',
                confirmButtonColor: '#3498db'
            }).then((result) => {
                if (result.isConfirmed) {
                    changeStep(unansweredIndex);
                }
            });
        } else {
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: "Pastikan semua jawaban sudah benar!",
                icon: 'question',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onbeforeunload = null;
                    localStorage.removeItem('exam_end_time_' + mkId);
                    localStorage.removeItem('exam_answers_' + mkId);
                    localStorage.removeItem('exam_current_step_' + mkId);
                    form.submit();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('examForm');
        if (!form) return;

        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'u' || e.key === 's') || e.key === 'F12') {
                e.preventDefault();
            }
        });

        let lastStep = localStorage.getItem('exam_current_step_' + mkId);
        if (lastStep !== null) {
            let targetStep = parseInt(lastStep);
            if(targetStep < totalSoal) {
                changeStep(targetStep);
            }
        }

        let savedAnswers = JSON.parse(localStorage.getItem('exam_answers_' + mkId)) || {};
        for (const [soalId, nilai] of Object.entries(savedAnswers)) {
            let radio = document.querySelector(`input[name="jawaban[${soalId}]"][value="${nilai}"]`);
            if (radio) radio.checked = true;
            let area = document.querySelector(`textarea[name="jawaban[${soalId}]"]`);
            if (area) area.value = nilai;
        }

        let durationInSeconds = <?php echo $durasi_detik; ?>;
        let examEndTime = localStorage.getItem('exam_end_time_' + mkId);

        if (!examEndTime) {
            examEndTime = Math.floor(Date.now() / 1000) + durationInSeconds;
            localStorage.setItem('exam_end_time_' + mkId, examEndTime);
        }

        const timerInterval = setInterval(function() {
            let now = Math.floor(Date.now() / 1000);
            let timeLeft = examEndTime - now;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                forceSubmitExam("Waktu ujian telah habis!");
            } else {
                let hours = Math.floor(timeLeft / 3600);
                let minutes = Math.floor((timeLeft % 3600) / 60);
                let seconds = timeLeft % 60;
                timerDisplay.innerText = 
                    String(hours).padStart(2, '0') + ":" + 
                    String(minutes).padStart(2, '0') + ":" + 
                    String(seconds).padStart(2, '0');
            }
        }, 1000);
        window.onbeforeunload = () => "Ujian sedang berlangsung!";
    });
</script>
</body>
</html>