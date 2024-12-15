// Modal Edit
const editModal = document.getElementById("editModal");
const closeEditModal = document.querySelector(".close-edit");

// Fungsi Buka Modal Edit
function openEditModal(videoId, judul, deskripsi) {
    document.getElementById("video_id").value = videoId;
    document.getElementById("edit_judul").value = judul;
    document.getElementById("edit_deskripsi").value = deskripsi;
    editModal.style.display = "block";
}

// Tutup Modal Edit
closeEditModal.onclick = function () {
    editModal.style.display = "none";
};

window.onclick = function (event) {
    if (event.target === editModal) {
        editModal.style.display = "none";
    }
};

// Modal Video
const videoModal = document.getElementById("videoModal");
const closeVideoModalBtn = document.querySelector(".close-video");

// Fungsi untuk membuka modal video
function openVideoModal(videoPath) {
    const videoSource = document.getElementById("modalVideoSource");
    const videoElement = document.getElementById("modalVideo");

    videoSource.src = videoPath;
    videoElement.load(); // Muat ulang video
    videoModal.style.display = "flex";
}

// Fungsi untuk menutup modal video
function closeVideoModal() {
    const videoElement = document.getElementById("modalVideo");

    videoModal.style.display = "none";
    videoElement.pause(); // Hentikan video saat modal ditutup
}

// Tutup Modal Video
closeVideoModalBtn.onclick = function () {
    closeVideoModal();
};

window.onclick = function (event) {
    if (event.target === videoModal) {
        closeVideoModal();
    } else if (event.target === editModal) {
        editModal.style.display = "none";
    }
};
